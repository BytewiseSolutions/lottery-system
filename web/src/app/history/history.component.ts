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

  ngOnInit() {
    this.isLoggedIn = !!localStorage.getItem('token');
    this.setRandomQuote();
    this.startQuoteRotation();
    if (this.isLoggedIn) {
      this.loadHistory();
    }
  }

  private startQuoteRotation() {
    setInterval(() => {
      this.setRandomQuote();
    }, 5000);
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
    this.historyEntries = entries.map(entry => ({
      ...entry,
      numbers: typeof entry.numbers === 'string' ? JSON.parse(entry.numbers) : entry.numbers,
      bonus_numbers: typeof entry.bonus_numbers === 'string' ? JSON.parse(entry.bonus_numbers) : entry.bonus_numbers,
      date: new Date(entry.created_at).toDateString()
    }));

    this.filteredEntries = [...this.historyEntries];
    this.groupEntriesByDate();
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
    return new Date(dateString).toLocaleTimeString('en-US', { 
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
