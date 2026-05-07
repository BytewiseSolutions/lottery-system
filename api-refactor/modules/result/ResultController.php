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

    public function createResult()
    {
        try {
            $resultDto = ResultDto::fromRequest();
            $result = $this->resultService->createResult($resultDto);

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

    public function updateResult()
    {
        try {
            $resultDto = ResultDto::fromRequest();
            $result = $this->resultService->updateResult($resultDto);

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
}
