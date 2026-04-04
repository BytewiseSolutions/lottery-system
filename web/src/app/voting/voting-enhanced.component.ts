import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LayoutComponent } from '../layout/layout.component';
import { LotteryService } from '../services/lottery.service';
import { ToastService } from '../services/toast.service';
import { Subscription, interval } from 'rxjs';

interface VoteData {
  lottery: string;
  numbers: number[];
  bonusNumbers: number[];
  drawDate: string;
}

interface VotingHistory {
  id: number;
  lottery: string;
  numbers: number[];
  bonusNumbers: number[];
  voteDate: string;
  drawDate: string;
  createdAt: string;
}

@Component({
  selector: 'app-voting-enhanced',
  imports: [CommonModule, LayoutComponent],
  templateUrl: './voting-enhanced.component.html',
  styleUrl: './voting-enhanced.component.css'
})
export class VotingEnhancedComponent implements OnInit, OnDestroy {
  activeTab: 'voting' | 'leading' | 'history' = 'voting';
  currentStep: 1 | 2 | 3 | 4 | 5 = 1;
  upcomingDraw: any = null;
  selectedNumbers: number[] = [];
  selectedBonus: number[] = [];
  numbers = Array.from({length: 75}, (_, i) => i + 1);
  votingHistory: VotingHistory[] = [];
  leadingNumbers: any = null;
  countdown = '';
  isVotingTime = false;
  votingStarted = false;
  showSuccessPopup = false;
  showFullList = false;
  
  // Enhanced features
  isLoading = false;
  isSubmitting = false;
  loadingMessage = '';
  errorMessage = '';
  showErrorDialog = false;
  
  // Subscriptions
  private countdownSubscription?: Subscription;
  private autoSaveSubscription?: Subscription;
  
  // Auto-save draft
  private draftKey = 'voting_draft';
  
  // Validation states
  numbersValid = false;
  bonusValid = false;
  
  constructor(
    private lotteryService: LotteryService, 
    private toastService: ToastService
  ) {}
  
