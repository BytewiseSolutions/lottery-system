import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BackendService } from '../../../../util/backend.service';
import { SidebarComponent } from '../../../sidebar/sidebar.component';
import { EntryDetail } from '../entry';

@Component({
  selector: 'app-entry-details',
  standalone: true,
  imports: [CommonModule, SidebarComponent],
  templateUrl: './entry-details.component.html',
  styleUrl: './entry-details.component.css'
})
export class EntryDetailsComponent implements OnInit {
  activeTab: 'numbers' | 'user' | 'meta' = 'numbers';
  loading = true;
  error = false;
  entry: EntryDetail | null = null;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private backendService: BackendService
  ) {}

  ngOnInit(): void {
    this.route.paramMap.subscribe((params) => {
      const id = Number(params.get('id'));

      if (!id) {
        this.loading = false;
        this.error = true;
        return;
      }

      this.loadEntry(id);
    });
  }

  goBackToEntries() {
    this.router.navigate(['/admin-dashboard/entry']);
  }

  retryEntryLoad() {
    const id = Number(this.route.snapshot.paramMap.get('id'));

    if (id) {
      this.loadEntry(id);
    }
  }

  formatNumbers(numbers: number[] = []): string {
    return numbers.join(', ');
  }

  formatDate(value?: string): string {
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

  formatTime(value?: string): string {
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

  getUserLabel(): string {
    if (!this.entry) {
      return 'Not available';
    }

    return this.entry.user_name?.trim() || `User #${this.entry.user_id}`;
  }

  private loadEntry(entryId: number) {
    this.loading = true;
    this.error = false;

    this.backendService.getEntryById(entryId).subscribe({
      next: (response: any) => {
        if (!response?.success || !response.data) {
          this.entry = null;
          this.error = true;
          this.loading = false;
          return;
        }

        this.entry = response.data as EntryDetail;
        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load entry details:', error);
        this.entry = null;
        this.error = true;
        this.loading = false;
      }
    });
  }
}
