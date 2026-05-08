<?php

class ActivityLogController
{
    private $service;
    private $authRepository;
    private $userRepository;

    public function __construct()
    {
        $this->service = new ActivityLogService();
        $this->authRepository = new AuthRepository();
        $this->userRepository = new UserRepository();
    }

    public function getLogs()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$currentUser->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $limit = $_GET['limit'] ?? 50;

            $result = $this->service->getAll($limit);

            Response::json(
                $result['success'],
                $result['message'] ?? 'Logs fetched',
                $result['data'] ?? null,
                200
            );

        } catch (Exception $e) {
            Response::json(false, 'Failed to fetch logs', null, 500);
        }
    }

    public function getUserLogs()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$currentUser->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $userId = $_GET['user_id'] ?? null;

            if (!$userId) {
                Response::json(false, 'User ID required', null, 400);
                return;
            }

            $result = $this->service->getByUser($userId);

            Response::json(
                $result['success'],
                $result['message'] ?? 'User logs fetched',
                $result['data'] ?? null,
                200
            );

        } catch (Exception $e) {
            Response::json(false, 'Failed to fetch user logs', null, 500);
        }
    }

    private function getCurrentUser()
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? '';

        if (!$header && function_exists('getallheaders')) {
            $headers = getallheaders();
            $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (!preg_match('/Bearer\s+(.+)$/i', $header, $matches)) {
            return null;
        }

        $token = trim($matches[1]);
        $user = $this->authRepository->findUserByToken($token);

        if ($user) {
            return $user;
        }

        return $this->getUserFromLegacyJwt($token);
    }

    private function getUserFromLegacyJwt($token)
    {
        $jwtPath = dirname(__DIR__, 3) . '/api/config/jwt.php';

        if (!file_exists($jwtPath)) {
            return null;
        }

        require_once $jwtPath;

        if (!class_exists('JWT') || !method_exists('JWT', 'decode')) {
            return null;
        }

        $payload = JWT::decode($token);

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
