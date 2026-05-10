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

    public function getEntryTrends()
    {
        try {
            $period = $_GET['period'] ?? 'last_7_days';
            $result = $this->analyticsService->getEntryTrends($period);

            if ($result['success']) {
                Response::json(true, 'Entry trends fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('AnalyticsController entry trends error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch entry trends', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getRevenueDistribution()
    {
        try {
            $period = $_GET['period'] ?? 'last_7_days';
            $result = $this->analyticsService->getRevenueDistribution($period);

            if ($result['success']) {
                Response::json(true, 'Revenue distribution fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('AnalyticsController revenue distribution error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch revenue distribution', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getPerformanceMetrics()
    {
        try {
            $period = $_GET['period'] ?? 'last_7_days';
            $result = $this->analyticsService->getPerformanceMetrics($period);

            if ($result['success']) {
                Response::json(true, 'Performance metrics fetched successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('AnalyticsController performance metrics error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch performance metrics', null, HTTP_INTERNAL_ERROR);
        }
    }
}
