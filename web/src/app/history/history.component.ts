import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { LayoutComponent } from '../layout/layout.component';
import { environment } from '../../environments/environment';

interface HistoryEntry {
  id: number;
  lottery: string;
  numbers: number[];
  bonus_numbers: number[];
  created_at: string;
  draw_date: string;
  date: string;
  matchedNumbers?: number[];
  matchedBonus?: number[];
  unmatchedNumbers?: number[];
  unmatchedBonus?: number[];
  status?: 'Won' | 'Lost' | 'Pending';
}

interface GroupedEntry {
  date: string;
  entries: HistoryEntry[];
  hasMoreEntries: boolean;
  showAllEntries: boolean;
}

@Component({
  selector: 'app-history',
  imports: [CommonModule, FormsModule, LayoutComponent],
  templateUrl: './history.component.html',
  styleUrl: './history.component.css'
})
export class HistoryComponent implements OnInit {
  isLoggedIn = false;
  historyEntries: HistoryEntry[] = [];
  filteredEntries: HistoryEntry[] = [];
  groupedEntries: GroupedEntry[] = [];
  selectedDate = '';
  currentQuote = "A ticket today could change your tomorrow.";
  results: any[] = [];

  ngOnInit() {
    this.isLoggedIn = !!localStorage.getItem('token');
    this.setRandomQuote();
    this.startQuoteRotation();
    if (this.isLoggedIn) {
      this.loadResults();
      this.loadHistory();
    }
  }

  private startQuoteRotation() {
    setInterval(() => {
      this.setRandomQuote();
    }, 5000);
  }

  async loadResults() {
    try {
      const response = await fetch(`${environment.apiUrl}/results`);
      this.results = await response.json();
    } catch (error) {
      console.error('Error loading results:', error);
    }
  }

  async loadHistory() {
    try {
      const token = localStorage.getItem('token');
      const response = await fetch(`${environment.apiUrl}/entries`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      const entries = await response.json();
      this.processEntries(entries);
    } catch (error) {
      console.error('Error loading history:', error);
    }
  }

  processEntries(entries: any[]) {
    this.historyEntries = entries.map(entry => {
      const numbers = typeof entry.numbers === 'string' ? JSON.parse(entry.numbers) : entry.numbers;
      const bonus_numbers = typeof entry.bonus_numbers === 'string' ? JSON.parse(entry.bonus_numbers) : entry.bonus_numbers;
      
      const matchResult = this.checkMatch(entry.lottery, entry.draw_date, numbers, bonus_numbers);
      
      return {
        ...entry,
        numbers,
        bonus_numbers,
        date: new Date(entry.created_at).toDateString(),
        matchedNumbers: matchResult.matchedNumbers,
        matchedBonus: matchResult.matchedBonus,
        unmatchedNumbers: matchResult.unmatchedNumbers,
        unmatchedBonus: matchResult.unmatchedBonus,
        status: matchResult.status
      };
    });

    this.filteredEntries = [...this.historyEntries];
    this.groupEntriesByDate();
  }

  checkMatch(lottery: string, drawDate: string, numbers: number[], bonusNumbers: number[]) {
    if (!drawDate) {
      return { matchedNumbers: [], matchedBonus: [], status: 'Pending' as const };
    }
    
    const entryDrawDate = new Date(drawDate).toISOString().split('T')[0];
    
    const result = this.results.find(r => {
      const resultDate = r.drawDate || r.draw_date;
      if (!resultDate) return false;
      const resultDrawDate = new Date(resultDate).toISOString().split('T')[0];
      return r.lottery === lottery && resultDrawDate === entryDrawDate;
    });

    if (!result || result.status !== 'published') {
      return { matchedNumbers: [], matchedBonus: [], status: 'Pending' as const };
    }

    const winningNumbers = result.numbers || result.winning_numbers;
    const winningBonus = result.bonusNumbers || result.bonus_numbers;

    if (!winningNumbers || !winningBonus) {
      return { matchedNumbers: [], matchedBonus: [], status: 'Pending' as const };
    }

    const matchedNumbers = numbers.filter(n => winningNumbers.includes(n));
    const matchedBonus = bonusNumbers.filter(b => winningBonus.includes(b));
    const unmatchedNumbers = numbers.filter(n => !winningNumbers.includes(n));
    const unmatchedBonus = bonusNumbers.filter(b => !winningBonus.includes(b));

    const status = (matchedNumbers.length === 5 && matchedBonus.length === 2) ? 'Won' : 'Lost';

    return { matchedNumbers, matchedBonus, unmatchedNumbers, unmatchedBonus, status: status as 'Won' | 'Lost' };
  }

  groupEntriesByDate() {
    const grouped = this.filteredEntries.reduce((acc, entry) => {
      if (!acc[entry.date]) {
        acc[entry.date] = [];
      }
      acc[entry.date].push(entry);
      return acc;
    }, {} as {[key: string]: HistoryEntry[]});

    this.groupedEntries = Object.keys(grouped)
      .sort((a, b) => new Date(b).getTime() - new Date(a).getTime())
      .map(date => ({
        date,
        entries: grouped[date].sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime()),
        hasMoreEntries: grouped[date].length > 1,
        showAllEntries: false
      }));
  }

  filterByDate() {
    if (!this.selectedDate) {
      this.filteredEntries = [...this.historyEntries];
    } else {
      const selectedDateObj = new Date(this.selectedDate);
      this.filteredEntries = this.historyEntries.filter(entry => {
        const entryDate = new Date(entry.created_at);
        return entryDate.toDateString() === selectedDateObj.toDateString();
      });
    }
    this.groupEntriesByDate();
  }

  toggleMoreEntries(group: GroupedEntry) {
    group.showAllEntries = !group.showAllEntries;
  }

  formatTime(dateString: string): string {
    return new Date(dateString).toLocaleString('en-US', { 
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit', 
      minute: '2-digit',
      hour12: false
    });
  }

  formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('en-US', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
  }

  formatDrawDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('en-US', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
  }

  private setRandomQuote() {
    const quotes = [
      "A ticket today could change your tomorrow.",
      "Dream big — the jackpot is calling your name.",
      "Someone's going to win. Why not you?",
      "It only takes one lucky number to rewrite your story.",
      "Hope starts with a single ticket."
    ];
    this.currentQuote = quotes[Math.floor(Math.random() * quotes.length)];
  }

  getCurrentUserId(): number {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user).id : 0;
  }
}
