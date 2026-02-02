import { Component, OnInit } from '@angular/core';
import { LotteryService } from '../services/lottery.service';

@Component({
  selector: 'app-winnings',
  templateUrl: './winnings.page.html',
  styleUrls: ['./winnings.page.scss'],
  standalone: false,
})
export class WinningsPage implements OnInit {
  totalWinnings = 0;
  winnings: any[] = [];
  loading = true;

  constructor(private lottery: LotteryService) {}

  ngOnInit() {
    this.loadWinnings();
  }

  loadWinnings() {
    this.loading = true;
    this.lottery.getMyWinnings().subscribe({
      next: (data: any) => {
        this.totalWinnings = data.total_winnings || 0;
        this.winnings = Array.isArray(data.winnings) ? data.winnings : [];
        this.loading = false;
      },
      error: (err) => {
        console.error('Error loading winnings:', err);
        this.loading = false;
      }
    });
  }

  parseNumbers(numbersJson: any): number[] {
    if (Array.isArray(numbersJson)) return numbersJson;
    try {
      return JSON.parse(numbersJson);
    } catch {
      return [];
    }
  }

  getStatusColor(status: string): string {
    return status === 'paid' ? 'success' : status === 'failed' ? 'danger' : 'warning';
  }

  handleRefresh(event: any) {
    this.loadWinnings();
    setTimeout(() => {
      event.target.complete();
    }, 1000);
  }
}
