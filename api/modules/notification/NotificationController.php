<?php

class NotificationController
{
    private $notificationService;
    private $authRepository;
    private $userRepository;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
        $this->authRepository = new AuthRepository();
        $this->userRepository = new UserRepository();
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

    public function getPublicNotifications()
    {
        try {
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = max(MIN_PAGE_SIZE, min(MAX_PAGE_SIZE, (int)($_GET['limit'] ?? 10)));

            $result = $this->notificationService->getPublicNotifications($page, $limit);

            if ($result['success']) {
                Response::json(true, 'Notifications retrieved successfully', $result['data'], HTTP_OK);
            } else {
                Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Logger::error('NotificationController get public notifications error', [
                'error' => $e->getMessage()
            ]);
            Response::json(false, 'Failed to get notifications', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getNotifications()
    {
        try {
            $userId = $this->getCurrentUserId();
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = max(MIN_PAGE_SIZE, min(MAX_PAGE_SIZE, (int)($_GET['limit'] ?? 10)));

            if (!$userId) {
                Response::json(false, 'Authentication required', null, HTTP_UNAUTHORIZED);
                return;
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
            $user = $this->getCurrentUser();
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$user) {
                Response::json(false, 'Authentication required', null, HTTP_UNAUTHORIZED);
            }

            if (!$user->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            if (!isset($input['title']) || !isset($input['message'])) {
                Response::json(false, 'Title and message are required', null, HTTP_BAD_REQUEST);
            }

            $result = $this->notificationService->createNotification($user->id, $input);
            
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

    public function markAllAsRead()
    {
        try {
            $userId = $this->getCurrentUserId();

            if (!$userId) {
                Response::json(false, 'Authentication required', null, HTTP_UNAUTHORIZED);
                return;
            }

            $this->notificationService->markAllAsRead($userId);
            Response::json(true, 'All notifications marked as read', null, HTTP_OK);

        } catch (Exception $e) {
            Logger::error('NotificationController markAllAsRead error', ['error' => $e->getMessage()]);
            Response::json(false, 'Failed to mark notifications as read', null, HTTP_INTERNAL_ERROR);
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
        $user = $this->getCurrentUser();

        return $user ? $user->id : null;
    }

    private function getCurrentUser()
    {
        $token = $this->extractBearerToken();

        if (!$token) {
            return null;
        }

        try {
            $user = $this->authRepository->findUserByToken($token);

            if ($user) {
                return $user;
            }

            return $this->getUserFromLegacyJwt($token);
        } catch (Exception $e) {
            Logger::error('NotificationController token validation failed', [
                'error' => $e->getMessage()
            ]);

            return null;
        }

        return null;
    }

    private function extractBearerToken()
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? '';

        if (!$authHeader && function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (!preg_match('/Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return null;
        }

        return trim($matches[1]);
    }

    private function getUserFromLegacyJwt($token)
    {
        $payload = LegacyJwt::decode($token);

        if (!$payload || !is_array($payload)) {
            return null;
        }

        if (isset($payload['exp']) && (int)$payload['exp'] < time()) {
            return null;
        }

        $userId = isset($payload['id']) ? (int)$payload['id'] : 0;

        if ($userId <= 0) {
            return null;
        }

        return $this->userRepository->findById($userId);
    }
}
