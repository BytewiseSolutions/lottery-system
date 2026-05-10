<?php

class FileController
{
    private $fileService;
    private $authRepository;
    private $userRepository;

    public function __construct()
    {
        $this->fileService = new FileService();
        $this->authRepository = new AuthRepository();
        $this->userRepository = new UserRepository();
    }

    public function uploadFile()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Authentication required', null, HTTP_UNAUTHORIZED);
            }

            $file = $_FILES['profilePicture'] ?? $_FILES['file'] ?? null;
            $result = $this->fileService->uploadProfilePicture($currentUser, $file);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_CREATED);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            Logger::error('File upload controller error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to upload file', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getFile()
    {
        try {
            $fileId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $file = $this->fileService->getFileById($fileId);

            if (!$file) {
                http_response_code(404);
                exit;
            }

            header('Content-Type: ' . ($file['file_type'] ?? 'application/octet-stream'));
            header('Content-Length: ' . filesize($file['file_path']));
            header('Cache-Control: public, max-age=86400');
            readfile($file['file_path']);
            exit;
        } catch (Exception $e) {
            Logger::error('File get controller error', [
                'error' => $e->getMessage()
            ]);

            http_response_code(500);
            exit;
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
