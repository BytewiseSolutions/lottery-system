import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { BackendService } from '../../../util/backend.service';
import { Router } from '@angular/router';
import { EntryDetail } from './entry';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-entry',
  imports: [CommonModule, FormsModule, SidebarComponent],
  templateUrl: './entry.component.html',
  styleUrl: './entry.component.css'
})
export class EntryComponent implements OnInit {
  loading = true;
  error = false;
  exporting = false;
  entries: EntryDetail[] = [];
  filteredEntries: EntryDetail[] = [];
  paginatedEntries: EntryDetail[] = [];
  currentPage = 1;
  itemsPerPage = 10;
  totalItems = 0;
  totalPages = 0;
  searchTerm = '';
  selectedLottery = 'all';
  sortOrder: 'newest' | 'oldest' = 'newest';
  lotteryOptions: string[] = [];

  constructor(
    private backendService: BackendService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadEntries();
  }

  loadEntries() {
    this.loading = true;
    this.error = false;

    this.backendService.getAllEntries().subscribe({
      next: (response: any) => {
        this.entries = response?.success && Array.isArray(response.data) ? response.data : [];
        this.lotteryOptions = [...new Set(this.entries.map((entry) => entry.lottery).filter(Boolean))].sort((a, b) => a.localeCompare(b));
        this.applyFilters();
        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load entries:', error);
        this.entries = [];
        this.filteredEntries = [];
        this.paginatedEntries = [];
        this.totalItems = 0;
        this.totalPages = 0;
        this.lotteryOptions = [];
        this.loading = false;
        this.error = true;
      }
    });
  }

  viewEntry(entry: EntryDetail) {
    this.router.navigate(['/admin-dashboard/entry', entry.id]);
  }

  exportCsv() {
    if (this.filteredEntries.length === 0 || this.exporting) {
      return;
    }

    this.exporting = true;

    const rows = [
      ['ID', 'User', 'Email', 'Phone', 'Lottery', 'Draw Date', 'Draw Time', 'Main Numbers', 'Bonus Numbers', 'Submitted At'],
      ...this.filteredEntries.map((entry) => [
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

  onFiltersChange() {
    this.applyFilters();
  }

  clearFilters() {
    this.searchTerm = '';
    this.selectedLottery = 'all';
    this.sortOrder = 'newest';
    this.applyFilters();
  }

  onPageChange(page: number) {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.updatePaginatedEntries();
    }
  }

  formatNumbers(numbers: number[] = []): string {
    return Array.isArray(numbers) ? numbers.join(', ') : '';
  }

  formatDrawLabel(entry: EntryDetail): string {
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

  getUserLabel(entry: EntryDetail): string {
    return entry.user_name?.trim() || `User #${entry.user_id}`;
  }

  hasActiveFilters(): boolean {
    return !!this.searchTerm.trim() || this.selectedLottery !== 'all' || this.sortOrder !== 'newest';
  }

  private applyFilters() {
    const search = this.searchTerm.trim().toLowerCase();

    let filtered = [...this.entries];

    if (this.selectedLottery !== 'all') {
      filtered = filtered.filter((entry) => entry.lottery === this.selectedLottery);
    }

    if (search) {
      filtered = filtered.filter((entry) => {
        const searchFields = [
          String(entry.id),
          this.getUserLabel(entry),
          entry.user_email || '',
          entry.user_phone || '',
          entry.lottery || '',
          this.formatDrawDate(entry.draw_datetime || entry.draw_date),
          this.formatDateTime(entry.created_at)
        ];

        return searchFields.some((field) => field.toLowerCase().includes(search));
      });
    }

    filtered.sort((left, right) => {
      const leftTime = new Date(left.created_at || '').getTime();
      const rightTime = new Date(right.created_at || '').getTime();

      if (this.sortOrder === 'oldest') {
        return leftTime - rightTime;
      }

      return rightTime - leftTime;
    });

    this.filteredEntries = filtered;
    this.totalItems = filtered.length;
    this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
    this.currentPage = 1;
    this.updatePaginatedEntries();
  }

  private updatePaginatedEntries() {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const endIndex = startIndex + this.itemsPerPage;
    this.paginatedEntries = this.filteredEntries.slice(startIndex, endIndex);
  }

}