  ngOnInit() {
    this.loadUpcomingDraw();
    this.startCountdown();
    this.loadDraft();
    this.setupAutoSave();
    this.activeTab = 'voting';
    setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 100);
  }
  
  ngOnDestroy() {
    this.countdownSubscription?.unsubscribe();
    this.autoSaveSubscription?.unsubscribe();
  }
  
  loadUpcomingDraw() {
    this.isLoading = true;
    this.loadingMessage = 'Loading upcoming draws...';
    
    this.lotteryService.getUpcomingDraws().subscribe({
      next: (draws: any) => {
        this.isLoading = false;
        const drawsArray = Array.isArray(draws) ? draws : draws.draws || [];
        if (drawsArray.length > 0) {
          this.upcomingDraw = drawsArray[0];
          this.checkVotingEligibility();
        } else {
          this.showError('No upcoming draws available');
        }
      },
      error: (err) => {
        this.isLoading = false;
        this.showError('Failed to load upcoming draws');
        console.error('Error loading draws:', err);
      }
    });
  }
  
  checkVotingEligibility() {
    if (!this.upcomingDraw) return;

    this.isVotingTime = true;
  }
  
  startCountdown() {
    this.countdownSubscription = interval(1000).subscribe(() => {
      this.updateCountdown();
    });
  }
  
  updateCountdown() {
    if (!this.upcomingDraw) return;
    
    const now = new Date();
    const drawDate = new Date(this.upcomingDraw.draw_date);
    drawDate.setHours(19, 59, 59, 999); // Voting closes at 19:59
    
    const diff = drawDate.getTime() - now.getTime();
    
    if (diff <= 0) {
      this.countdown = '00:00:00';
      this.isVotingTime = false;
      this.loadUpcomingDraw(); // Reload to get next draw
      return;
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    if (days > 0) {
      this.countdown = `${days}d ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    } else {
      this.countdown = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
  }
  
  setupAutoSave() {
    this.autoSaveSubscription = interval(30000).subscribe(() => {
      this.saveDraft();
    });
  }
  
  saveDraft() {
    if (this.selectedNumbers.length > 0 || this.selectedBonus.length > 0) {
      const draft = {
        numbers: this.selectedNumbers,
        bonusNumbers: this.selectedBonus,
        lottery: this.upcomingDraw?.lottery,
        drawDate: this.upcomingDraw?.draw_date,
        timestamp: Date.now()
      };
      localStorage.setItem(this.draftKey, JSON.stringify(draft));
    }
  }
  
  loadDraft() {
    const draftStr = localStorage.getItem(this.draftKey);
    if (draftStr) {
      try {
        const draft = JSON.parse(draftStr);
        // Only load if draft is less than 24 hours old
        if (Date.now() - draft.timestamp < 24 * 60 * 60 * 1000) {
          this.selectedNumbers = draft.numbers || [];
          this.selectedBonus = draft.bonusNumbers || [];
          this.validateSelections();
        }
      } catch (e) {
        console.error('Error loading draft:', e);
      }
    }
  }
  
  clearDraft() {
    localStorage.removeItem(this.draftKey);
  }
  
  setTab(tab: 'voting' | 'leading' | 'history') {
    this.activeTab = tab;
    if (tab === 'history') this.loadHistory();
    if (tab === 'leading') this.loadLeadingNumbers();
    setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 100);
  }
  
  selectNumber(num: number) {
    if (this.currentStep === 1 || this.currentStep === 2) {
      this.toggleMainNumber(num);
    } else if (this.currentStep === 3 || this.currentStep === 4) {
      this.toggleBonusNumber(num);
    }
    this.validateSelections();
    this.saveDraft();
  }
  
  private toggleMainNumber(num: number) {
    const idx = this.selectedNumbers.indexOf(num);
    if (idx > -1) {
      this.selectedNumbers.splice(idx, 1);
    } else if (this.selectedNumbers.length < 5) {
      this.selectedNumbers.push(num);
      this.selectedNumbers.sort((a, b) => a - b);
    } else {
      this.showError('You can only select 5 main numbers');
    }
  }
  
  private toggleBonusNumber(num: number) {
    // Check if number is already selected in main numbers
    if (this.selectedNumbers.includes(num)) {
      this.showError('This number is already selected in main numbers');
      return;
    }
    
    const idx = this.selectedBonus.indexOf(num);
    if (idx > -1) {
      this.selectedBonus.splice(idx, 1);
    } else if (this.selectedBonus.length < 2) {
      this.selectedBonus.push(num);
      this.selectedBonus.sort((a, b) => a - b);
    } else {
      this.showError('You can only select 2 bonus numbers');
    }
  }
  
  validateSelections() {
    this.numbersValid = this.selectedNumbers.length === 5;
    this.bonusValid = this.selectedBonus.length === 2;
    
    // Check for overlaps
    const overlap = this.selectedNumbers.some(num => this.selectedBonus.includes(num));
    if (overlap) {
      this.bonusValid = false;
      this.showError('Numbers cannot appear in both main and bonus sections');
    }
  }
  
  isSelected(num: number): boolean {
    if (this.currentStep === 1 || this.currentStep === 2) {
      return this.selectedNumbers.includes(num);
    }
    if (this.currentStep === 3 || this.currentStep === 4) {
      return this.selectedBonus.includes(num);
    }
    return false;
  }
  
  isDisabled(num: number): boolean {
    if (this.currentStep === 3 || this.currentStep === 4) {
      // Disable if already selected in main numbers
      return this.selectedNumbers.includes(num);
    }
    return false;
  }
  
  startVoting() {
    this.votingStarted = true;
    this.currentStep = 1;
    this.scrollToVotingArea();
  }
  
  nextStep() {
    if (this.currentStep === 1 && this.numbersValid) {
      this.currentStep = 2;
    } else if (this.currentStep === 2) {
      this.currentStep = 3;
    } else if (this.currentStep === 3 && this.bonusValid) {
      this.currentStep = 4;
    } else if (this.currentStep === 4) {
      this.currentStep = 5;
    }
    this.scrollToVotingArea();
  }
  
  previousStep() {
    if (this.currentStep > 1) {
      this.currentStep = (this.currentStep - 1) as 1 | 2 | 3 | 4 | 5;
      this.scrollToVotingArea();
    }
  }
  
  editSection1() {
    this.currentStep = 2;
    this.scrollToVotingArea();
  }
  
  editSection2() {
    this.currentStep = 4;
    this.scrollToVotingArea();
  }
  
  private scrollToVotingArea() {
    setTimeout(() => {
      const element = document.querySelector('.lottery-info');
      if (element) {
        const yOffset = -150;
        const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    }, 100);
  }
  
  submitVote() {
    if (!this.validateVoteSubmission()) {
      return;
    }
    
    this.isSubmitting = true;
    this.loadingMessage = 'Submitting your vote...';
    
    const voteData: VoteData = {
      lottery: this.upcomingDraw.lottery || this.upcomingDraw.name || 'Unknown Lottery',
      numbers: [...this.selectedNumbers],
      bonusNumbers: [...this.selectedBonus],
      drawDate: this.upcomingDraw.draw_date?.split(' ')[0] || this.upcomingDraw.drawDate
    };
    
    this.lotteryService.submitVote(voteData).subscribe({
      next: (response) => {
        this.isSubmitting = false;
        this.showSuccessPopup = true;
        this.clearDraft();
        
        setTimeout(() => {
          const popup = document.querySelector('.success-popup');
          if (popup) {
            popup.classList.add('show');
          }
        }, 50);
        
        setTimeout(() => {
          if (this.showSuccessPopup) {
            this.dismissSuccessPopup();
          }
        }, 15000);
      },
      error: (err) => {
        this.isSubmitting = false;
        const errorMsg = err.error?.error || 'Failed to submit vote';
        this.showError(errorMsg);
      }
    });
  }
  
  private validateVoteSubmission(): boolean {
    const token = localStorage.getItem('token');
    if (!token) {
      this.showError('Please login to vote');
      return false;
    }
    
    if (!this.upcomingDraw) {
      this.showError('No upcoming draw available');
      return false;
    }
    
    if (!this.numbersValid) {
      this.showError('Please select exactly 5 main numbers');
      return false;
    }
    
    if (!this.bonusValid) {
      this.showError('Please select exactly 2 bonus numbers');
      return false;
    }
    
    return true;
  }
  
  dismissSuccessPopup() {
    this.showSuccessPopup = false;
    this.votingStarted = false;
    this.currentStep = 1;
    this.selectedNumbers = [];
    this.selectedBonus = [];
    this.validateSelections();
  }
  
  quickPick() {
    this.selectedNumbers = [];
    const available = [...this.numbers];
    
    for (let i = 0; i < 5; i++) {
      const randomIndex = Math.floor(Math.random() * available.length);
      this.selectedNumbers.push(available[randomIndex]);
      available.splice(randomIndex, 1);
    }
    
    this.selectedNumbers.sort((a, b) => a - b);
    this.validateSelections();
    this.saveDraft();
  }
  
  quickPickBonus() {
    this.selectedBonus = [];
    const available = this.numbers.filter(num => !this.selectedNumbers.includes(num));
    
    for (let i = 0; i < 2; i++) {
      const randomIndex = Math.floor(Math.random() * available.length);
      this.selectedBonus.push(available[randomIndex]);
      available.splice(randomIndex, 1);
    }
    
    this.selectedBonus.sort((a, b) => a - b);
    this.validateSelections();
    this.saveDraft();
  }
  
  loadHistory() {
    const token = localStorage.getItem('token');
    if (!token) return;
    
    this.isLoading = true;
    this.loadingMessage = 'Loading voting history...';
    
    this.lotteryService.getVotingHistory().subscribe({
      next: (data) => {
        this.isLoading = false;
        this.votingHistory = data.votes || [];
      },
      error: (err) => {
        this.isLoading = false;
        this.showError('Failed to load voting history');
        console.error(err);
      }
    });
  }
  
  loadLeadingNumbers() {
    if (!this.upcomingDraw) return;
    
    this.isLoading = true;
    this.loadingMessage = 'Loading leading numbers...';
    
    this.lotteryService.getLeadingNumbers(
      this.upcomingDraw.lottery_type || this.upcomingDraw.lottery, 
      this.upcomingDraw.draw_date?.split(' ')[0] || this.upcomingDraw.drawDate
    ).subscribe({
      next: (data) => {
        this.isLoading = false;
        this.leadingNumbers = data;
        
        // Sort the top numbers numerically for display
        if (this.leadingNumbers.section1) {
          this.leadingNumbers.topSection1 = this.leadingNumbers.section1
            .slice(0, 5)
            .map((item: any) => item.number)
            .sort((a: number, b: number) => a - b);
        }
        if (this.leadingNumbers.section2) {
          this.leadingNumbers.topSection2 = this.leadingNumbers.section2
            .slice(0, 2)
            .map((item: any) => item.number)
            .sort((a: number, b: number) => a - b);
        }
      },
      error: (err) => {
        this.isLoading = false;
        this.showError('Failed to load leading numbers');
        console.error(err);
      }
    });
  }
  
  private showError(message: string) {
    this.errorMessage = message;
    this.showErrorDialog = true;
    this.toastService.showError(message);
  }
  
  closeErrorDialog() {
    this.showErrorDialog = false;
    this.errorMessage = '';
  }
  
  resetVoting() {
    this.selectedNumbers = [];
    this.selectedBonus = [];
    this.currentStep = 1;
    this.votingStarted = false;
    this.validateSelections();
    this.clearDraft();
  }
}
