import { Component, OnInit } from '@angular/core';
import { LotteryService } from '../services/lottery.service';
import { ToastService } from '../services/toast.service';
import { ShareService } from '../services/share.service';

@Component({
  selector: 'app-past-draws',
  templateUrl: './past-draws.page.html',
  styleUrls: ['./past-draws.page.scss'],
  standalone: false,
})
export class PastDrawsPage implements OnInit {
  draws: any[] = [];
  filteredDraws: any[] = [];
  loading = true;
  selectedLottery = 'all';
  lotteryTypes: string[] = [];

  constructor(
    private lottery: LotteryService,
    private toast: ToastService,
    private share: ShareService
  ) {}

  ngOnInit() {
    this.loadPastDraws();
  }

  loadPastDraws() {
    this.loading = true;
    this.lottery.getPastDraws().subscribe({
      next: (data) => {
        this.draws = data;
        this.filteredDraws = data;
        const types = Array.from(new Set(data.map((d: any) => d.lottery))) as string[];
        this.lotteryTypes = ['all', ...types];
        this.loading = false;
      },
      error: () => {
        this.toast.showError('Failed to load past draws');
        this.loading = false;
      }
    });
  }

  filterByLottery() {
    this.filteredDraws = this.selectedLottery === 'all' 
      ? this.draws 
      : this.draws.filter(d => d.lottery === this.selectedLottery);
  }

  parseNumbers(nums: any): number[] {
    return Array.isArray(nums) ? nums : JSON.parse(nums || '[]');
  }

  async shareWinningNumbers(draw: any) {
    const numbers = this.parseNumbers(draw.numbers || draw.winning_numbers).join(', ');
    const bonus = this.parseNumbers(draw.bonusNumbers || draw.bonus_numbers).join(', ');
    const date = draw.drawDate || draw.draw_date;
    const text = `${draw.lottery} - ${new Date(date).toLocaleDateString()}\nWinning Numbers: ${numbers}\nBonus: ${bonus}\nJackpot: R${draw.jackpot}`;
    
    const success = await this.share.share(text, 'Lottery Results');
    if (success) {
      this.toast.showSuccess('Shared successfully');
    } else {
      this.toast.showError('Failed to share');
    }
  }
}
