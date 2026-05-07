import { Component, OnInit } from '@angular/core';
import { SidebarComponent } from '../../../sidebar/sidebar.component';
import { CommonModule } from '@angular/common';
import { WinnerListComponent } from '../winner-list/winner-list.component';
import { ActivatedRoute, Router } from '@angular/router';
import { BackendService } from '../../../../util/backend.service';
import { SuccessPopupService } from '../../../../services/success-popup.service';
import { ErrorHandlerService } from '../../../../services/error-handler.service';

interface ResultDetail {
  id: number;
  draw_id: number;
  lottery: string;
  draw_date: string;
  jackpot: number | string;
  status: string;
  winning_numbers: number[];
  bonus_numbers: number[];
  created_at?: string;
  updated_at?: string;
  total_entries?: number;
}

@Component({
  selector: 'app-results-details',
  standalone: true,
  imports: [
    CommonModule, 
    SidebarComponent,
    WinnerListComponent
  ],
  templateUrl: './results-details.component.html',
  styleUrl: './results-details.component.css'
})
export class ResultsDetailsComponent implements OnInit {

  activeTab: string = 'winners';
  loading = true;
  error = false;
  winnersLoading = false;
  winnersError = false;
  entriesLoading = false;
  entriesError = false;
  processingWinnerId: number | null = null;
  processingWinnerAction: 'pay' | 'claim' | null = null;
  result: ResultDetail | null = null;
  winners: any[] = [];
  entries: any[] = [];

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private backendService: BackendService,
    private successPopupService: SuccessPopupService,
    private errorHandlerService: ErrorHandlerService
  ) {}

  ngOnInit() {
    this.route.paramMap.subscribe(params => {
      const id = Number(params.get('id'));

      if (!id) {
        this.loading = false;
        this.error = true;
        return;
      }

      this.loadResult(id);
    });
  }

  private loadResult(resultId: number) {
    this.loading = true;
    this.error = false;

    this.backendService.getResultById(resultId).subscribe({
      next: (response: any) => {
        if (!response?.success || !response.data) {
          this.result = null;
          this.error = true;
          this.loading = false;
          return;
        }

        this.result = response.data as ResultDetail;
        this.error = !this.result;
        if (this.result) {
          this.loadWinners(this.result.id);
          this.loadEntries(this.result.draw_id);
        } else {
          this.winners = [];
          this.entries = [];
        }
        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load result details:', error);
        this.result = null;
        this.error = true;
        this.loading = false;
      }
    });
  }

  private loadWinners(resultId: number) {
    this.winnersLoading = true;
    this.winnersError = false;

    this.backendService.getWinners(resultId).subscribe({
      next: (response: any) => {
        this.winners = response?.success && Array.isArray(response.data) ? response.data : [];
        this.winnersLoading = false;
      },
      error: (error) => {
        console.error('Failed to load winners:', error);
        this.winners = [];
        this.winnersLoading = false;
        this.winnersError = true;
      }
    });
  }

  private loadEntries(drawId: number) {
    this.entriesLoading = true;
    this.entriesError = false;

    this.backendService.getEntriesByDraw(drawId).subscribe({
      next: (response: any) => {
        this.entries = response?.success && Array.isArray(response.data) ? response.data : [];
        this.entriesLoading = false;
      },
      error: (error) => {
        console.error('Failed to load entries:', error);
        this.entries = [];
        this.entriesLoading = false;
        this.entriesError = true;
      }
    });
  }

  retryEntriesLoad() {
    if (this.result) {
      this.loadEntries(this.result.draw_id);
    }
  }

  payWinner(paymentData: any) {
    this.processingWinnerId = Number(paymentData?.winner?.id) || null;
    this.processingWinnerAction = 'pay';

    this.backendService.processWinnerPayment({
      winner_id: paymentData?.winner?.id,
      amount: paymentData?.amount,
      payment_method: paymentData?.payment_method,
      transaction_id: paymentData?.transaction_id
    }).subscribe({
      next: (response: any) => {
        this.clearWinnerActionState();

        if (response?.success && this.result) {
          this.successPopupService.show('Winner payment processed successfully.', 'Payment Processed');
          this.loadWinners(this.result.id);
          return;
        }

        this.errorHandlerService.showError(response?.message || 'Failed to process payment');
      },
      error: (error) => {
        console.error('Failed to process payment:', error);
        this.clearWinnerActionState();
        this.errorHandlerService.showError(error?.error?.message || 'Failed to process payment');
      }
    });
  }

  markClaimed(claimData: any) {
    this.processingWinnerId = Number(claimData?.winner?.id) || null;
    this.processingWinnerAction = 'claim';

    this.backendService.markWinnerClaimed(claimData?.winner?.id).subscribe({
      next: (response: any) => {
        this.clearWinnerActionState();

        if (response?.success && this.result) {
          this.successPopupService.show('Winner marked as claimed.', 'Claim Updated');
          this.loadWinners(this.result.id);
          return;
        }

        this.errorHandlerService.showError(response?.message || 'Failed to mark winner as claimed');
      },
      error: (error) => {
        console.error('Failed to mark winner as claimed:', error);
        this.clearWinnerActionState();
        this.errorHandlerService.showError(error?.error?.message || 'Failed to mark winner as claimed');
      }
    });
  }

  goBackToResults() {
    this.router.navigate(['/admin-dashboard/results']);
  }

  retryResultLoad() {
    const id = Number(this.route.snapshot.paramMap.get('id'));

    if (id) {
      this.loadResult(id);
    }
  }

  formatDate(dateString?: string): string {
    if (!dateString) {
      return 'Not available';
    }

    const date = new Date(dateString);

    if (Number.isNaN(date.getTime())) {
      return dateString;
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

  formatDrawDate(dateString?: string): string {
    if (!dateString) {
      return 'Not available';
    }

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

  formatJackpot(value: number | string | undefined): string {
    const amount = typeof value === 'number' ? value : Number(value);

    if (Number.isNaN(amount)) {
      return String(value ?? '0');
    }

    return amount.toLocaleString('en-LS', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  formatNumbers(numbers: number[] = []): string {
    return numbers.join(', ');
  }

  private clearWinnerActionState() {
    this.processingWinnerId = null;
    this.processingWinnerAction = null;
  }
}
