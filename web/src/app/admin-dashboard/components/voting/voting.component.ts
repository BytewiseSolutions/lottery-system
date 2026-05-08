import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { forkJoin } from 'rxjs';
import { ErrorHandlerService } from '../../../services/error-handler.service';
import { SuccessPopupService } from '../../../services/success-popup.service';
import { BackendService } from '../../../util/backend.service';
import { SidebarComponent } from '../../sidebar/sidebar.component';

interface AdminDrawOption {
  id: number;
  lottery: string;
  draw_date: string;
  status?: string;
  bucket: string;
}

interface HighestVotePreview {
  draw_id: number;
  lottery: string;
  draw_date: string;
  jackpot: number | string;
  winning_numbers: number[];
  bonus_numbers: number[];
  total_main_votes: number;
  total_bonus_votes: number;
}

interface VoteDistribution {
  mainNumberVotes?: Record<string, number>;
  bonusNumberVotes?: Record<string, number>;
  mainNumbers?: number[];
  bonusNumbers?: number[];
}

interface VoteAllocation {
  id: number;
  draw_id: number;
  lottery: string;
  numbers: number[];
  bonusNumbers: number[];
  bonus_numbers?: number[];
  allocatedVotes: number;
  allocated_votes: number;
  totalVotes: number;
  total_votes: number;
  drawDate: string;
  createdAt?: string;
  admin_name?: string;
  admin_email?: string;
  votingData?: VoteDistribution | null;
  voting_data?: VoteDistribution | null;
}

@Component({
  selector: 'app-voting',
  imports: [CommonModule, FormsModule, SidebarComponent],
  templateUrl: './voting.component.html',
  styleUrl: './voting.component.css'
})
export class VotingComponent implements OnInit {
  readonly numbers = Array.from({ length: 75 }, (_, index) => index + 1);

  loading = true;
  error = false;
  saving = false;
  exporting = false;
  previewLoading = false;
  detailLoading = false;
  deleting = false;
  isEditMode = false;

  currentStep: 'main' | 'bonus' = 'main';
  drawOptions: AdminDrawOption[] = [];
  highestVotePreview: HighestVotePreview | null = null;
  highestVoteMessage = 'Select a draw to see the current highest-vote combination.';
  allocations: VoteAllocation[] = [];
  selectedAllocation: VoteAllocation | null = null;

  selectedDrawId: number | null = null;
  editingVoteId: number | null = null;
  mainNumberVotes: Record<number, number> = {};
  bonusNumberVotes: Record<number, number> = {};
  showVotePopup = false;
  selectedNumberForVoting: number | null = null;
  voteAmount = 0;
  popupSection: 'main' | 'bonus' = 'main';
  formError = '';

  currentPage = 1;
  itemsPerPage = 10;
  totalItems = 0;
  totalPages = 0;
  totalAllocatedVotes = 0;
  totalAllocations = 0;
  latestAllocationAt: string | null = null;

  searchTerm = '';
  selectedLottery = 'all';
  sortOrder: 'newest' | 'oldest' = 'newest';
  lotteryOptions: string[] = [];

  showDetailsModal = false;
  showDeleteModal = false;
  showAllocationModal = false;

  constructor(
    private backendService: BackendService,
    private router: Router,
    private successPopupService: SuccessPopupService,
    private errorHandlerService: ErrorHandlerService
  ) {}

  openAllocationModal(): void {
    this.router.navigate(['/admin-dashboard/voting/new']);
  }

  closeAllocationModal(): void {
    if (this.saving) return;
    this.showAllocationModal = false;
    this.resetForm();
  }

  ngOnInit(): void {
    this.loadPageData();
  }

  loadPageData(): void {
    this.loadDrawOptions();
    this.loadAllocations();
  }

