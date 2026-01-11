import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-analytics',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './analytics.component.html',
  styleUrls: ['./analytics.component.scss']
})
export class AnalyticsComponent {
  @Input() analyticsRange = '30d';
  @Input() analyticsData: any = {};
  @Output() rangeChange = new EventEmitter<string>();
  @Output() customDateRange = new EventEmitter<void>();

  setAnalyticsRange(range: string) {
    this.analyticsRange = range;
    this.rangeChange.emit(range);
  }

  openDateRangePicker() {
    this.customDateRange.emit();
  }
}