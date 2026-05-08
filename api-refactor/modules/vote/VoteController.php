<?php

class VoteController
{
    private $voteService;
    private $authRepository;
    private $userRepository;

    public function __construct()
    {
        $this->voteService = new VoteService();
        $this->authRepository = new AuthRepository();
        $this->userRepository = new UserRepository();
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

    public function getAdminVotes()
    {
        try {
            $user = $this->getCurrentUser();

            if (!$user) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$user->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? DEFAULT_PAGE_SIZE;
            $filters = [
                'search' => trim((string)($_GET['search'] ?? '')),
                'lottery' => trim((string)($_GET['lottery'] ?? 'all')),
                'sort_order' => trim((string)($_GET['sort_order'] ?? 'newest')),
                'draw_id' => isset($_GET['draw_id']) ? (int)$_GET['draw_id'] : 0
            ];

            $result = $this->voteService->getAdminVotesPage($page, $limit, $filters);

            if ($result['success']) {
                Response::json(
                    true,
                    'Vote allocations fetched successfully',
                    $result['data'],
                    HTTP_OK,
                    [
                        'pagination' => $result['pagination'] ?? null,
                        'filters' => $result['filters'] ?? null,
                        'stats' => $result['stats'] ?? null
                    ]
                );
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('VoteController admin votes error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch vote allocations', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getAdminVoteDetails()
    {
        try {
            $user = $this->getCurrentUser();

            if (!$user) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$user->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $voteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $result = $this->voteService->getAdminVoteById($voteId);

            if ($result['success']) {
                Response::json(true, 'Vote allocation fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('VoteController admin vote details error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch vote allocation', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function createAdminVote()
    {
        try {
            $user = $this->getCurrentUser();

            if (!$user) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$user->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $result = $this->voteService->createAdminVote($user, $input);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_CREATED);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('VoteController create admin vote error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to create vote allocation', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function updateAdminVote()
    {
        try {
            $user = $this->getCurrentUser();

            if (!$user) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$user->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $voteId = isset($input['id']) ? (int)$input['id'] : 0;
            $result = $this->voteService->updateAdminVote($user, $voteId, $input);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('VoteController update admin vote error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to update vote allocation', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function deleteAdminVote()
    {
        try {
            $user = $this->getCurrentUser();

            if (!$user) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$user->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $voteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $result = $this->voteService->deleteAdminVote($user, $voteId);

            if ($result['success']) {
                Response::json(true, $result['message'], null, HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('VoteController delete admin vote error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to delete vote allocation', null, HTTP_INTERNAL_ERROR);
        }
    }

    private function getAuthenticatedUser()
    {
        return $this->getCurrentUser();
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
