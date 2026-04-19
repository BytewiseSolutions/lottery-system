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
}
