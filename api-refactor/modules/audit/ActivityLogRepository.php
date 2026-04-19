<?php

class ActivityLogRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function create(ActivityLogDto $dto)
    {
        $sql = "INSERT INTO activity_log 
                (user_id, action, details, ip_address, created_at)
                VALUES (:user_id, :action, :details, :ip_address, NOW())";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':user_id'    => $dto->user_id,
            ':action'     => $dto->action,
            ':details'    => $dto->details,
            ':ip_address' => $dto->ip_address
        ]);
    }

    public function getAll($limit = 50)
    {
        $sql = "SELECT * FROM activity_log 
                ORDER BY created_at DESC 
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $logs = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $logs[] = new ActivityLog($row);
        }

        return $logs;
    }

    public function getByUserId($userId)
    {
        $sql = "SELECT * FROM activity_log 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        $logs = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $logs[] = new ActivityLog($row);
        }

        return $logs;
    }
}