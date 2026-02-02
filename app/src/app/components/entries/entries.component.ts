import { Component, OnInit } from '@angular/core';
import { LotteryService } from '../../services/lottery.service';

@Component({
  selector: 'app-entries',
  templateUrl: './entries.component.html',
  styleUrls: ['./entries.component.scss'],
  standalone: false,
})
export class EntriesComponent  implements OnInit {
  entries: any[] = [];
  results: any[] = [];

  constructor(private lottery: LotteryService) {}

  ngOnInit() {
    this.loadEntries();
    this.loadResults();
    window.addEventListener('entrySubmitted', () => this.loadEntries());
  }

  loadEntries() {
    this.lottery.getMyEntries().subscribe({
      next: (entries: any) => this.entries = Array.isArray(entries) ? entries : [],
      error: (err) => console.error('Error loading entries:', err)
    });
  }

  loadResults() {
    this.lottery.getResults().subscribe({
      next: (results: any) => this.results = Array.isArray(results) ? results : [],
      error: (err) => console.error('Error loading results:', err)
    });
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
}
