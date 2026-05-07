<?php

class EntryController
{
    private $entryService;
    private $authRepository;
    private $userRepository;

    public function __construct()
    {
        $this->entryService = new EntryService();
        $this->authRepository = new AuthRepository();
        $this->userRepository = new UserRepository();
    }

    public function submitEntry()
    {
        try {
            $user = $this->getAuthenticatedUser();

            if (!$user) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            $entryDto = EntryDto::fromRequest();
            $result = $this->entryService->submitEntry($user, $entryDto);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_CREATED);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('EntryController submit entry error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to submit entry', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getEntryHistory()
    {
        try {
            $user = $this->getAuthenticatedUser();

            if (!$user) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            $result = $this->entryService->getEntryHistory($user->id);

            if ($result['success']) {
                Response::json(true, 'Entry history fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('EntryController entry history error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch entry history', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getEntriesByDraw()
    {
        try {
            $drawId = isset($_GET['draw_id']) ? (int)$_GET['draw_id'] : null;

            if (!$drawId) {
                Response::json(false, 'Draw ID required', null, HTTP_BAD_REQUEST);
            }

            $result = $this->entryService->getEntriesByDraw($drawId);

            if ($result['success']) {
                Response::json(true, 'Draw entries fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('EntryController draw entries error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch draw entries', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getAllEntries()
    {
        try {
            $user = $this->getCurrentUser();

            if (!$user) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$user->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $result = $this->entryService->getAllEntries();

            if ($result['success']) {
                Response::json(true, 'Entries fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('EntryController all entries error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch entries', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getEntryDetails()
    {
        try {
            $user = $this->getCurrentUser();

            if (!$user) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$user->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $entryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $result = $this->entryService->getEntryById($entryId);

            if ($result['success']) {
                Response::json(true, 'Entry fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('EntryController entry details error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch entry details', null, HTTP_INTERNAL_ERROR);
        }
    }

    private function getAuthenticatedUser()
    {
        $user = $this->getCurrentUser();

        return $user;
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
