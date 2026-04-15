<?php
declare(strict_types=1);

namespace App\Domain\Voting;

use PDO;

final class LeadingNumbersSnapshotRepository
{
    public function __construct(private readonly PDO $db)
    {
        $this->ensureTableExists();
    }

    public function find(string $lottery, string $drawDate): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT lottery, draw_date, top_five, top_two, section1_data, section2_data,
                    total_user_votes, total_admin_allocations, total_main_votes, total_bonus_votes,
                    created_at, updated_at
             FROM leading_numbers_snapshot
             WHERE lottery = ?
               AND draw_date = ?
             LIMIT 1'
        );
        $stmt->execute([$lottery, $drawDate]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRow($row) : null;
    }

    public function latestForLottery(string $lottery): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT lottery, draw_date, top_five, top_two, section1_data, section2_data,
                    total_user_votes, total_admin_allocations, total_main_votes, total_bonus_votes,
                    created_at, updated_at
             FROM leading_numbers_snapshot
             WHERE lottery = ?
             ORDER BY draw_date DESC, updated_at DESC
             LIMIT 1'
        );
        $stmt->execute([$lottery]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRow($row) : null;
    }

    public function upsert(array $summary): void
    {
        $driver = (string) $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $stmt = $this->db->prepare(
                'INSERT INTO leading_numbers_snapshot (
                    lottery, draw_date, top_five, top_two, section1_data, section2_data,
                    total_user_votes, total_admin_allocations, total_main_votes, total_bonus_votes,
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                ON CONFLICT(lottery, draw_date) DO UPDATE SET
                    top_five = excluded.top_five,
                    top_two = excluded.top_two,
                    section1_data = excluded.section1_data,
                    section2_data = excluded.section2_data,
                    total_user_votes = excluded.total_user_votes,
                    total_admin_allocations = excluded.total_admin_allocations,
                    total_main_votes = excluded.total_main_votes,
                    total_bonus_votes = excluded.total_bonus_votes,
                    updated_at = CURRENT_TIMESTAMP'
            );
        } else {
            $stmt = $this->db->prepare(
                'INSERT INTO leading_numbers_snapshot (
                    lottery, draw_date, top_five, top_two, section1_data, section2_data,
                    total_user_votes, total_admin_allocations, total_main_votes, total_bonus_votes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    top_five = VALUES(top_five),
                    top_two = VALUES(top_two),
                    section1_data = VALUES(section1_data),
                    section2_data = VALUES(section2_data),
                    total_user_votes = VALUES(total_user_votes),
                    total_admin_allocations = VALUES(total_admin_allocations),
                    total_main_votes = VALUES(total_main_votes),
                    total_bonus_votes = VALUES(total_bonus_votes),
                    updated_at = CURRENT_TIMESTAMP'
            );
        }

        $stmt->execute([
            $summary['lottery'],
            $summary['draw_date'],
            json_encode($summary['top_five']),
            json_encode($summary['top_two']),
            json_encode($summary['section1']),
            json_encode($summary['section2']),
            $summary['total_user_votes'],
            $summary['total_admin_allocations'],
            $summary['total_main_votes'],
            $summary['total_bonus_votes'],
        ]);
    }

    private function mapRow(array $row): array
    {
        return [
            'lottery' => (string) $row['lottery'],
            'draw_date' => substr((string) $row['draw_date'], 0, 10),
            'section1' => $this->decodeItems($row['section1_data'] ?? null),
            'section2' => $this->decodeItems($row['section2_data'] ?? null),
            'topSection1' => $this->decodeNumbers($row['top_five'] ?? null),
            'topSection2' => $this->decodeNumbers($row['top_two'] ?? null),
            'top_five' => $this->decodeNumbers($row['top_five'] ?? null),
            'top_two' => $this->decodeNumbers($row['top_two'] ?? null),
            'total_user_votes' => (int) ($row['total_user_votes'] ?? 0),
            'total_admin_allocations' => (int) ($row['total_admin_allocations'] ?? 0),
            'total_main_votes' => (int) ($row['total_main_votes'] ?? 0),
            'total_bonus_votes' => (int) ($row['total_bonus_votes'] ?? 0),
            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
            'snapshot_updated_at' => $row['updated_at'] ?? null,
            'from_snapshot' => true,
        ];
    }

    private function decodeNumbers(?string $value): array
    {
        $decoded = json_decode((string) $value, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_map('intval', $decoded));
    }

    private function decodeItems(?string $value): array
    {
        $decoded = json_decode((string) $value, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, static fn ($item): bool => is_array($item)));
    }

    private function ensureTableExists(): void
    {
        $driver = (string) $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $this->db->exec(
                'CREATE TABLE IF NOT EXISTS leading_numbers_snapshot (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    lottery TEXT NOT NULL,
                    draw_date TEXT NOT NULL,
                    top_five TEXT NOT NULL,
                    top_two TEXT NOT NULL,
                    section1_data TEXT NOT NULL,
                    section2_data TEXT NOT NULL,
                    total_user_votes INTEGER NOT NULL DEFAULT 0,
                    total_admin_allocations INTEGER NOT NULL DEFAULT 0,
                    total_main_votes INTEGER NOT NULL DEFAULT 0,
                    total_bonus_votes INTEGER NOT NULL DEFAULT 0,
                    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(lottery, draw_date)
                )'
            );

            return;
        }

        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS leading_numbers_snapshot (
                id INT AUTO_INCREMENT PRIMARY KEY,
                lottery VARCHAR(100) NOT NULL,
                draw_date DATE NOT NULL,
                top_five TEXT NOT NULL,
                top_two TEXT NOT NULL,
                section1_data LONGTEXT NOT NULL,
                section2_data LONGTEXT NOT NULL,
                total_user_votes INT NOT NULL DEFAULT 0,
                total_admin_allocations INT NOT NULL DEFAULT 0,
                total_main_votes INT NOT NULL DEFAULT 0,
                total_bonus_votes INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_leading_numbers_snapshot (lottery, draw_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }
}
