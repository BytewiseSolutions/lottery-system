import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { ClaimFormComponent } from '../results/claim-form/claim-form.component';
import { PayFormComponent } from '../results/pay-form/pay-form.component';
import { BackendService } from '../../../util/backend.service';
import { SuccessPopupService } from '../../../services/success-popup.service';
import { ErrorHandlerService } from '../../../services/error-handler.service';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-winner',
  imports: [
    CommonModule,
    FormsModule,
    SidebarComponent,
    PayFormComponent,
    ClaimFormComponent
  ],
  templateUrl: './winner.component.html',
  styleUrl: './winner.component.css'
})
export class WinnerComponent implements OnInit {
  isPayModalOpen = false;
  isClaimModalOpen = false;
  selectedWinner: any = null;
  loading = true;
  error = false;
  exporting = false;
  processingWinnerId: number | null = null;
  processingWinnerAction: 'pay' | 'claim' | null = null;
  winners: any[] = [];
  currentPage = 1;
  itemsPerPage = 10;
  totalItems = 0;
  totalPages = 0;
  selectedClaimStatus = 'all';
  selectedPaymentStatus = 'all';
  sortOrder: 'newest' | 'oldest' = 'newest';
  totalPrizePoolAmount = 0;
  paidWinnersTotal = 0;
  pendingWinnersTotal = 0;
  latestWinnerCreatedAt: string | null = null;

  markClaimed(winner: any) {
    this.selectedWinner = winner;
    this.isClaimModalOpen = true;
  }

  payWinner(winner: any) {
    this.selectedWinner = winner;
    this.isPayModalOpen = true;
  }

  constructor(
    private backendService: BackendService,
    private successPopupService: SuccessPopupService,
    private errorHandlerService: ErrorHandlerService
  ) {}

  ngOnInit(): void {
    this.loadWinners();
  }

  loadWinners() {
    this.loading = true;
    this.error = false;

    this.backendService.getWinnersPage({
      page: this.currentPage,
      limit: this.itemsPerPage,
      claim_status: this.selectedClaimStatus !== 'all' ? this.selectedClaimStatus : undefined,
      payment_status: this.selectedPaymentStatus !== 'all' ? this.selectedPaymentStatus : undefined,
      sort_order: this.sortOrder
    }).subscribe({
      next: (response: any) => {
        this.winners = response?.success && Array.isArray(response.data) ? response.data : [];
        const pagination = response?.meta?.pagination || {};
        const stats = response?.meta?.stats || {};

        this.totalItems = Number(pagination.total_items ?? this.winners.length);
        this.totalPages = Number(pagination.total_pages ?? 0);
        this.currentPage = Number(pagination.current_page ?? this.currentPage);
        this.itemsPerPage = Number(pagination.per_page ?? this.itemsPerPage);
        this.totalPrizePoolAmount = Number(stats.total_prize_pool ?? 0);
        this.paidWinnersTotal = Number(stats.paid_winners_count ?? 0);
        this.pendingWinnersTotal = Number(stats.pending_winners_count ?? 0);
        this.latestWinnerCreatedAt = stats.latest_winner_created_at ?? null;
        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load winners:', error);
        this.winners = [];
        this.totalItems = 0;
        this.totalPages = 0;
        this.totalPrizePoolAmount = 0;
        this.paidWinnersTotal = 0;
        this.pendingWinnersTotal = 0;
        this.latestWinnerCreatedAt = null;
        this.loading = false;
        this.error = true;
      }
    });
  }

