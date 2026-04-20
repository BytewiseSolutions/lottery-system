import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BackendService } from '../../../util/backend.service';
import { forkJoin } from 'rxjs';
import { AnalyticsData } from './analytics';

@Component({
  selector: 'app-analytics',
  imports: [CommonModule],
  templateUrl: './analytics.component.html',
  styleUrl: './analytics.component.css'
})
export class AnalyticsComponent implements OnInit {
  analyticsData: AnalyticsData = {
    entryTrends: [],
    revenueDistribution: {
      entryFees: 0,
      premium: 0,
      other: 0,
      total: 0
    },
    performanceMetrics: {
      conversionRate: 0,
      conversionTrend: 0,
      averageEntryValue: 0,
      averageEntryTrend: 0,
      returnRate: 0,
      returnTrend: 0,
      payoutRatio: 0,
      payoutTrend: 0
    }
  };

  selectedPeriod = 'last_7_days';
  loading = true;
  error: string | null = null;

  constructor(private backendService: BackendService) {}

  ngOnInit() {
    this.loadAnalyticsData();
  }

  loadAnalyticsData() {
    this.loading = true;
    this.error = null;

    forkJoin({
      entryTrends: this.backendService.getEntryTrends(this.selectedPeriod),
      revenueDistribution: this.backendService.getRevenueDistribution(this.selectedPeriod),
      performanceMetrics: this.backendService.getPerformanceMetrics(this.selectedPeriod)
    }).subscribe({
      next: (responses) => {
        if (responses.entryTrends.success) {
          this.analyticsData.entryTrends = responses.entryTrends.data;
        }
        if (responses.revenueDistribution.success) {
          this.analyticsData.revenueDistribution = responses.revenueDistribution.data;
        }
        if (responses.performanceMetrics.success) {
          this.analyticsData.performanceMetrics = responses.performanceMetrics.data;
        }
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Failed to load analytics data';
        this.loading = false;
        console.error('Analytics error:', err);
      }
    });
  }

  onPeriodChange(event: any) {
    this.selectedPeriod = event.target.value;
    this.loadAnalyticsData();
  }

  formatCurrency(amount: number): string {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(amount);
  }

  formatPercentage(value: number): string {
    return `${value.toFixed(1)}%`;
  }

  getTrendClass(trend: number): string {
    if (trend > 0) return 'positive';
    if (trend < 0) return 'negative';
    return 'neutral';
  }

  getTrendSymbol(trend: number): string {
    if (trend > 0) return '+';
    if (trend < 0) return '';
    return '';
  }

  getMaxEntries(): number {
    if (this.analyticsData.entryTrends.length === 0) return 1;
    return Math.max(...this.analyticsData.entryTrends.map(trend => trend.entries));
  }
}
