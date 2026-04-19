<?php

class ResultService
{
    private $resultRepository;

    public function __construct()
    {
        $this->resultRepository = new ResultRepository();
    }

    public function getLatestResults($limit = 1)
    {
        try {
            $results = $this->resultRepository->getPublishedResults($limit);

            return [
                'success' => true,
                'data' => array_map(function ($result) {
                    return $result->toArray();
                }, $results)
            ];

        } catch (Exception $e) {
            Logger::error('Get latest results failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load latest results'
            ];
        }
    }

    public function getResults()
    {
        try {
            $results = $this->resultRepository->getPublishedResults();

            return [
                'success' => true,
                'data' => array_map(function ($result) {
                    return $result->toArray();
                }, $results)
            ];

        } catch (Exception $e) {
            Logger::error('Get results failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load results'
            ];
        }
    }
}
