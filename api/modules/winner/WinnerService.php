<?php

class WinnerService
{
    private $winnerRepository;
    private $activityLogService;

    public function __construct()
    {
        $this->winnerRepository = new WinnerRepository();
        $this->activityLogService = new ActivityLogService();
    }

    public function getWinners($resultId = null)
    {
        try {
            $winners = $this->winnerRepository->getWinners($resultId);

            return [
                'success' => true,
                'data' => array_map(function ($winner) {
                    return $winner->toArray();
                }, $winners)
            ];

        } catch (Exception $e) {
            Logger::error('Get winners failed', [
                'error' => $e->getMessage(),
                'result_id' => $resultId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load winners'
            ];
        }
    }

    public function getWinnersPage($page = 1, $limit = 20, $filters = [])
    {
        try {
            $page = max(1, (int)$page);
            $limit = min(MAX_PAGE_SIZE, max(MIN_PAGE_SIZE, (int)$limit));

            $winners = $this->winnerRepository->getWinnersPage($page, $limit, $filters);
            $totalCount = $this->winnerRepository->countWinners($filters);
            $totalPages = $totalCount > 0 ? (int)ceil($totalCount / $limit) : 0;
            $stats = $this->winnerRepository->getWinnerStats($filters);

            return [
                'success' => true,
                'data' => array_map(function ($winner) {
                    return $winner->toArray();
                }, $winners),
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total_items' => $totalCount,
                    'total_pages' => $totalPages,
                    'has_next' => $page < $totalPages,
                    'has_prev' => $page > 1
                ],
                'stats' => $stats
            ];

        } catch (Exception $e) {
            Logger::error('Get paginated winners failed', [
                'error' => $e->getMessage(),
                'page' => $page,
                'limit' => $limit
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load winners'
            ];
        }
    }

    public function getUserWinnings($currentUser)
    {
        try {
            return [
                'success' => true,
                'data' => $this->winnerRepository->getUserWinnings($currentUser->id)
            ];
        } catch (Exception $e) {
            Logger::error('Get user winnings failed', [
                'error' => $e->getMessage(),
                'user_id' => $currentUser->id ?? null
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load winnings'
            ];
        }
    }

    public function markClaimed($winnerId, $currentUser = null)
    {
        if (!$winnerId) {
            return [
                'success' => false,
                'message' => 'Winner is required'
            ];
        }

        try {
            $winner = $this->winnerRepository->findById($winnerId);

            if (!$winner) {
                return [
                    'success' => false,
                    'message' => 'Winner not found'
                ];
            }

            if ($winner->claim_status === CLAIM_CLAIMED) {
                return [
                    'success' => true,
                    'message' => 'Winner is already marked as claimed'
                ];
            }

            $updated = $this->winnerRepository->updateClaimStatus($winnerId, CLAIM_CLAIMED);

            if ($updated && $currentUser) {
                $winnerName = trim((string)($winner->name ?? 'winner'));
                $this->activityLogService->log(
                    $currentUser->id,
                    ACTION_WINNER_CLAIM,
                    "Marked winner {$winnerName} as claimed"
                );
            }

            return [
                'success' => (bool)$updated,
                'message' => $updated ? 'Winner marked as claimed' : 'Failed to update winner'
            ];

        } catch (Exception $e) {
            Logger::error('Mark winner claimed failed', [
                'error' => $e->getMessage(),
                'winner_id' => $winnerId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to mark winner as claimed'
            ];
        }
    }
}