  loadDrawOptions(): void {
    forkJoin({
      upcoming: this.backendService.getUpcomingDraws(),
      past: this.backendService.getPastDraws()
    }).subscribe({
      next: ({ upcoming, past }) => {
        const optionMap = new Map<number, AdminDrawOption>();
        const upcomingDraws = Array.isArray(upcoming?.data) ? upcoming.data : [];
        const pastDraws = Array.isArray(past?.data) ? past.data : [];

        upcomingDraws.forEach((draw: any) => {
          optionMap.set(Number(draw.id), {
            id: Number(draw.id),
            lottery: draw.lottery || draw.lottery_type || 'Lottery',
            draw_date: draw.draw_date || draw.drawDate,
            status: draw.status,
            bucket: 'Upcoming Draws'
          });
        });

        pastDraws.forEach((draw: any) => {
          optionMap.set(Number(draw.id), {
            id: Number(draw.id),
            lottery: draw.lottery || draw.lottery_type || 'Lottery',
            draw_date: draw.draw_date || draw.drawDate,
            status: draw.status,
            bucket: 'Pending Result Draws'
          });
        });

        this.drawOptions = Array.from(optionMap.values()).sort((left, right) => {
          return new Date(left.draw_date).getTime() - new Date(right.draw_date).getTime();
        });

        if (!this.selectedDrawId && this.drawOptions.length > 0) {
          this.selectedDrawId = this.drawOptions[0].id;
        }

        if (this.selectedDrawId) {
          this.loadHighestVotePreview(this.selectedDrawId);
        } else {
          this.highestVotePreview = null;
          this.highestVoteMessage = 'No eligible draws are available for vote allocation right now.';
        }
      },
      error: (error) => {
        console.error('Failed to load draw options:', error);
        this.drawOptions = [];
        this.highestVotePreview = null;
        this.highestVoteMessage = 'Eligible draws could not be loaded right now.';
      }
    });
  }

  loadAllocations(): void {
    this.loading = true;
    this.error = false;

    this.backendService.getAdminVotes({
      page: this.currentPage,
      limit: this.itemsPerPage,
      search: this.searchTerm.trim() || undefined,
      lottery: this.selectedLottery !== 'all' ? this.selectedLottery : undefined,
      sort_order: this.sortOrder
    }).subscribe({
      next: (response: any) => {
        this.allocations = response?.success && Array.isArray(response.data) ? response.data : [];
        const pagination = response?.meta?.pagination || {};
        const filters = response?.meta?.filters || {};
        const stats = response?.meta?.stats || {};

        this.totalItems = Number(pagination.total_items ?? this.allocations.length);
        this.totalPages = Number(pagination.total_pages ?? 0);
        this.currentPage = Number(pagination.current_page ?? this.currentPage);
        this.itemsPerPage = Number(pagination.per_page ?? this.itemsPerPage);
        this.totalAllocatedVotes = Number(stats.total_allocated_votes ?? 0);
        this.totalAllocations = Number(stats.allocations_count ?? this.totalItems);
        this.latestAllocationAt = stats.latest_created_at ?? null;
        this.lotteryOptions = Array.isArray(filters.lottery_options) ? filters.lottery_options : this.getDefaultLotteryOptions();
        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load vote allocations:', error);
        this.allocations = [];
        this.totalItems = 0;
        this.totalPages = 0;
        this.totalAllocatedVotes = 0;
        this.totalAllocations = 0;
        this.latestAllocationAt = null;
        this.loading = false;
        this.error = true;
      }
    });
  }

  onDrawChange(): void {
    this.formError = '';

    if (this.selectedDrawId) {
      this.loadHighestVotePreview(this.selectedDrawId);
      return;
    }

    this.highestVotePreview = null;
    this.highestVoteMessage = 'Select a draw to see the current highest-vote combination.';
  }

  loadHighestVotePreview(drawId: number): void {
    this.previewLoading = true;
    this.highestVotePreview = null;
    this.highestVoteMessage = 'Loading highest-vote preview...';

    this.backendService.getHighestVoteForDraw(drawId).subscribe({
      next: (response: any) => {
        this.previewLoading = false;

        if (response?.success && response.data) {
          this.highestVotePreview = response.data;
          this.highestVoteMessage = '';
          return;
        }

        this.highestVotePreview = null;
        this.highestVoteMessage = response?.message || 'No highest-vote data is available for this draw yet.';
      },
      error: (error) => {
        console.error('Failed to load highest-vote preview:', error);
        this.previewLoading = false;
        this.highestVotePreview = null;
        this.highestVoteMessage = 'Highest-vote data could not be loaded right now.';
      }
    });
  }

