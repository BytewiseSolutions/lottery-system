<?php

class SettingsController
{
    private $settingsService;
    private $authRepository;
    private $userRepository;

    public function __construct()
    {
        $this->settingsService = new SettingsService();
        $this->authRepository = new AuthRepository();
        $this->userRepository = new UserRepository();
    }

    public function getSettings()
    {
        try {
            $result = $this->settingsService->getSettings();

            if ($result['success']) {
                Response::json(true, 'Settings retrieved successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Logger::error('SettingsController get settings error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to retrieve settings', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function updateSettings()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$currentUser->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $result = $this->settingsService->updateSettings($currentUser, $input);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_OK);
            }

            $statusCode = isset($result['errors']) ? HTTP_UNPROCESSABLE_ENTITY : HTTP_BAD_REQUEST;
            $data = isset($result['errors']) ? ['errors' => $result['errors']] : null;
            Response::json(false, $result['message'], $data, $statusCode);
        } catch (Exception $e) {
            Logger::error('SettingsController update settings error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to update settings', null, HTTP_INTERNAL_ERROR);
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
