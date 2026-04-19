import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { LayoutComponent } from '../layout/layout.component';
import { ToastService } from '../services/toast.service';
import { BackendService } from '../util/backend.service';

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
  isLoadingDraw = false;
  drawLoadError = '';
  
  constructor(
    private backendService: BackendService,
    private toastService: ToastService,
    private router: Router
  ) {}
  
  ngOnInit() {
    this.loadUpcomingDraw();
    this.checkVotingTime();
    setInterval(() => this.checkVotingTime(), 1000);
    this.activeTab = 'voting';
    setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 100);
  }
  
  loadUpcomingDraw() {
    this.isLoadingDraw = true;
    this.drawLoadError = '';
    this.upcomingDraw = null;
    console.log('Loading upcoming draw...');
    
    this.backendService.getCurrentDraw().subscribe({
      next: (response: any) => {
        console.log('getCurrentDraw response:', response);
        this.isLoadingDraw = false;
        const draw = response?.data;

        if (response?.success && draw) {
          this.upcomingDraw = draw;
          this.isVotingTime = !!draw.is_voting_open;
          console.log('upcomingDraw set to:', this.upcomingDraw);
        } else {
          this.loadFallbackDraw();
        }
      },
      error: (err) => {
        console.error('Error loading voting draw:', err);
        console.error('Error status:', err.status);
        console.error('Error message:', err.message);
        this.isVotingTime = false;
        this.loadFallbackDraw();
      }
    });
  }
  
  loadFallbackDraw() {
    this.backendService.getUpcomingDraws().subscribe({
      next: (response: any) => {
        const drawsArray = response?.data ?? [];
        if (drawsArray.length > 0) {
          this.upcomingDraw = drawsArray[0];
          this.isVotingTime = !!drawsArray[0].is_voting_open;
          this.drawLoadError = '';
        } else {
          this.upcomingDraw = null;
          this.isVotingTime = false;
          this.drawLoadError = response?.message || 'No voting draw is available right now.';
        }
        this.isLoadingDraw = false;
      },
      error: (err) => {
        console.error('Error loading fallback draws:', err);
        this.upcomingDraw = null;
        this.isVotingTime = false;
        this.drawLoadError = 'Unable to load voting information right now. Please try again.';
        this.isLoadingDraw = false;
      }
    });
  }

  checkVotingTime() {
    if (!this.upcomingDraw) {
      this.countdown = '00:00:00';
      this.isVotingTime = false;
      return;
    }

    const now = new Date();
    const drawDate = new Date(this.upcomingDraw.draw_date || this.upcomingDraw.drawDate);
    
    drawDate.setHours(19, 59, 59, 999);
    
    const diff = drawDate.getTime() - now.getTime();
    
    if (diff <= 0) {
      this.countdown = '00:00:00';
      this.isVotingTime = false;
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
        // Let backend handle sorting when needed
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
        // Let backend handle sorting when needed
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
      if (this.selectedNumbers.length !== 5) {
        this.toastService.showError('Please select exactly 5 numbers for Section 1');
        return;
      }
      this.currentStep = 3;
    } else if (this.currentStep === 3 && this.selectedBonus.length === 2) {
      this.currentStep = 4;
    } else if (this.currentStep === 4) {
      if (this.selectedBonus.length !== 2) {
        this.toastService.showError('Please select exactly 2 bonus numbers for Section 2');
        return;
      }
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
    if (!this.hasAuthToken()) {
      this.showLogin();
      this.votingStarted = false;
      this.currentStep = 1;
      return;
    }

    if (!this.upcomingDraw) {
      this.toastService.showError('No upcoming draw available');
      this.votingStarted = false;
      this.currentStep = 1;
      return;
    }

    if (this.selectedNumbers.length !== 5) {
      this.toastService.showError('Please select exactly 5 numbers for Section 1');
      this.currentStep = 2;
      return;
    }

    if (this.selectedBonus.length !== 2) {
      this.toastService.showError('Please select exactly 2 bonus numbers for Section 2');
      this.currentStep = 4;
      return;
    }
    
    console.log('Upcoming draw object:', this.upcomingDraw);
    
    const voteData = {
      lottery: this.upcomingDraw.lottery || this.upcomingDraw.lottery_type || 'Unknown Lottery',
      numbers: [...this.selectedNumbers].sort((a, b) => a - b),
      bonusNumbers: [...this.selectedBonus].sort((a, b) => a - b),
      drawDate: this.getDrawDate(this.upcomingDraw)
    };
    
    console.log('Vote data being sent:', voteData);
    
    this.backendService.submitVote(voteData).subscribe({
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
        if (err?.status === 401) {
          this.clearAuthState();
          this.showLogin();
          return;
        }

        this.toastService.showError(err?.error?.message || err?.error?.error || 'Failed to submit vote');
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
    this.backendService.getQuickPickNumbers('main').subscribe({
      next: (response: any) => {
        if (response.success) {
          this.selectedNumbers = response?.data?.numbers ?? [];
        } else {
          this.toastService.showError('Failed to generate quick pick numbers');
        }
      },
      error: (err) => {
        this.toastService.showError('Failed to generate quick pick numbers');
        console.error('Quick pick error:', err);
      }
    });
  }
  
  quickPickBonus() {
    this.backendService.getQuickPickNumbers('bonus', this.selectedNumbers).subscribe({
      next: (response: any) => {
        if (response.success) {
          this.selectedBonus = response?.data?.numbers ?? [];
        } else {
          this.toastService.showError('Failed to generate quick pick bonus numbers');
        }
      },
      error: (err) => {
        this.toastService.showError('Failed to generate quick pick bonus numbers');
        console.error('Quick pick bonus error:', err);
      }
    });
  }
  
  loadHistory() {
    if (!this.hasAuthToken()) {
      this.showLogin();
      return;
    }
    
    this.backendService.getVoteHistory().subscribe({
      next: (response: any) => this.votingHistory = response?.data?.votes || [],
      error: (err) => console.error(err)
    });
  }
  
  loadLeadingNumbers() {
    if (!this.upcomingDraw) return;
    
    this.backendService.getLeadingNumbers(
      this.upcomingDraw.lottery || this.upcomingDraw.lottery_type, 
      this.getDrawDate(this.upcomingDraw)
    ).subscribe({
      next: (response: any) => {
        this.leadingNumbers = response?.data ?? null;
        // Backend should handle sorting, but keep this for compatibility
        if (this.leadingNumbers.section1) {
          this.leadingNumbers.topSection1 = this.leadingNumbers.section1
            .slice(0, 5)
            .map((item: any) => item.number);
        }
        if (this.leadingNumbers.section2) {
          this.leadingNumbers.topSection2 = this.leadingNumbers.section2
            .slice(0, 2)
            .map((item: any) => item.number);
        }
      },
      error: (err) => console.error(err)
    });
  }

  private getDrawDate(draw: any): string {
    const value = draw?.draw_date || draw?.drawDate || '';

    return String(value).split(' ')[0].split('T')[0];
  }

  private hasAuthToken(): boolean {
    return !!(localStorage.getItem('auth_token') || localStorage.getItem('token'));
  }

  private clearAuthState() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }

  private showLogin() {
    this.router.navigate(['/login'], {
      queryParams: {
        returnUrl: this.router.url
      }
    });
  }
}
