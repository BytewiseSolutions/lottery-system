import { Component, OnInit, OnDestroy } from '@angular/core';
import { VotingService } from '../services/voting.service';
import { LotteryService } from '../services/lottery.service';
import { ToastService } from '../services/toast.service';

@Component({
  selector: 'app-voting',
  templateUrl: './voting.page.html',
  styleUrls: ['./voting.page.scss'],
  standalone: false
})
export class VotingPage implements OnInit, OnDestroy {
  selectedTab = 'voting';
  currentStep = 1;
  selectedNumbers: number[] = [];
  selectedBonusNumbers: number[] = [];
  numbers = Array.from({ length: 75 }, (_, i) => i + 1);
  upcomingDraws: any[] = [];
  selectedDraw: any = null;
  votingHistory: any[] = [];
  leadingNumbers: any = { section1: [], section2: [] };
  countdown = '00:00:00';
  isVotingTime = false;
  private countdownInterval: any;

  constructor(
    private votingService: VotingService,
    private lotteryService: LotteryService,
    private toastService: ToastService
  ) { }

  ngOnInit() {
    this.loadUpcomingDraws();
    this.updateVotingStatus();
    this.countdownInterval = setInterval(() => {
      this.updateVotingStatus();
    }, 1000);
  }

  ngOnDestroy() {
    if (this.countdownInterval) {
      clearInterval(this.countdownInterval);
    }
  }

  loadUpcomingDraws() {
    this.lotteryService.getUpcomingDraws().subscribe({
      next: (response) => {
        this.upcomingDraws = response.draws || [];
        if (this.upcomingDraws.length > 0) {
          this.selectedDraw = this.upcomingDraws[0];
          this.loadLeadingNumbers();
        }
      },
      error: (error) => console.error('Error loading draws:', error)
    });
  }

  updateVotingStatus() {
    this.isVotingTime = this.votingService.isVotingTime();
    this.countdown = this.votingService.getVotingCountdown();
  }

  selectNumber(num: number) {
    if (this.currentStep === 1 || this.currentStep === 2) {
      const index = this.selectedNumbers.indexOf(num);
      if (index > -1) {
        this.selectedNumbers.splice(index, 1);
      } else if (this.selectedNumbers.length < 5) {
        this.selectedNumbers.push(num);
      }
    } else if (this.currentStep === 3 || this.currentStep === 4) {
      const index = this.selectedBonusNumbers.indexOf(num);
      if (index > -1) {
        this.selectedBonusNumbers.splice(index, 1);
      } else if (this.selectedBonusNumbers.length < 2) {
        this.selectedBonusNumbers.push(num);
      }
    }
  }

  isSelected(num: number): boolean {
    if (this.currentStep <= 2) {
      return this.selectedNumbers.includes(num);
    } else {
      return this.selectedBonusNumbers.includes(num);
    }
  }

  nextStep() {
    if (this.currentStep === 1 && this.selectedNumbers.length === 5) {
      this.currentStep = 2;
    } else if (this.currentStep === 2) {
      this.currentStep = 3;
    } else if (this.currentStep === 3 && this.selectedBonusNumbers.length === 2) {
      this.currentStep = 4;
    } else if (this.currentStep === 4) {
      this.currentStep = 5;
    }
  }

  editNumbers() {
    this.currentStep = 2;
  }

  editBonusNumbers() {
    this.currentStep = 4;
  }

  submitVote() {
    if (!this.isVotingTime) {
      this.toastService.showError('Voting is only allowed between 19:00 and 19:59');
      return;
    }

    if (!this.selectedDraw) {
      this.toastService.showError('Please select a lottery');
      return;
    }

    this.votingService.submitVote(
      this.selectedDraw.name,
      this.selectedNumbers,
      this.selectedBonusNumbers,
      this.selectedDraw.draw_date
    ).subscribe({
      next: (response) => {
        this.toastService.showSuccess('Vote submitted successfully!');
        this.resetVoting();
        this.loadLeadingNumbers();
      },
      error: (error) => {
        this.toastService.showError(error.error?.error || 'Failed to submit vote');
      }
    });
  }

  resetVoting() {
    this.currentStep = 1;
    this.selectedNumbers = [];
    this.selectedBonusNumbers = [];
  }

  loadVotingHistory() {
    this.votingService.getVotingHistory().subscribe({
      next: (response) => {
        this.votingHistory = response.votes || [];
      },
      error: (error) => console.error('Error loading voting history:', error)
    });
  }

  loadLeadingNumbers() {
    if (!this.selectedDraw) return;
    
    this.votingService.getLeadingNumbers(this.selectedDraw.name, this.selectedDraw.draw_date).subscribe({
      next: (response) => {
        this.leadingNumbers = response;
      },
      error: (error) => console.error('Error loading leading numbers:', error)
    });
  }

  onTabChange(tab: string) {
    this.selectedTab = tab;
    if (tab === 'history') {
      this.loadVotingHistory();
    } else if (tab === 'leading') {
      this.loadLeadingNumbers();
    }
  }

  getNumbersInRange(start: number, end: number): number[] {
    return this.numbers.slice(start - 1, end);
  }
}
