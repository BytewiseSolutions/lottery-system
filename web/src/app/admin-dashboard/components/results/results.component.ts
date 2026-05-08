import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { ResultsFormComponent } from './results-form/results-form.component';
import { Router } from '@angular/router';
import { BackendService } from '../../../util/backend.service';
import { SuccessPopupService } from '../../../util/success-popup.service';
import { ErrorHandlerService } from '../../../util/error-handler.service';

interface AdminResult {
  id: number;
  lottery: string;
  draw_date: string;
  draw_id: number | string;
  jackpot: number | string;
  winners_count: number;
  status: string;
  winning_numbers: number[];
  bonus_numbers: number[];
  created_at?: string;
}

@Component({
  selector: 'app-results',
  imports: [
    CommonModule,
    SidebarComponent,
    ResultsFormComponent
  ],
  templateUrl: './results.component.html',
  styleUrl: './results.component.css'
})
export class ResultsComponent implements OnInit {
  isModalOpen = false;
  isEditMode = false;
  loading = true;
  error = false;
  saving = false;
  selectedResult: any = null;
  currentPage = 1;
  itemsPerPage = 10;
  totalItems = 0;
  totalPages = 0;

  constructor(
    private router: Router,
    private backendService: BackendService,
    private successPopupService: SuccessPopupService,
    private errorHandlerService: ErrorHandlerService
  ) {}

  results: AdminResult[] = [];
  paginatedResults: AdminResult[] = [];

  ngOnInit() {
    this.loadResults();
  }

  loadResults() {
    this.loading = true;
    this.error = false;

    this.backendService.getResults().subscribe({
      next: (response: any) => {
        if (response?.success) {
          this.results = Array.isArray(response.data) ? response.data : [];
          this.totalItems = this.results.length;
          this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
          this.currentPage = 1;
          this.updatePaginatedResults();
        } else {
          this.results = [];
          this.error = true;
          this.totalItems = 0;
          this.totalPages = 0;
          this.paginatedResults = [];
        }

        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load results:', error);
        this.results = [];
        this.paginatedResults = [];
        this.totalItems = 0;
        this.totalPages = 0;
        this.loading = false;
        this.error = true;
      }
    });
  }

  openModal() {
    if (this.saving) {
      return;
    }

    this.isModalOpen = true;
    this.isEditMode = false;
    this.selectedResult = null;
  }

  closeModal() {
    if (this.saving) {
      return;
    }

    this.isModalOpen = false;
    this.isEditMode = false;
    this.selectedResult = null;
  }
  viewResult(result: any) {
    this.router.navigate(['/admin-dashboard/results', result.id]);
  }
  editResult(result: any) {
    if (this.saving) {
      return;
    }

    this.isModalOpen = true;
    this.isEditMode = true;

    this.selectedResult = {
      ...result
    };
  }

  isModalBusy(): boolean {
    return this.saving;
  }

  onPageChange(page: number) {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.updatePaginatedResults();
    }
  }

  private updatePaginatedResults() {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const endIndex = startIndex + this.itemsPerPage;
    this.paginatedResults = this.results.slice(startIndex, endIndex);
  }

  saveResult(formData: any) {
    const payload = {
      ...(this.isEditMode && this.selectedResult?.id ? { id: this.selectedResult.id } : {}),
      ...formData,
      winning_numbers: formData.winning_numbers.map((num: number | null) => Number(num)),
      bonus_numbers: formData.bonus_numbers.filter((num: number | null) => num !== null).map((num: number | null) => Number(num)),
      jackpot: Number(formData.jackpot),
      winners_count: Number(formData.winners_count ?? 0)
    };

    this.saving = true;

    const request = this.isEditMode
      ? this.backendService.updateResult(payload)
      : this.backendService.createResult(payload);

    request.subscribe({
      next: (response: any) => {
        this.saving = false;

        if (response?.success) {
          this.successPopupService.show(
            this.isEditMode ? 'Result updated successfully.' : 'Result uploaded successfully.',
            this.isEditMode ? 'Result Updated' : 'Result Uploaded'
          );
          this.closeModal();
          this.loadResults();
          return;
        }

        this.errorHandlerService.showError(response?.message || 'Failed to save result');
      },
      error: (error) => {
        console.error('Failed to save result:', error);
        this.saving = false;
        this.errorHandlerService.showError(error?.error?.message || 'Failed to save result');
      }
    });
  }

  formatDrawDate(dateString: string): string {
    const date = new Date(dateString);

    if (Number.isNaN(date.getTime())) {
      return dateString;
    }

    return date.toLocaleDateString('en-GB', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
  }

  formatJackpot(value: number | string): string {
    const amount = typeof value === 'number' ? value : Number(value);

    if (Number.isNaN(amount)) {
      return String(value);
    }

    return amount.toLocaleString('en-LS', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  formatStatus(status: string): string {
    return status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Unknown';
  }
}
