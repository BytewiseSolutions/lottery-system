<?php

class NotificationController
{
    private $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    public function getUnreadCount()
    {
        try {
            $userId = $this->getCurrentUserId();
            
            if (!$userId) {
                Response::json(false, 'Authentication required', null, HTTP_UNAUTHORIZED);
            }

            $result = $this->notificationService->getUnreadCount($userId);
            
            if ($result['success']) {
                Response::json(true, 'Unread count retrieved successfully', $result['data'], HTTP_OK);
            } else {
                Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);
            }
            
        } catch (Exception $e) {
            Logger::error('NotificationController unread count error', [
                'error' => $e->getMessage()
            ]);
            Response::json(false, 'Failed to get unread count', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getNotifications()
    {
        try {
            $userId = $this->getCurrentUserId();
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            
            if (!$userId) {
                Response::json(false, 'Authentication required', null, HTTP_UNAUTHORIZED);
            }

            $result = $this->notificationService->getNotifications($userId, $page, $limit);
            
            if ($result['success']) {
                Response::json(true, 'Notifications retrieved successfully', $result['data'], HTTP_OK);
            } else {
                Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);
            }
            
        } catch (Exception $e) {
            Logger::error('NotificationController get notifications error', [
                'error' => $e->getMessage()
            ]);
            Response::json(false, 'Failed to get notifications', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function createNotification()
    {
        try {
            $userId = $this->getCurrentUserId();
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$userId) {
                Response::json(false, 'Authentication required', null, HTTP_UNAUTHORIZED);
            }

            if (!isset($input['title']) || !isset($input['message'])) {
                Response::json(false, 'Title and message are required', null, HTTP_BAD_REQUEST);
            }

            $result = $this->notificationService->createNotification($userId, $input);
            
            if ($result['success']) {
                Response::json(true, 'Notification created successfully', null, HTTP_OK);
            } else {
                Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);
            }
            
        } catch (Exception $e) {
            Logger::error('NotificationController create notification error', [
                'error' => $e->getMessage()
            ]);
            Response::json(false, 'Failed to create notification', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function markAsRead()
    {
        try {
            $userId = $this->getCurrentUserId();
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$userId) {
                Response::json(false, 'Authentication required', null, HTTP_UNAUTHORIZED);
            }

            if (!isset($input['id'])) {
                Response::json(false, 'Notification ID required', null, HTTP_BAD_REQUEST);
            }

            $result = $this->notificationService->markAsRead($userId, $input['id']);
            
            if ($result['success']) {
                Response::json(true, 'Notification marked as read', null, HTTP_OK);
            } else {
                Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);
            }
            
        } catch (Exception $e) {
            Logger::error('NotificationController mark as read error', [
                'error' => $e->getMessage()
            ]);
            Response::json(false, 'Failed to mark notification as read', null, HTTP_INTERNAL_ERROR);
        }
    }

    private function getCurrentUserId()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (strpos($authHeader, 'Bearer ') === 0) {
            $token = substr($authHeader, 7);
            
            try {
                // Use the AuthRepository to find user by token
                $authRepository = new AuthRepository();
                $user = $authRepository->findUserByToken($token);
                return $user ? $user->id : null;
            } catch (Exception $e) {
                Logger::error('Token validation failed', ['error' => $e->getMessage()]);
                return null;
            }
        }
        
        return null;
    }
}