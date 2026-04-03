import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LayoutComponent } from '../layout/layout.component';
import { LotteryService } from '../services/lottery.service';
import { ToastService } from '../services/toast.service';

@Component({
  selector: 'app-voting',
  imports: [CommonModule, LayoutComponent],
  templateUrl: './voting.component.html',
  styleUrl: './voting.component.css'
})
export class VotingComponent implements OnInit {
  activeTab: 'voting' | 'leading' | 'history' = 'voting';
  currentStep: 1 | 2 | 3 | 4 | 5 = 1;
  upcomingDraw: any = null;
  selectedNumbers: number[] = [];
  selectedBonus: number[] = [];
  numbers = Array.from({length: 75}, (_, i) => i + 1);
  votingHistory: any[] = [];
  leadingNumbers: any = null;
  countdown = '';
  isVotingTime = false;
  votingStarted = false;
  showSuccessPopup = false;
  showFullList = false;
  
  constructor(private lotteryService: LotteryService, private toastService: ToastService) {}
  
  ngOnInit() {
    this.loadUpcomingDraw();
    this.checkVotingTime();
    setInterval(() => this.checkVotingTime(), 1000);
    this.activeTab = 'voting';
    setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 100);
  }
  
  loadUpcomingDraw() {
    this.lotteryService.getCurrentVotingDraw().subscribe({
      next: (response: any) => {
        if (response.success && response.current_voting_draw) {
          this.upcomingDraw = response.current_voting_draw;
          this.isVotingTime = response.current_voting_draw.is_voting_open;
        } else {
          this.upcomingDraw = null;
          this.isVotingTime = false;
        }
      },
      error: (err) => {
        console.error('Error loading voting draw:', err);
        this.isVotingTime = false;
      }
    });
  }
  
  checkVotingTime() {
    if (!this.upcomingDraw || !this.upcomingDraw.voting_closes_at) {
      this.loadUpcomingDraw();
      return;
    }
    
    const now = new Date();
    const votingCloseTime = new Date(this.upcomingDraw.voting_closes_at);
    
    const diff = votingCloseTime.getTime() - now.getTime();
    
    if (diff <= 0) {
      this.countdown = '00:00:00';
      this.isVotingTime = false;
      // Voting has closed, reload to get next voting lottery
      this.loadUpcomingDraw();
      return;
    }
    
    this.isVotingTime = true;
    
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
  
  setTab(tab: 'voting' | 'leading' | 'history') {
    this.activeTab = tab;
    if (tab === 'history') this.loadHistory();
    if (tab === 'leading') this.loadLeadingNumbers();
    setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 100);
  }
  
  selectNumber(num: number) {
    if (this.currentStep === 1 || this.currentStep === 2) {
      // Main numbers selection
      const idx = this.selectedNumbers.indexOf(num);
      if (idx > -1) {
        this.selectedNumbers.splice(idx, 1);
      } else if (this.selectedNumbers.length < 5) {
        this.selectedNumbers.push(num);
        this.selectedNumbers.sort((a, b) => a - b);
      }
    } else if (this.currentStep === 3 || this.currentStep === 4) {
      // Bonus numbers selection - check if already selected in main numbers
      if (this.selectedNumbers.includes(num)) {
        this.toastService.showError(`Number ${num} is already selected in main numbers. Please choose a different number.`);
        return;
      }
      
      const idx = this.selectedBonus.indexOf(num);
      if (idx > -1) {
        this.selectedBonus.splice(idx, 1);
      } else if (this.selectedBonus.length < 2) {
        this.selectedBonus.push(num);
        this.selectedBonus.sort((a, b) => a - b);
      }
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
    // In bonus number selection steps, disable numbers already selected in main numbers
    if (this.currentStep === 3 || this.currentStep === 4) {
      return this.selectedNumbers.includes(num);
    }
    return false;
  }
  
  startVoting() {
    this.votingStarted = true;
    this.currentStep = 1;
    this.selectedNumbers = [];
    this.selectedBonus = [];
    setTimeout(() => {
      const element = document.querySelector('.lottery-info');
      if (element) {
        const yOffset = -150;
        const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    }, 100);
  }
  
  nextStep() {
    if (this.currentStep === 1 && this.selectedNumbers.length === 5) {
      this.currentStep = 2;
    } else if (this.currentStep === 2) {
      this.currentStep = 3;
    } else if (this.currentStep === 3 && this.selectedBonus.length === 2) {
      this.currentStep = 4;
    } else if (this.currentStep === 4) {
      this.currentStep = 5;
    }
    setTimeout(() => {
      const element = document.querySelector('.lottery-info');
      if (element) {
        const yOffset = -150;
        const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    }, 100);
  }
  
  editSection1() {
    this.currentStep = 2;
    setTimeout(() => {
      const element = document.querySelector('.lottery-info');
      if (element) {
        const yOffset = -150;
        const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    }, 100);
  }
  
  editSection2() {
    this.currentStep = 4;
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
    const token = localStorage.getItem('token');
    if (!token || !this.upcomingDraw) {
      this.toastService.showError('Please login to vote');
      this.votingStarted = false;
      this.currentStep = 1;
      return;
    }
    
    console.log('Upcoming draw object:', this.upcomingDraw);
    
    const voteData = {
      lottery: this.upcomingDraw.lottery || this.upcomingDraw.lottery_type || 'Unknown Lottery',
      numbers: this.selectedNumbers,
      bonusNumbers: this.selectedBonus,
      drawDate: this.upcomingDraw.draw_date?.split(' ')[0] || this.upcomingDraw.drawDate
    };
    
    console.log('Vote data being sent:', voteData);
    
    this.lotteryService.submitVote(voteData).subscribe({
      next: () => {
        this.showSuccessPopup = true;
        
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
        this.toastService.showError(err.error?.error || 'Failed to submit vote');
      }
    });
  }
  
  dismissSuccessPopup() {
    this.showSuccessPopup = false;
    this.votingStarted = false;
    this.currentStep = 1;
    this.selectedNumbers = [];
    this.selectedBonus = [];
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
  }
  
  quickPickBonus() {
    this.selectedBonus = [];
    // Filter out numbers already selected in main numbers
    const available = this.numbers.filter(num => !this.selectedNumbers.includes(num));
    
    for (let i = 0; i < 2; i++) {
      const randomIndex = Math.floor(Math.random() * available.length);
      this.selectedBonus.push(available[randomIndex]);
      available.splice(randomIndex, 1);
    }
    this.selectedBonus.sort((a, b) => a - b);
  }
  
  loadHistory() {
    const token = localStorage.getItem('token');
    if (!token) return;
    
    this.lotteryService.getVotingHistory().subscribe({
      next: (data) => this.votingHistory = data.votes || [],
      error: (err) => console.error(err)
    });
  }
  
  loadLeadingNumbers() {
    if (!this.upcomingDraw) return;
    
    this.lotteryService.getLeadingNumbers(
      this.upcomingDraw.lottery || this.upcomingDraw.lottery_type, 
      this.upcomingDraw.draw_date?.split(' ')[0] || this.upcomingDraw.drawDate
    ).subscribe({
      next: (data) => {
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
      error: (err) => console.error(err)
    });
  }
}
