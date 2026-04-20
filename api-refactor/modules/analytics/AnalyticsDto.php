<?php

class EntryTrendDto
{
    public $date;
    public $entries;
    public $revenue;

    public function __construct($data = [])
    {
        $this->date = $data['date'] ?? null;
        $this->entries = (int)($data['entries'] ?? 0);
        $this->revenue = (float)($data['revenue'] ?? 0);
    }

    public function toArray()
    {
        return [
            'date' => $this->date,
            'entries' => $this->entries,
            'revenue' => $this->revenue
        ];
    }
}

class RevenueDistributionDto
{
    public $entryFees;
    public $premium;
    public $other;
    public $total;

    public function __construct($data = [])
    {
        $this->entryFees = (float)($data['entryFees'] ?? 0);
        $this->premium = (float)($data['premium'] ?? 0);
        $this->other = (float)($data['other'] ?? 0);
        $this->total = (float)($data['total'] ?? 0);
    }

    public function toArray()
    {
        return [
            'entryFees' => $this->entryFees,
            'premium' => $this->premium,
            'other' => $this->other,
            'total' => $this->total
        ];
    }
}

class PerformanceMetricsDto
{
    public $conversionRate;
    public $conversionTrend;
    public $averageEntryValue;
    public $averageEntryTrend;
    public $returnRate;
    public $returnTrend;
    public $payoutRatio;
    public $payoutTrend;

    public function __construct($data = [])
    {
        $this->conversionRate = (float)($data['conversionRate'] ?? 0);
        $this->conversionTrend = (float)($data['conversionTrend'] ?? 0);
        $this->averageEntryValue = (float)($data['averageEntryValue'] ?? 0);
        $this->averageEntryTrend = (float)($data['averageEntryTrend'] ?? 0);
        $this->returnRate = (float)($data['returnRate'] ?? 0);
        $this->returnTrend = (float)($data['returnTrend'] ?? 0);
        $this->payoutRatio = (float)($data['payoutRatio'] ?? 0);
        $this->payoutTrend = (float)($data['payoutTrend'] ?? 0);
    }

    public function toArray()
    {
        return [
            'conversionRate' => $this->conversionRate,
            'conversionTrend' => $this->conversionTrend,
            'averageEntryValue' => $this->averageEntryValue,
            'averageEntryTrend' => $this->averageEntryTrend,
            'returnRate' => $this->returnRate,
            'returnTrend' => $this->returnTrend,
            'payoutRatio' => $this->payoutRatio,
            'payoutTrend' => $this->payoutTrend
        ];
    }
}

class AnalyticsDataDto
{
    public $entryTrends;
    public $revenueDistribution;
    public $performanceMetrics;

    public function __construct($data = [])
    {
        $this->entryTrends = $data['entryTrends'] ?? [];
        $this->revenueDistribution = isset($data['revenueDistribution']) 
            ? new RevenueDistributionDto($data['revenueDistribution']) 
            : new RevenueDistributionDto();
        $this->performanceMetrics = isset($data['performanceMetrics']) 
            ? new PerformanceMetricsDto($data['performanceMetrics']) 
            : new PerformanceMetricsDto();
    }

    public function toArray()
    {
        return [
            'entryTrends' => array_map(function($trend) {
                return is_array($trend) ? (new EntryTrendDto($trend))->toArray() : $trend->toArray();
            }, $this->entryTrends),
            'revenueDistribution' => $this->revenueDistribution->toArray(),
            'performanceMetrics' => $this->performanceMetrics->toArray()
        ];
    }
}