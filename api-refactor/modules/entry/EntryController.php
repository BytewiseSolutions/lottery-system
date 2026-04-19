<?php

class EntryController
{
    private $entryService;
    private $authRepository;

    public function __construct()
    {
        $this->entryService = new EntryService();
        $this->authRepository = new AuthRepository();
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

    private function getAuthenticatedUser()
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? '';

        if (!$header && function_exists('getallheaders')) {
            $headers = getallheaders();
            $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (!preg_match('/Bearer\s+(.+)$/i', $header, $matches)) {
            return null;
        }

        return $this->authRepository->findUserByToken(trim($matches[1]));
    }
}
