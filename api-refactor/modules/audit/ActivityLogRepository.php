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
        $sql = "SELECT al.*, u.first_name, u.last_name, u.email 
                FROM activity_log al
                LEFT JOIN user u ON al.user_id = u.id
                ORDER BY al.created_at DESC 
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $logs = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $logData = [
                'id' => $row['id'],
                'user_id' => $row['user_id'],
                'action' => $row['action'],
                'details' => $row['details'],
                'ip_address' => $row['ip_address'],
                'created_at' => $row['created_at'],
                'user_name' => $row['first_name'] && $row['last_name'] 
                    ? $row['first_name'] . ' ' . $row['last_name']
                    : ($row['email'] ?? 'System'),
            ];
            
            $logs[] = new ActivityLog($logData);
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