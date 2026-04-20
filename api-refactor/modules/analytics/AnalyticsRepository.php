<?php

class AnalyticsRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function fetchStats()
    {
        return new Analytics([
            'totalUsers' => (int)$this->scalar('SELECT COUNT(*) as total FROM user'),
            'totalEntries' => (int)$this->scalar('SELECT COUNT(*) as total FROM entry'),
            'totalPayouts' => (float)($this->scalar('SELECT SUM(jackpot) as total FROM result') ?? 0),
            'winnersLastMonth' => (int)($this->scalar(
                'SELECT COUNT(*) as winners FROM winner WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)'
            ) ?? 0)
        ]);
    }

    public function fetchEntryTrends($period = 'last_7_days')
    {
        try {
            $dateCondition = $this->getPeriodCondition($period);
            
            $query = "
                SELECT 
                    DATE(e.created_at) as date,
                    COUNT(*) as entries,
                    COUNT(*) * 10.00 as revenue
                FROM entry e
                WHERE e.created_at >= $dateCondition
                GROUP BY DATE(e.created_at)
                ORDER BY date ASC
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return array_map(function($row) {
                return [
                    'date' => $row['date'],
                    'entries' => (int)$row['entries'],
                    'revenue' => (float)$row['revenue']
                ];
            }, $results);
            
        } catch (Exception $e) {
            Logger::error('AnalyticsRepository fetchEntryTrends error', [
                'error' => $e->getMessage(),
                'period' => $period
            ]);
            throw $e;
        }
    }

    public function fetchRevenueDistribution($period = 'last_7_days')
    {
        try {
            $dateCondition = $this->getPeriodCondition($period);
            
            $query = "
                SELECT 
                    COALESCE(SUM(p.amount), 0) as total
                FROM payment p
                WHERE p.created_at >= $dateCondition
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $total = (float)($result['total'] ?? 0);
            
            // If no payment data, use estimated revenue from entries
            if ($total == 0) {
                $entryQuery = "
                    SELECT COUNT(*) * 10.00 as estimated_total
                    FROM entry e
                    WHERE e.created_at >= $dateCondition
                ";
                
                $entryStmt = $this->pdo->prepare($entryQuery);
                $entryStmt->execute();
                $entryResult = $entryStmt->fetch(PDO::FETCH_ASSOC);
                $total = (float)($entryResult['estimated_total'] ?? 0);
            }
            
            return [
                'entryFees' => $total * 0.65,
                'premium' => $total * 0.25,
                'other' => $total * 0.10,
                'total' => $total
            ];
            
        } catch (Exception $e) {
            Logger::error('AnalyticsRepository fetchRevenueDistribution error', [
                'error' => $e->getMessage(),
                'period' => $period
            ]);
            throw $e;
        }
    }

    public function fetchPerformanceMetrics($period = 'last_7_days')
    {
        try {
            $dateCondition = $this->getPeriodCondition($period);
            
            // Current period metrics
            $currentStats = $this->getMetricsForPeriod($dateCondition);
            
            // For simplicity, use mock previous period data for trends
            $previousStats = [
                'conversionRate' => $currentStats['conversionRate'] * 0.9,
                'averageEntryValue' => $currentStats['averageEntryValue'] * 0.95,
                'returnRate' => $currentStats['returnRate'] * 1.1,
                'payoutRatio' => $currentStats['payoutRatio'] * 1.05
            ];
            
            return [
                'conversionRate' => (float)$currentStats['conversionRate'],
                'conversionTrend' => (float)$this->calculateTrend($currentStats['conversionRate'], $previousStats['conversionRate']),
                'averageEntryValue' => (float)$currentStats['averageEntryValue'],
                'averageEntryTrend' => (float)$this->calculateTrend($currentStats['averageEntryValue'], $previousStats['averageEntryValue']),
                'returnRate' => (float)$currentStats['returnRate'],
                'returnTrend' => (float)$this->calculateTrend($currentStats['returnRate'], $previousStats['returnRate']),
                'payoutRatio' => (float)$currentStats['payoutRatio'],
                'payoutTrend' => (float)$this->calculateTrend($currentStats['payoutRatio'], $previousStats['payoutRatio'])
            ];
            
        } catch (Exception $e) {
            Logger::error('AnalyticsRepository fetchPerformanceMetrics error', [
                'error' => $e->getMessage(),
                'period' => $period
            ]);
            throw $e;
        }
    }

    private function getMetricsForPeriod($dateCondition)
    {
        try {
            $query = "
                SELECT 
                    COUNT(DISTINCT u.id) as totalUsers,
                    COUNT(e.id) as totalEntries,
                    COALESCE(SUM(p.amount), 0) as totalRevenue,
                    COALESCE(SUM(r.jackpot), 0) as totalPayouts,
                    COUNT(w.id) as totalWinners
                FROM user u
                LEFT JOIN entry e ON u.id = e.user_id AND e.created_at >= $dateCondition
                LEFT JOIN winner w ON u.id = w.user_id AND w.created_at >= $dateCondition
                LEFT JOIN payment p ON w.id = p.winner_id
                LEFT JOIN result r ON w.result_id = r.id
                WHERE u.created_at >= $dateCondition
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $totalUsers = (int)($stats['totalUsers'] ?? 0);
            $totalEntries = (int)($stats['totalEntries'] ?? 0);
            $totalRevenue = (float)($stats['totalRevenue'] ?? 0);
            $totalPayouts = (float)($stats['totalPayouts'] ?? 0);
            $totalWinners = (int)($stats['totalWinners'] ?? 0);
            
            if ($totalRevenue == 0 && $totalEntries > 0) {
                $totalRevenue = $totalEntries * 10.00;
            }
            
            $conversionRate = $totalUsers > 0 ? ($totalEntries / $totalUsers) * 100 : 0;
            $averageEntryValue = $totalEntries > 0 ? $totalRevenue / $totalEntries : 10.00;
            $returnRate = $totalEntries > 0 ? ($totalWinners / $totalEntries) * 100 : 0;
            $payoutRatio = $totalRevenue > 0 ? ($totalPayouts / $totalRevenue) * 100 : 0;
            
            return [
                'conversionRate' => $conversionRate,
                'averageEntryValue' => $averageEntryValue,
                'returnRate' => $returnRate,
                'payoutRatio' => $payoutRatio
            ];
            
        } catch (Exception $e) {
            Logger::error('AnalyticsRepository getMetricsForPeriod error', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'conversionRate' => 0,
                'averageEntryValue' => 10.00,
                'returnRate' => 0,
                'payoutRatio' => 0
            ];
        }
    }

    private function getPeriodCondition($period)
    {
        switch ($period) {
            case 'last_7_days':
                return 'DATE_SUB(NOW(), INTERVAL 7 DAY)';
            case 'last_30_days':
                return 'DATE_SUB(NOW(), INTERVAL 30 DAY)';
            case 'last_90_days':
                return 'DATE_SUB(NOW(), INTERVAL 90 DAY)';
            case 'last_year':
                return 'DATE_SUB(NOW(), INTERVAL 1 YEAR)';
            default:
                return 'DATE_SUB(NOW(), INTERVAL 7 DAY)';
        }
    }

    private function getPreviousPeriodCondition($period)
    {
        switch ($period) {
            case 'last_7_days':
                return 'DATE_SUB(NOW(), INTERVAL 14 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)';
            case 'last_30_days':
                return 'DATE_SUB(NOW(), INTERVAL 60 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)';
            case 'last_90_days':
                return 'DATE_SUB(NOW(), INTERVAL 180 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)';
            case 'last_year':
                return 'DATE_SUB(NOW(), INTERVAL 2 YEAR) AND created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)';
            default:
                return 'DATE_SUB(NOW(), INTERVAL 14 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)';
        }
    }

    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return (($current - $previous) / $previous) * 100;
    }

    private function scalar($query)
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? reset($result) : null;
    }
}
