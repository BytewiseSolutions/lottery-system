import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { LayoutComponent } from '../layout/layout.component';
import { BackendService } from '../util/backend.service';
import { ApiResponse } from '../util/api-response';

interface Stats {
  winnersLastMonth: number;
  totalEntries: number;
  totalPayouts: number;
}

@Component({
  selector: 'app-about',
  imports: [RouterLink, LayoutComponent, CommonModule],
  templateUrl: './about.component.html',
  styleUrl: './about.component.css'
})
export class AboutComponent implements OnInit {
  stats: Stats = {
    winnersLastMonth: 0,
    totalEntries: 0,
    totalPayouts: 0
  };

  constructor(private backendService: BackendService) {}

  ngOnInit() {
    this.backendService.getAnalytics().subscribe({
      next: (response: ApiResponse<Stats>) => {
        this.stats = response?.data ?? this.stats;
      },
      error: (error) => {
        console.error('Error loading stats:', error);
      }
    });
  }

  formatNumber(num: number): string {
    if (num >= 1000000) {
      return (num / 1000000).toFixed(1) + 'M';
    }
    return num.toLocaleString();
  }

  formatCurrency(num: number): string {
    if (num >= 1000000) {
      return '$' + (num / 1000000).toFixed(1) + 'M';
    }
    return '$' + num.toLocaleString();
  }
}
