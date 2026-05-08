<?php

class AnalyticsService
{
    private $analyticsRepository;

    public function __construct()
    {
        $this->analyticsRepository = new AnalyticsRepository();
    }

    public function getStats()
    {
        try {
            $stats = $this->analyticsRepository->fetchStats();

            return [
                'success' => true,
                'data' => $stats->toArray()
            ];

        } catch (Exception $e) {
            Logger::error('Get analytics stats failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load analytics stats'
            ];
        }
    }

    public function getEntryTrends($period = 'last_7_days')
    {
        try {
            $trends = $this->analyticsRepository->fetchEntryTrends($period);

            return [
                'success' => true,
                'data' => $trends
            ];

        } catch (Exception $e) {
            Logger::error('Get entry trends failed', [
                'error' => $e->getMessage(),
                'period' => $period
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load entry trends'
            ];
        }
    }

    public function getRevenueDistribution($period = 'last_7_days')
    {
        try {
            $distribution = $this->analyticsRepository->fetchRevenueDistribution($period);

            return [
                'success' => true,
                'data' => $distribution
            ];

        } catch (Exception $e) {
            Logger::error('Get revenue distribution failed', [
                'error' => $e->getMessage(),
                'period' => $period
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load revenue distribution'
            ];
        }
    }

    public function getPerformanceMetrics($period = 'last_7_days')
    {
        try {
            $metrics = $this->analyticsRepository->fetchPerformanceMetrics($period);

            return [
                'success' => true,
                'data' => $metrics
            ];

        } catch (Exception $e) {
            Logger::error('Get performance metrics failed', [
                'error' => $e->getMessage(),
                'period' => $period
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load performance metrics'
            ];
        }
    }
}
