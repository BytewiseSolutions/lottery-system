<?php

class AnalyticsController
{
    private $analyticsService;

    public function __construct()
    {
        $this->analyticsService = new AnalyticsService();
    }

    public function getStats()
    {
        try {
            $result = $this->analyticsService->getStats();

            if ($result['success']) {
                Response::json(true, 'Analytics stats fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('AnalyticsController stats error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch analytics stats', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getStatistics()
    {
        $this->getStats();
    }
}
