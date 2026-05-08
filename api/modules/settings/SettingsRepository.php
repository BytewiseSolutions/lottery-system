<?php

class SettingsRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
        $this->ensureTable();
    }

    public function getAll()
    {
        $stmt = $this->pdo->prepare('SELECT setting_key, setting_value FROM system_setting ORDER BY setting_key ASC');
        $stmt->execute();

        $settings = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $settings;
    }

    public function upsertMany($settings, $updatedBy = null)
    {
        $sql = 'INSERT INTO system_setting (setting_key, setting_value, updated_by, updated_at)
                VALUES (:setting_key, :setting_value, :updated_by, NOW())
                ON DUPLICATE KEY UPDATE
                    setting_value = VALUES(setting_value),
                    updated_by = VALUES(updated_by),
                    updated_at = NOW()';

        $stmt = $this->pdo->prepare($sql);

        foreach ($settings as $key => $value) {
            $stmt->execute([
                ':setting_key' => $key,
                ':setting_value' => (string)$value,
                ':updated_by' => $updatedBy
            ]);
        }

        return true;
    }

    private function ensureTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS system_setting (
                    setting_key VARCHAR(100) PRIMARY KEY,
                    setting_value TEXT NULL,
                    updated_by INT NULL,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    CONSTRAINT fk_system_setting_updated_by
                        FOREIGN KEY (updated_by) REFERENCES user(id)
                        ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';

        $this->pdo->exec($sql);
    }
}
