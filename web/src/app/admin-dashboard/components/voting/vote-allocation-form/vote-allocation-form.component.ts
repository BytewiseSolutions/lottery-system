import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { forkJoin } from 'rxjs';
import { ErrorHandlerService } from '../../../../util/error-handler.service';
import { SuccessPopupService } from '../../../../util/success-popup.service';
import { BackendService } from '../../../../util/backend.service';
import { SidebarComponent } from '../../../sidebar/sidebar.component';

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
  votingData?: VoteDistribution | null;
  voting_data?: VoteDistribution | null;
}

@Component({
  selector: 'app-vote-allocation-form',
  standalone: true,
  imports: [CommonModule, FormsModule, SidebarComponent],
  templateUrl: './vote-allocation-form.component.html',
  styleUrl: './vote-allocation-form.component.css'
})
export class VoteAllocationFormComponent implements OnInit {
  readonly numbers = Array.from({ length: 75 }, (_, index) => index + 1);

  isEditMode = false;
  editingVoteId: number | null = null;
  loading = true;
  saving = false;
  previewLoading = false;
  formError = '';

  currentStep: 'main' | 'bonus' = 'main';
  drawOptions: AdminDrawOption[] = [];
  selectedDrawId: number | null = null;
  mainNumberVotes: Record<number, number> = {};
  bonusNumberVotes: Record<number, number> = {};
  highestVotePreview: HighestVotePreview | null = null;
  highestVoteMessage = 'Select a draw to see the current highest-vote combination.';

  showVotePopup = false;
  selectedNumberForVoting: number | null = null;
  voteAmount = 0;
  popupSection: 'main' | 'bonus' = 'main';

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private backendService: BackendService,
    private successPopupService: SuccessPopupService,
    private errorHandlerService: ErrorHandlerService
  ) {}

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');

    if (id) {
      this.isEditMode = true;
      this.editingVoteId = Number(id);
      this.loadForEdit(Number(id));
    } else {
      this.isEditMode = false;
      this.loadDrawOptions();
    }
  }

  private loadForEdit(voteId: number): void {
    forkJoin({
      vote: this.backendService.getAdminVoteById(voteId),
      upcoming: this.backendService.getUpcomingDraws()
    }).subscribe({
      next: ({ vote, upcoming }) => {
        this.buildDrawOptions(upcoming);

        if (vote?.success && vote.data) {
          const allocation = vote.data as VoteAllocation;
          this.selectedDrawId = allocation.draw_id;
          this.mainNumberVotes = this.extractVoteMap(allocation, 'main');
          this.bonusNumberVotes = this.extractVoteMap(allocation, 'bonus');
          this.ensureDrawOptionFromAllocation(allocation);

          if (this.selectedDrawId) {
            this.loadHighestVotePreview(this.selectedDrawId);
          }
        }

        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load allocation for edit:', error);
        this.errorHandlerService.showError('Failed to load vote allocation.');
        this.loading = false;
      }
    });
  }

  private loadDrawOptions(): void {
    this.backendService.getUpcomingDraws().subscribe({
      next: (upcoming) => {
        this.buildDrawOptions(upcoming);

        if (this.drawOptions.length > 0) {
          this.selectedDrawId = this.drawOptions[0].id;
          this.loadHighestVotePreview(this.selectedDrawId);
        }

        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load draw options:', error);
        this.loading = false;
      }
    });
  }

  private buildDrawOptions(upcoming: any): void {
    const upcomingDraws = Array.isArray(upcoming?.data) ? upcoming.data : [];

    this.drawOptions = upcomingDraws
      .map((draw: any) => ({
        id: Number(draw.id),
        lottery: draw.lottery || draw.lottery_type || 'Lottery',
        draw_date: draw.draw_date || draw.drawDate,
        status: draw.status,
        bucket: 'Upcoming Draws'
      }))
      .sort((left: AdminDrawOption, right: AdminDrawOption) => new Date(left.draw_date).getTime() - new Date(right.draw_date).getTime())
      .slice(0, 3);
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
        this.highestVoteMessage = response?.message || 'No highest-vote data available for this draw yet.';
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
          this.router.navigate(['/admin-dashboard/voting']);
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

  goBack(): void {
    this.router.navigate(['/admin-dashboard/voting']);
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

  getDrawOptionLabel(draw: AdminDrawOption): string {
    return `${draw.lottery} - ${this.formatDateTime(draw.draw_date)} (${draw.bucket})`;
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

  private validateAllocation(): string | null {
    if (!this.selectedDrawId) return 'Please select a draw.';
    if (this.getMainCount() !== 5) return 'Please allocate votes to exactly 5 main numbers.';
    if (this.getBonusCount() !== 2) return 'Please allocate votes to exactly 2 bonus numbers.';
    if (this.getTotalAllocatedVotes() <= 0) return 'Please allocate at least one vote.';
    return null;
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
}
