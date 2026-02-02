import { Component, OnInit } from '@angular/core';
import { LotteryService } from '../../services/lottery.service';
import { ShareService } from '../../services/share.service';
import { ToastService } from '../../services/toast.service';

@Component({
  selector: 'app-results',
  templateUrl: './results.component.html',
  styleUrls: ['./results.component.scss'],
  standalone: false,
})
export class ResultsComponent  implements OnInit {
  results: any[] = [];

  constructor(
    private lottery: LotteryService,
    private share: ShareService,
    private toast: ToastService
  ) {}

  ngOnInit() {
    this.loadResults();
    window.addEventListener('refreshData', () => this.loadResults());
  }

  loadResults() {
    this.lottery.getResults().subscribe({
      next: (results: any) => this.results = Array.isArray(results) ? results : [],
      error: (err) => console.error('Error loading results:', err)
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

  async shareResult(result: any) {
    const numbers = this.parseNumbers(result.numbers).join(', ');
    const bonus = this.parseNumbers(result.bonusNumbers).join(', ');
    const text = `${result.lottery} - ${new Date(result.drawDate).toLocaleDateString()}\nWinning Numbers: ${numbers}\nBonus: ${bonus}\nJackpot: $${result.jackpot}M`;
    
    const success = await this.share.share(text, 'Lottery Results');
    if (success) {
      this.toast.showSuccess('Shared successfully');
    }
  }
}