  openVotePopup(number: number, section: 'main' | 'bonus'): void {
    this.formError = '';

    if (section === 'main') {
      if (this.currentStep !== 'main') return;

      if (Object.keys(this.mainNumberVotes).length >= 5 && !this.mainNumberVotes[number]) {
        this.formError = 'You can only allocate votes to 5 main numbers.';
        return;
      }
    } else {
      if (this.currentStep !== 'bonus') return;

      if (this.mainNumberVotes[number] > 0) {
        this.formError = 'Bonus numbers must be different from main numbers.';
        return;
      }

      if (Object.keys(this.bonusNumberVotes).length >= 2 && !this.bonusNumberVotes[number]) {
        this.formError = 'You can only allocate votes to 2 bonus numbers.';
        return;
      }
    }

    this.selectedNumberForVoting = number;
    this.popupSection = section;
    this.voteAmount = section === 'main'
      ? (this.mainNumberVotes[number] || 0)
      : (this.bonusNumberVotes[number] || 0);
    this.showVotePopup = true;
  }

  saveVoteAmount(): void {
    if (this.selectedNumberForVoting === null) return;

    if (this.voteAmount < 0) {
      this.formError = 'Vote amount cannot be negative.';
      return;
    }

    if (this.popupSection === 'main') {
      if (this.voteAmount === 0) {
        delete this.mainNumberVotes[this.selectedNumberForVoting];
      } else {
        this.mainNumberVotes[this.selectedNumberForVoting] = Math.floor(this.voteAmount);
      }
    } else {
      if (this.voteAmount === 0) {
        delete this.bonusNumberVotes[this.selectedNumberForVoting];
      } else {
        this.bonusNumberVotes[this.selectedNumberForVoting] = Math.floor(this.voteAmount);
      }
    }

    this.closeVotePopup();
  }

  closeVotePopup(): void {
    this.showVotePopup = false;
    this.selectedNumberForVoting = null;
    this.voteAmount = 0;
  }

  nextToBonus(): void {
    if (Object.keys(this.mainNumberVotes).length !== 5) {
      this.formError = 'Please allocate votes to exactly 5 main numbers before continuing.';
      return;
    }

    this.currentStep = 'bonus';
    this.formError = '';
  }

  backToMain(): void {
    this.currentStep = 'main';
    this.formError = '';
  }

  removeMainVotes(number: number): void {
    delete this.mainNumberVotes[number];
  }

  removeBonusVotes(number: number): void {
    delete this.bonusNumberVotes[number];
  }

  saveAllocation(): void {
    this.formError = '';

    const validationMessage = this.validateAllocation();
    if (validationMessage) {
      this.formError = validationMessage;
      return;
    }

    const mainNumbers = this.getSortedKeys(this.mainNumberVotes);
    const bonusNumbers = this.getSortedKeys(this.bonusNumberVotes);
    const payload = {
      ...(this.isEditMode && this.editingVoteId ? { id: this.editingVoteId } : {}),
      draw_id: Number(this.selectedDrawId),
      numbers: mainNumbers,
      bonus_numbers: bonusNumbers,
      allocated_votes: this.getTotalAllocatedVotes(),
      voting_data: {
        mainNumberVotes: this.mainNumberVotes,
        bonusNumberVotes: this.bonusNumberVotes,
        mainNumbers,
        bonusNumbers
      }
    };

    this.saving = true;

    const request = this.isEditMode
      ? this.backendService.updateAdminVote(payload)
      : this.backendService.createAdminVote(payload);

    request.subscribe({
      next: (response: any) => {
        this.saving = false;

        if (response?.success) {
          this.successPopupService.show(
            this.isEditMode ? 'Vote allocation updated successfully.' : 'Vote allocation created successfully.',
            this.isEditMode ? 'Allocation Updated' : 'Allocation Created'
          );
          this.closeAllocationModal();
          this.loadDrawOptions();
          this.loadAllocations();
          return;
        }

        this.formError = response?.message || 'Failed to save vote allocation';
      },
      error: (error) => {
        console.error('Failed to save vote allocation:', error);
        this.saving = false;
        this.formError = error?.error?.message || 'Failed to save vote allocation';
      }
    });
  }

  editAllocation(allocation: VoteAllocation): void {
    this.router.navigate(['/admin-dashboard/voting', allocation.id, 'edit']);
  }

  cancelEdit(): void {
    this.closeAllocationModal();
  }

  viewAllocation(allocation: VoteAllocation): void {
    this.router.navigate(['/admin-dashboard/voting', allocation.id]);
  }

  closeDetailsModal(): void {
    this.showDetailsModal = false;
    this.selectedAllocation = null;
    this.detailLoading = false;
  }

  requestDelete(allocation: VoteAllocation): void {
    this.selectedAllocation = allocation;
    this.showDeleteModal = true;
  }

