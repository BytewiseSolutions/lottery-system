<?php

class ResultController
{
    private $resultService;
    private $authRepository;
    private $userRepository;

    public function __construct()
    {
        $this->resultService = new ResultService();
        $this->authRepository = new AuthRepository();
        $this->userRepository = new UserRepository();
    }

    public function getLatestResults()
    {
        try {
            $result = $this->resultService->getLatestResults();

            if ($result['success']) {
                Response::json(true, 'Latest results fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('ResultController latest results error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch latest results', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getResults()
    {
        try {
            $result = $this->resultService->getResults();

            if ($result['success']) {
                Response::json(true, 'Results fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('ResultController results error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch results', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getResultById()
    {
        try {
            $resultId = $_GET['id'] ?? null;
            $result = $this->resultService->getResultById($resultId);

            if ($result['success']) {
                Response::json(true, 'Result fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('ResultController result details error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch result details', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function createResult()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$currentUser->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $resultDto = ResultDto::fromRequest();
            $result = $this->resultService->createResult($resultDto, $currentUser);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_CREATED);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('ResultController create result error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to create result', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function autoPublishResults()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$currentUser->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $result = $this->resultService->autoPublishDueResults();

            if ($result['success']) {
                Response::json(true, 'Auto-publish complete', $result['data'] ?? null, HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('ResultController auto-publish error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to auto-publish results', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function updateResult()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$currentUser->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $resultDto = ResultDto::fromRequest();
            $result = $this->resultService->updateResult($resultDto, $currentUser);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('ResultController update result error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to update result', null, HTTP_INTERNAL_ERROR);
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
