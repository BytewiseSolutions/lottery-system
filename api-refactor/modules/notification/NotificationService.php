<?php

class NotificationService
{
    private $notificationRepository;

    public function __construct()
    {
        $this->notificationRepository = new NotificationRepository();
    }

    public function getUnreadCount($userId)
    {
        try {
            $count = $this->notificationRepository->getUnreadCount($userId);

            return [
                'success' => true,
                'data' => ['count' => $count]
            ];

        } catch (Exception $e) {
            Logger::error('Get unread notification count failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get unread notification count'
            ];
        }
    }

    public function getNotifications($userId, $page = 1, $limit = 10)
    {
        try {
            $notifications = $this->notificationRepository->getNotifications($userId, $page, $limit);

            return [
                'success' => true,
                'data' => $notifications
            ];

        } catch (Exception $e) {
            Logger::error('Get notifications failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get notifications'
            ];
        }
    }

    public function createNotification($createdBy, $data)
    {
        try {
            $title = $data['title'] ?? '';
            $message = $data['message'] ?? '';
            $type = $data['type'] ?? 'info';
            $recipientType = $data['recipient_type'] ?? 'all';

            $success = $this->notificationRepository->createNotification($createdBy, $title, $message, $type, $recipientType);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Notification created successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create notification'
                ];
            }

        } catch (Exception $e) {
            Logger::error('Create notification failed', [
                'error' => $e->getMessage(),
                'created_by' => $createdBy
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create notification'
            ];
        }
    }

    public function markAsRead($userId, $notificationId)
    {
        try {
            $success = $this->notificationRepository->markAsRead($userId, $notificationId);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Notification marked as read'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Notification not found or already read'
                ];
            }

        } catch (Exception $e) {
            Logger::error('Mark notification as read failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'notification_id' => $notificationId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ];
        }
    }
}