  closeDeleteModal(): void {
    if (this.deleting) return;
    this.showDeleteModal = false;
    this.selectedAllocation = null;
  }

  confirmDelete(): void {
    if (!this.selectedAllocation?.id) return;

    const deletingId = this.selectedAllocation.id;
    this.deleting = true;

    this.backendService.deleteAdminVote(deletingId).subscribe({
      next: (response: any) => {
        this.deleting = false;

        if (response?.success) {
          this.successPopupService.show('Vote allocation deleted successfully.', 'Allocation Deleted');

          if (this.isEditMode && this.editingVoteId === deletingId) {
            this.resetForm();
          }

          this.closeDeleteModal();
          this.loadDrawOptions();
          this.loadAllocations();
          return;
        }

        this.errorHandlerService.showError(response?.message || 'Failed to delete vote allocation');
      },
      error: (error) => {
        console.error('Failed to delete vote allocation:', error);
        this.deleting = false;
        this.errorHandlerService.showError(error?.error?.message || 'Failed to delete vote allocation');
      }
    });
  }

  retryLoad(): void {
    this.loadPageData();
  }

  onFiltersChange(): void {
    this.currentPage = 1;
    this.loadAllocations();
  }

  clearFilters(): void {
    this.searchTerm = '';
    this.selectedLottery = 'all';
    this.sortOrder = 'newest';
    this.currentPage = 1;
    this.loadAllocations();
  }

