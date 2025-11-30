import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { LayoutComponent } from '../layout/layout.component';
import { LotteryService, Stats } from '../services/lottery.service';

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

  constructor(private lotteryService: LotteryService) {}

  ngOnInit() {
    this.lotteryService.getStats().subscribe(stats => {
      this.stats = stats;
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
