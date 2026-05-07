<?php

class WinnerService
{
    private $winnerRepository;

    public function __construct()
    {
        $this->winnerRepository = new WinnerRepository();
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

    public function markClaimed($winnerId)
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
