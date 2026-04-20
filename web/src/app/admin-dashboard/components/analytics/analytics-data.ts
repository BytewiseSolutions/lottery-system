import { EntryTrend } from "./entry-trend";
import { PerformanceMetrics } from "./performance-metrics";
import { RevenueDistribution } from "./revenue-distribution";

export interface AnalyticsData {
  entryTrends: EntryTrend[];
  revenueDistribution: RevenueDistribution;
  performanceMetrics: PerformanceMetrics;
}