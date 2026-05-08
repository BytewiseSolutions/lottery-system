import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { firstValueFrom } from 'rxjs';
import { LayoutComponent } from '../layout/layout.component';
import { BackendService } from '../util/backend.service';
import { ApiResponse } from '../util/api-response';

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
  paginatedEntries: GroupedEntry[] = [];
  selectedDate = '';
  currentQuote = "A ticket today could change your tomorrow.";
  results: any[] = [];
  currentPage = 1;
  itemsPerPage = 5;
  totalPages = 0;

  constructor(private backendService: BackendService) {}

  ngOnInit() {
    this.isLoggedIn = !!(localStorage.getItem('auth_token') || localStorage.getItem('token'));
    this.setRandomQuote();
    this.startQuoteRotation();
    if (this.isLoggedIn) {
      this.loadHistoryData();
    }
  }

  private startQuoteRotation() {
    setInterval(() => {
      this.setRandomQuote();
    }, 5000);
  }

  async loadHistoryData() {
    await this.loadResults();
    await this.loadHistory();
  }

  async loadResults() {
    try {
      const response = await firstValueFrom(this.backendService.getResults()) as ApiResponse<any[]>;
      this.results = response?.data ?? [];
    } catch (error) {
      console.error('Error loading results:', error);
      this.results = [];
    }
  }

  async loadHistory() {
    try {
      const response = await firstValueFrom(this.backendService.getEntryHistory()) as ApiResponse<any[]>;
      const entries = response?.data ?? [];
      this.processEntries(entries);
    } catch (error) {
      console.error('Error loading history:', error);
      this.processEntries([]);
    }
  }

  processEntries(entries: any[]) {
    const safeEntries = Array.isArray(entries) ? entries : [];

    this.historyEntries = safeEntries.map(entry => {
      const numbers = this.parseNumberList(entry.numbers);
      const bonus_numbers = this.parseNumberList(entry.bonus_numbers);
      
      const matchResult = this.checkMatch(entry, numbers, bonus_numbers);
      
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

  checkMatch(entry: any, numbers: number[], bonusNumbers: number[]) {
    const lottery = entry?.lottery;
    const drawDate = entry?.draw_date;
    const entryDrawId = Number(entry?.draw_id || 0);

    if (!drawDate) {
      return {
        matchedNumbers: [],
        matchedBonus: [],
        unmatchedNumbers: [],
        unmatchedBonus: [],
        status: 'Pending' as const
      };
    }
    
    const entryDrawDate = new Date(drawDate).toISOString().split('T')[0];
    
    const result = this.results.find(r => {
      const resultDrawId = Number(r.draw_id || 0);

      if (entryDrawId > 0 && resultDrawId > 0) {
        return resultDrawId === entryDrawId;
      }

      const resultDate = r.drawDate || r.draw_date;
      if (!resultDate) return false;
      const resultDrawDate = new Date(resultDate).toISOString().split('T')[0];
      return r.lottery === lottery && resultDrawDate === entryDrawDate;
    });

    if (!result || String(result.status || '').toLowerCase() !== 'published') {
      return {
        matchedNumbers: [],
        matchedBonus: [],
        unmatchedNumbers: [],
        unmatchedBonus: [],
        status: 'Pending' as const
      };
    }

    const winningNumbers = this.parseNumberList(result.numbers || result.winning_numbers);
    const winningBonus = this.parseNumberList(result.bonusNumbers || result.bonus_numbers);

    if (winningNumbers.length === 0 && winningBonus.length === 0) {
      return {
        matchedNumbers: [],
        matchedBonus: [],
        unmatchedNumbers: [],
        unmatchedBonus: [],
        status: 'Pending' as const
      };
    }

    const matchedNumbers = numbers.filter(n => winningNumbers.includes(n));
    const matchedBonus = bonusNumbers.filter(b => winningBonus.includes(b));
    const unmatchedNumbers = numbers.filter(n => !winningNumbers.includes(n));
    const unmatchedBonus = bonusNumbers.filter(b => !winningBonus.includes(b));

    const status = (matchedNumbers.length === 5 && matchedBonus.length === 2) ? 'Won' : 'Lost';

    return { matchedNumbers, matchedBonus, unmatchedNumbers, unmatchedBonus, status: status as 'Won' | 'Lost' };
  }

  private parseNumberList(value: any): number[] {
    if (Array.isArray(value)) {
      return value.map((item) => Number(item)).filter((item) => !Number.isNaN(item));
    }

    if (typeof value === 'string') {
      try {
        const parsed = JSON.parse(value);
        if (Array.isArray(parsed)) {
          return parsed.map((item) => Number(item)).filter((item) => !Number.isNaN(item));
        }
      } catch (error) {
        console.warn('Failed to parse number list:', value, error);
      }
    }

    return [];
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
    
    this.totalPages = Math.ceil(this.groupedEntries.length / this.itemsPerPage);
    this.updatePagination();
  }

  updatePagination() {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const endIndex = startIndex + this.itemsPerPage;
    this.paginatedEntries = this.groupedEntries.slice(startIndex, endIndex);
  }

  goToPage(page: number) {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.updatePagination();
    }
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
