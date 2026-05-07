<?php

class WinnerController
{
    private $winnerService;

    public function __construct()
    {
        $this->winnerService = new WinnerService();
    }

    public function getWinners()
    {
        try {
            $resultId = isset($_GET['result_id']) ? (int)$_GET['result_id'] : null;
            $result = $this->winnerService->getWinners($resultId);

            if ($result['success']) {
                Response::json(true, 'Winners fetched successfully', $result['data'], HTTP_OK);
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
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $winnerId = isset($input['winner_id']) ? (int)$input['winner_id'] : null;
            $result = $this->winnerService->markClaimed($winnerId);

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
}
