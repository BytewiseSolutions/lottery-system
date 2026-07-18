<?php

class NotificationRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function getPublicNotifications($page = 1, $limit = 10)
    {
        try {
            $offset = ($page - 1) * $limit;

            $stmt = $this->pdo->prepare('
                SELECT MIN(id) as id, title, message, type, MIN(created_at) as created_at
                FROM notification
                WHERE sent_by IS NOT NULL
                GROUP BY title, message, type
                ORDER BY MIN(created_at) DESC
                LIMIT :limit OFFSET :offset
            ');

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            Logger::error('NotificationRepository getPublicNotifications error', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    public function getUnreadCount($userId)
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT COUNT(*) as count 
                FROM notification 
                WHERE user_id = :user_id AND is_read = 0
            ');
            
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int)($result['count'] ?? 0);
            
        } catch (Exception $e) {
            Logger::error('NotificationRepository getUnreadCount error', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            
            return 0;
        }
    }

    public function getNotifications($userId, $page = 1, $limit = 10)
    {
        try {
            $offset = ($page - 1) * $limit;
            
            $stmt = $this->pdo->prepare('
                SELECT 
                    id,
                    title,
                    message,
                    type,
                    is_read,
                    created_at
                FROM notification 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset
            ');
            
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            Logger::error('NotificationRepository getNotifications error', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            
            return [];
        }
    }

    public function createNotification($createdBy, $title, $message, $type, $recipientType)
    {
        try {
            // Get target users based on recipient type
            $userIds = $this->getTargetUsers($recipientType);
            
            if (empty($userIds)) {
                return false;
            }

            // Insert notification for each target user
            $stmt = $this->pdo->prepare('
                INSERT INTO notification (user_id, title, message, type, is_read, created_at, sent_by)
                VALUES (?, ?, ?, ?, 0, NOW(), ?)
            ');

            $success = true;
            foreach ($userIds as $userId) {
                $result = $stmt->execute([$userId, $title, $message, $type, $createdBy]);
                if (!$result) {
                    $success = false;
                }
            }

            return $success;
            
        } catch (Exception $e) {
            Logger::error('NotificationRepository createNotification error', [
                'error' => $e->getMessage(),
                'created_by' => $createdBy
            ]);
            
            return false;
        }
    }

    private function getTargetUsers($recipientType)
    {
        try {
            switch ($recipientType) {
                case 'admins':
                    $stmt = $this->pdo->prepare('SELECT id FROM user WHERE role = ?');
                    $stmt->execute([ROLE_ADMIN]);
                    break;
                case 'users':
                    $stmt = $this->pdo->prepare('SELECT id FROM user WHERE role = ?');
                    $stmt->execute([ROLE_USER]);
                    break;
                default: // 'all'
                    $stmt = $this->pdo->prepare('SELECT id FROM user WHERE is_active = 1');
                    $stmt->execute();
                    break;
            }
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (Exception $e) {
            Logger::error('NotificationRepository getTargetUsers error', [
                'error' => $e->getMessage(),
                'recipient_type' => $recipientType
            ]);
            
            return [];
        }
    }

    public function markAllAsRead($userId)
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE notification SET is_read = 1 WHERE user_id = :user_id AND is_read = 0');
            $stmt->execute([':user_id' => $userId]);
        } catch (Exception $e) {
            Logger::error('NotificationRepository markAllAsRead error', ['error' => $e->getMessage()]);
        }
    }

    public function markAsRead($userId, $notificationId)
    {
        try {
            $stmt = $this->pdo->prepare('
                UPDATE notification 
                SET is_read = 1 
                WHERE id = :id AND user_id = :user_id AND is_read = 0
            ');
            
            $stmt->execute([
                ':id' => $notificationId,
                ':user_id' => $userId
            ]);
            
            return $stmt->rowCount() > 0;
            
        } catch (Exception $e) {
            Logger::error('NotificationRepository markAsRead error', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'notification_id' => $notificationId
            ]);
            
            return false;
        }
    }
}