  onPageChange(page: number): void {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.loadAllocations();
    }
  }

  exportCsv(): void {
    if (this.totalItems === 0 || this.exporting) return;
    this.exporting = true;
    this.fetchVotesForExport(1, [], 100);
  }

  getMainCount(): number {
    return Object.keys(this.mainNumberVotes).length;
  }

  getBonusCount(): number {
    return Object.keys(this.bonusNumberVotes).length;
  }

  getMainVotesTotal(): number {
    return Object.values(this.mainNumberVotes).reduce((sum, votes) => sum + votes, 0);
  }

  getBonusVotesTotal(): number {
    return Object.values(this.bonusNumberVotes).reduce((sum, votes) => sum + votes, 0);
  }

  getTotalAllocatedVotes(): number {
    return this.getMainVotesTotal() + this.getBonusVotesTotal();
  }

  formatNumbers(numbers: Array<number | null | undefined> = []): string {
    return numbers
      .filter((value): value is number => typeof value === 'number' && Number.isFinite(value))
      .join(', ');
  }

  formatDateTime(value?: string | null): string {
    if (!value) return 'Not available';

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) return value;

    return date.toLocaleString('en-GB', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    });
  }

  getDrawOptionLabel(draw: AdminDrawOption): string {
    return `${draw.lottery} - ${this.formatDateTime(draw.draw_date)} (${draw.bucket})`;
  }

  hasActiveFilters(): boolean {
    return !!this.searchTerm.trim() || this.selectedLottery !== 'all' || this.sortOrder !== 'newest';
  }

  getLatestAllocationLabel(): string {
    return this.latestAllocationAt ? this.formatDateTime(this.latestAllocationAt) : 'No allocations yet';
  }

  getDetailVoteMap(type: 'main' | 'bonus'): Array<[string, number]> {
    const allocation = this.selectedAllocation;
    const votingData = allocation?.votingData || allocation?.voting_data;
    const map = type === 'main'
      ? (votingData?.mainNumberVotes || {})
      : (votingData?.bonusNumberVotes || {});

    return Object.entries(map).sort((left, right) => Number(left[0]) - Number(right[0]));
  }

  private fetchVotesForExport(page: number, collectedVotes: VoteAllocation[], limit: number): void {
    this.backendService.getAdminVotes({
      page,
      limit,
      search: this.searchTerm.trim() || undefined,
      lottery: this.selectedLottery !== 'all' ? this.selectedLottery : undefined,
      sort_order: this.sortOrder
    }).subscribe({
      next: (response: any) => {
        const exportVotes = response?.success && Array.isArray(response.data) ? response.data : [];
        const pagination = response?.meta?.pagination || {};
        const nextVotes = [...collectedVotes, ...exportVotes];
        const totalPages = Number(pagination.total_pages ?? 0);

        if (page < totalPages) {
          this.fetchVotesForExport(page + 1, nextVotes, limit);
          return;
        }

        this.downloadVotesCsv(nextVotes);
        this.exporting = false;
      },
      error: (error) => {
        console.error('Failed to export vote allocations:', error);
        this.exporting = false;
      }
    });
  }

  private downloadVotesCsv(votes: VoteAllocation[]): void {
    const rows = [
      ['ID', 'Lottery', 'Draw Date', 'Main Numbers', 'Main Votes', 'Bonus Numbers', 'Bonus Votes', 'Allocated Votes', 'Created By', 'Created At'],
      ...votes.map((vote) => [
        String(vote.id),
        vote.lottery || '',
        this.formatDateTime(vote.drawDate),
        this.formatNumbers(vote.numbers),
        String(this.sumVoteMap((vote.votingData || vote.voting_data)?.mainNumberVotes || {})),
        this.formatNumbers(vote.bonusNumbers || vote.bonus_numbers || []),
        String(this.sumVoteMap((vote.votingData || vote.voting_data)?.bonusNumberVotes || {})),
        String(vote.allocated_votes || vote.total_votes || 0),
        vote.admin_name || '',
        this.formatDateTime(vote.createdAt)
      ])
    ];

    const csv = rows
      .map((row) => row.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(','))
      .join('\n');

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `vote-allocations-${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();
    URL.revokeObjectURL(url);
  }

  private validateAllocation(): string | null {
    if (!this.selectedDrawId) return 'Please select a draw.';
    if (this.getMainCount() !== 5) return 'Please allocate votes to exactly 5 main numbers.';
    if (this.getBonusCount() !== 2) return 'Please allocate votes to exactly 2 bonus numbers.';
    if (this.getTotalAllocatedVotes() <= 0) return 'Please allocate at least one vote.';
    return null;
  }

  private resetForm(): void {
    const selectedDrawId = this.drawOptions[0]?.id ?? null;

    this.selectedDrawId = selectedDrawId;
    this.editingVoteId = null;
    this.isEditMode = false;
    this.currentStep = 'main';
    this.mainNumberVotes = {};
    this.bonusNumberVotes = {};
    this.formError = '';

    if (selectedDrawId) {
      this.loadHighestVotePreview(selectedDrawId);
    } else {
      this.highestVotePreview = null;
      this.highestVoteMessage = 'No eligible draws are available for vote allocation right now.';
    }
  }

  private ensureDrawOptionFromAllocation(allocation: VoteAllocation): void {
    const drawId = Number(allocation.draw_id);

    if (this.drawOptions.some((draw) => draw.id === drawId)) return;

    this.drawOptions = [
      {
        id: drawId,
        lottery: allocation.lottery,
        draw_date: allocation.drawDate,
        bucket: 'Existing Allocation'
      },
      ...this.drawOptions
    ];
  }

  private extractVoteMap(allocation: VoteAllocation, type: 'main' | 'bonus'): Record<number, number> {
    const votingData = allocation.votingData || allocation.voting_data;
    const rawMap = type === 'main'
      ? (votingData?.mainNumberVotes || {})
      : (votingData?.bonusNumberVotes || {});

    if (Object.keys(rawMap).length > 0) {
      return Object.entries(rawMap).reduce((result, [number, votes]) => {
        result[Number(number)] = Number(votes);
        return result;
      }, {} as Record<number, number>);
    }

    const numbers = type === 'main'
      ? (allocation.numbers || [])
      : (allocation.bonusNumbers || allocation.bonus_numbers || []);

    if (numbers.length === 0) return {};

    const fallbackVotes = Math.max(1, Math.floor(
      (allocation.allocated_votes || allocation.total_votes || 0) /
      (allocation.numbers.length + (allocation.bonusNumbers || allocation.bonus_numbers || []).length || 1)
    ));

    return numbers.reduce((result, number) => {
      result[Number(number)] = fallbackVotes;
      return result;
    }, {} as Record<number, number>);
  }

  private getSortedKeys(voteMap: Record<number, number>): number[] {
    return Object.keys(voteMap)
      .map((value) => Number(value))
      .sort((left, right) => left - right);
  }

  private sumVoteMap(voteMap: Record<string, number>): number {
    return Object.values(voteMap).reduce((sum, votes) => sum + Number(votes || 0), 0);
  }

  private getDefaultLotteryOptions(): string[] {
    return ['Monday Lotto', 'Wednesday Lotto', 'Friday Lotto'];
  }
}
