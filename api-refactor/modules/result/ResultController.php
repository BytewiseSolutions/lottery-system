<?php

class ResultController
{
    private $resultService;

    public function __construct()
    {
        $this->resultService = new ResultService();
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
}