  handleClaim(claimData: any) {
    this.processingWinnerId = Number(claimData?.winner?.id) || null;
    this.processingWinnerAction = 'claim';

    this.backendService.markWinnerClaimed(claimData?.winner?.id).subscribe({
      next: (response: any) => {
        this.clearWinnerActionState();

        if (response?.success) {
          this.successPopupService.show('Winner marked as claimed.', 'Claim Updated');
          this.closeClaimModal();
          this.loadWinners();
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

  handlePayment(paymentData: any) {
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

        if (response?.success) {
          this.successPopupService.show('Winner payment processed successfully.', 'Payment Processed');
          this.closePayModal();
          this.loadWinners();
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

  closeClaimModal() {
    this.isClaimModalOpen = false;
    this.selectedWinner = null;
  }

  closePayModal() {
    this.isPayModalOpen = false;
    this.selectedWinner = null;
  }

  onFiltersChange() {
    this.currentPage = 1;
    this.loadWinners();
  }

  clearFilters() {
    this.selectedClaimStatus = 'all';
    this.selectedPaymentStatus = 'all';
    this.sortOrder = 'newest';
    this.currentPage = 1;
    this.loadWinners();
  }

  onPageChange(page: number) {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.loadWinners();
    }
  }

  exportCsv() {
    if (this.totalItems === 0 || this.exporting) {
      return;
    }

    this.exporting = true;
    this.fetchWinnersForExport(1, [], 100);
  }

  get totalPrizePool(): number {
    return this.totalPrizePoolAmount;
  }

  get paidWinnersCount(): number {
    return this.paidWinnersTotal;
  }

  get pendingWinnersCount(): number {
    return this.pendingWinnersTotal;
  }

  get latestWinnerDateLabel(): string {
    if (!this.latestWinnerCreatedAt) {
      return 'All Draws';
    }

    return this.formatDate(this.latestWinnerCreatedAt);
  }

  getSummaryLabel(): string {
    return this.totalItems > 0
      ? `All Draws - ${this.latestWinnerDateLabel}`
      : 'All Draws';
  }

  canPay(winner: any): boolean {
    return winner?.claim_status === 'claimed' && winner?.payment_status !== 'paid' && !this.isProcessingAnotherWinner(winner, 'pay');
  }

  canClaim(winner: any): boolean {
    return winner?.claim_status !== 'claimed' && !this.isProcessingAnotherWinner(winner, 'claim');
  }

  getPayLabel(winner: any): string {
    if (this.processingWinnerId === Number(winner?.id) && this.processingWinnerAction === 'pay') {
      return 'Processing...';
    }

    if (winner?.payment_status === 'paid') {
      return 'Paid';
    }

    if (winner?.claim_status !== 'claimed') {
      return 'Awaiting Claim';
    }

    return 'Pay';
  }

  getClaimLabel(winner: any): string {
    if (this.processingWinnerId === Number(winner?.id) && this.processingWinnerAction === 'claim') {
      return 'Updating...';
    }

    return winner?.claim_status === 'claimed' ? 'Claimed' : 'Claim';
  }

  formatCurrency(value: number | string): string {
    const amount = Number(value || 0);

    return amount.toLocaleString('en-LS', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  formatDate(value?: string): string {
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

  hasActiveFilters(): boolean {
    return this.selectedClaimStatus !== 'all' || this.selectedPaymentStatus !== 'all' || this.sortOrder !== 'newest';
  }

  private clearWinnerActionState() {
    this.processingWinnerId = null;
    this.processingWinnerAction = null;
  }

  private isProcessingAnotherWinner(winner: any, action: 'pay' | 'claim'): boolean {
    if (!this.processingWinnerAction) {
      return false;
    }

    return this.processingWinnerAction === action || this.processingWinnerId === Number(winner?.id);
  }

  private fetchWinnersForExport(page: number, collectedWinners: any[], limit: number) {
    this.backendService.getWinnersPage({
      page,
      limit,
      claim_status: this.selectedClaimStatus !== 'all' ? this.selectedClaimStatus : undefined,
      payment_status: this.selectedPaymentStatus !== 'all' ? this.selectedPaymentStatus : undefined,
      sort_order: this.sortOrder
    }).subscribe({
      next: (response: any) => {
        const exportWinners = response?.success && Array.isArray(response.data) ? response.data : [];
        const pagination = response?.meta?.pagination || {};
        const nextWinners = [...collectedWinners, ...exportWinners];
        const totalPages = Number(pagination.total_pages ?? 0);

        if (page < totalPages) {
          this.fetchWinnersForExport(page + 1, nextWinners, limit);
          return;
        }

        this.downloadWinnersCsv(nextWinners);
        this.exporting = false;
      },
      error: (error) => {
        console.error('Failed to export winners:', error);
        this.exporting = false;
      }
    });
  }

  private downloadWinnersCsv(winners: any[]) {
    const rows = [
      ['ID', 'Winner', 'Email', 'Prize Amount', 'Claim Status', 'Payment Status', 'Won At'],
      ...winners.map((winner) => [
        String(winner.id),
        winner.name || '',
        winner.email || '',
        String(winner.prize_amount ?? ''),
        winner.claim_status || '',
        winner.payment_status || '',
        this.formatDate(winner.created_at)
      ])
    ];

    const csv = rows
      .map((row) => row.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(','))
      .join('\n');

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `winners-${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();
    URL.revokeObjectURL(url);
  }
}
