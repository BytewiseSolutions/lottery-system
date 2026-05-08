<?php

class WinnerController
{
    private $winnerService;
    private $authRepository;
    private $userRepository;

    public function __construct()
    {
        $this->winnerService = new WinnerService();
        $this->authRepository = new AuthRepository();
        $this->userRepository = new UserRepository();
    }

    public function getWinners()
    {
        try {
            $resultId = isset($_GET['result_id']) ? (int)$_GET['result_id'] : null;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : null;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
            $claimStatus = trim((string)($_GET['claim_status'] ?? 'all'));
            $paymentStatus = trim((string)($_GET['payment_status'] ?? 'all'));
            $sortOrder = trim((string)($_GET['sort_order'] ?? 'newest'));

            if ($page !== null || $limit !== null || $claimStatus !== 'all' || $paymentStatus !== 'all' || $sortOrder !== 'newest') {
                $result = $this->winnerService->getWinnersPage(
                    $page ?? 1,
                    $limit ?? DEFAULT_PAGE_SIZE,
                    [
                        'result_id' => $resultId,
                        'claim_status' => $claimStatus,
                        'payment_status' => $paymentStatus,
                        'sort_order' => $sortOrder
                    ]
                );

                if ($result['success']) {
                    Response::json(
                        true,
                        'Winners fetched successfully',
                        $result['data'],
                        HTTP_OK,
                        [
                            'pagination' => $result['pagination'] ?? null,
                            'stats' => $result['stats'] ?? null
                        ]
                    );
                }
            } else {
                $result = $this->winnerService->getWinners($resultId);

                if ($result['success']) {
                    Response::json(true, 'Winners fetched successfully', $result['data'], HTTP_OK);
                }
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('WinnerController winners error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch winners', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function markClaimed()
    {
        try {
            $currentUser = $this->getCurrentUser();
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $winnerId = isset($input['winner_id']) ? (int)$input['winner_id'] : null;
            $result = $this->winnerService->markClaimed($winnerId, $currentUser);

            if ($result['success']) {
                Response::json(true, $result['message'], null, HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('WinnerController claim error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to claim winner', null, HTTP_INTERNAL_ERROR);
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
