import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { BackendService } from '../../../util/backend.service';

interface AdminEntry {
  id: number;
  user_id: number;
  draw_id: number;
  lottery: string;
  numbers: number[];
  bonus_numbers: number[];
  draw_date: string;
  draw_datetime?: string;
  created_at: string;
  user_name?: string;
  user_email?: string;
  user_phone?: string;
}

@Component({
  selector: 'app-entry',
  imports: [CommonModule, SidebarComponent],
  templateUrl: './entry.component.html',
  styleUrl: './entry.component.css'
})
export class EntryComponent implements OnInit {
  loading = true;
  error = false;
  exporting = false;
  entries: AdminEntry[] = [];
  selectedEntry: AdminEntry | null = null;

  constructor(private backendService: BackendService) {}

  ngOnInit(): void {
    this.loadEntries();
  }

  loadEntries() {
    this.loading = true;
    this.error = false;

    this.backendService.getAllEntries().subscribe({
      next: (response: any) => {
        this.entries = response?.success && Array.isArray(response.data) ? response.data : [];
        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load entries:', error);
        this.entries = [];
        this.loading = false;
        this.error = true;
      }
    });
  }

  viewEntry(entry: AdminEntry) {
    this.selectedEntry = entry;
  }

  closeEntryModal() {
    this.selectedEntry = null;
  }

  exportCsv() {
    if (this.entries.length === 0 || this.exporting) {
      return;
    }

    this.exporting = true;

    const rows = [
      ['ID', 'User', 'Email', 'Phone', 'Lottery', 'Draw Date', 'Draw Time', 'Main Numbers', 'Bonus Numbers', 'Submitted At'],
      ...this.entries.map((entry) => [
        String(entry.id),
        entry.user_name || '',
        entry.user_email || '',
        entry.user_phone || '',
        entry.lottery || '',
        this.formatDrawDate(entry.draw_datetime || entry.draw_date),
        this.formatDrawTime(entry.draw_datetime),
        this.formatNumbers(entry.numbers),
        this.formatNumbers(entry.bonus_numbers),
        this.formatDateTime(entry.created_at)
      ])
    ];

    const csv = rows
      .map((row) => row.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(','))
      .join('\n');

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `entries-${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();
    URL.revokeObjectURL(url);

    this.exporting = false;
  }

  formatNumbers(numbers: number[] = []): string {
    return Array.isArray(numbers) ? numbers.join(', ') : '';
  }

  formatDrawLabel(entry: AdminEntry): string {
    const drawDate = entry.draw_datetime || entry.draw_date;
    return `${entry.lottery} ${this.formatDrawDate(drawDate)}`;
  }

  formatDrawDate(value?: string): string {
    if (!value) {
      return 'Not available';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
      return value;
    }

    return date.toLocaleDateString('en-GB', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
  }

  formatDrawTime(value?: string): string {
    if (!value) {
      return 'Not available';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
      return 'Not available';
    }

    return date.toLocaleTimeString('en-GB', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    });
  }

  formatDateTime(value?: string): string {
    if (!value) {
      return 'Not available';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
      return value;
    }

    return date.toLocaleString('en-GB', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    });
  }

  getUserLabel(entry: AdminEntry): string {
    return entry.user_name?.trim() || `User #${entry.user_id}`;
  }

}
