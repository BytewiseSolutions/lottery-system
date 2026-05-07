<?php

class VoteController
{
    private $voteService;
    private $authRepository;

    public function __construct()
    {
        $this->voteService = new VoteService();
        $this->authRepository = new AuthRepository();
    }

    public function submitVote()
    {
        try {
            $user = $this->getAuthenticatedUser();

            if (!$user) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            $voteDto = VoteDto::fromRequest();
            $result = $this->voteService->submitVote($user, $voteDto);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_CREATED);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('VoteController submit vote error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to submit entry', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getVoteHistory()
    {
        try {
            $user = $this->getAuthenticatedUser();

            if (!$user) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            $result = $this->voteService->getVoteHistory($user->id);

            if ($result['success']) {
                Response::json(true, 'Vote history fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('VoteController vote history error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch vote history', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getQuickPick()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $type = $input['type'] ?? 'main';
            $excludeNumbers = is_array($input['excludeNumbers'] ?? null) ? $input['excludeNumbers'] : [];
            $result = $this->voteService->getQuickPick($type, $excludeNumbers);

            if ($result['success']) {
                Response::json(true, 'Quick pick generated successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('VoteController quick pick error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to generate quick pick numbers', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getLeadingNumbers()
    {
        try {
            $lottery = $_GET['lottery'] ?? null;
            $drawDate = $_GET['voteDate'] ?? $_GET['drawDate'] ?? null;
            $result = $this->voteService->getLeadingNumbers($lottery, $drawDate);

            if ($result['success']) {
                Response::json(true, 'Leading numbers fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('VoteController leading numbers error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch leading numbers', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getHighestVoteForDraw()
    {
        try {
            $drawId = $_GET['draw_id'] ?? null;
            $result = $this->voteService->getHighestVoteForDraw($drawId);

            if ($result['success']) {
                Response::json(true, 'Highest vote fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('VoteController highest vote error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch highest vote', null, HTTP_INTERNAL_ERROR);
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
