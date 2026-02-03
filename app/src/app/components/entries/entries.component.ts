import { Component, OnInit } from '@angular/core';
import { LotteryService } from '../../services/lottery.service';
import { ToastService } from '../../services/toast.service';

@Component({
  selector: 'app-entries',
  templateUrl: './entries.component.html',
  styleUrls: ['./entries.component.scss'],
  standalone: false,
})
export class EntriesComponent  implements OnInit {
  entries: any[] = [];
  filteredEntries: any[] = [];
  results: any[] = [];
  loading = true;
  error = false;
  selectedLottery = 'all';
  lotteryTypes: string[] = [];
  entryLimit: any = { limit: 10, used: 0, remaining: 10 };

  constructor(private lottery: LotteryService, private toast: ToastService) {}

  ngOnInit() {
    this.loadEntries();
    this.loadResults();
    this.loadEntryLimit();
    window.addEventListener('entrySubmitted', () => {
      this.loadEntries();
      this.loadEntryLimit();
    });
    window.addEventListener('refreshData', () => {
      this.loadEntries();
      this.loadResults();
      this.loadEntryLimit();
    });
  }

  loadEntries() {
    this.loading = true;
    this.error = false;
    this.lottery.getMyEntries().subscribe({
      next: (entries: any) => {
        this.entries = Array.isArray(entries) ? entries : [];
        this.filteredEntries = this.entries;
        this.lotteryTypes = ['all', ...new Set(this.entries.map((e: any) => e.lottery))];
        this.loading = false;
      },
      error: () => {
        this.loading = false;
        this.error = true;
        this.toast.showError('Failed to load entries');
      }
    });
  }

  loadResults() {
    this.lottery.getResults().subscribe({
      next: (results: any) => this.results = Array.isArray(results) ? results : [],
      error: () => {}
    });
  }

  loadEntryLimit() {
    this.lottery.getEntryLimit().subscribe({
      next: (data) => this.entryLimit = data,
      error: () => {}
    });
  }

  filterByLottery() {
    this.filteredEntries = this.selectedLottery === 'all'
      ? this.entries
      : this.entries.filter(e => e.lottery === this.selectedLottery);
  }

  parseNumbers(numbersJson: string): number[] {
    try {
      return JSON.parse(numbersJson);
    } catch {
      return [];
    }
  }

  getResultForEntry(entry: any): any {
    return this.results.find(r => 
      r.lottery === entry.lottery && 
      new Date(r.drawDate).toDateString() === new Date(entry.draw_date).toDateString()
    );
  }

  isNumberMatch(entryNum: number, result: any): boolean {
    if (!result) return false;
    const winningNums = Array.isArray(result.numbers) ? result.numbers : this.parseNumbers(result.numbers);
    return winningNums.includes(entryNum);
  }

  isBonusMatch(entryNum: number, result: any): boolean {
    if (!result) return false;
    const bonusNums = Array.isArray(result.bonusNumbers) ? result.bonusNumbers : this.parseNumbers(result.bonusNumbers);
    return bonusNums.includes(entryNum);
  }

  getEntryStatus(entry: any): 'pending' | 'won' | 'lost' {
    const result = this.getResultForEntry(entry);
    if (!result) return 'pending';
    
    const entryNumbers = this.parseNumbers(entry.numbers);
    const entryBonusNumbers = this.parseNumbers(entry.bonus_numbers);
    const winningNumbers = Array.isArray(result.numbers) ? result.numbers : this.parseNumbers(result.numbers);
    const winningBonusNumbers = Array.isArray(result.bonusNumbers) ? result.bonusNumbers : this.parseNumbers(result.bonusNumbers);
    
    const allMainMatch = entryNumbers.every(num => winningNumbers.includes(num));
    const allBonusMatch = entryBonusNumbers.every(num => winningBonusNumbers.includes(num));
    
    return (allMainMatch && allBonusMatch) ? 'won' : 'lost';
  }
}
