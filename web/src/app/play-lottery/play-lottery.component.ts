import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { LayoutComponent } from '../layout/layout.component';
import { LoginComponent } from '../shared/components/login/login.component';
import { SignupComponent } from '../shared/components/signup/signup.component';
import { BackendService } from '../util/backend.service';
import { ModalService } from '../util/modal.service';

@Component({
  selector: 'app-play-lottery',
  imports: [CommonModule, LayoutComponent, LoginComponent, SignupComponent],
  templateUrl: './play-lottery.component.html',
  styleUrl: './play-lottery.component.css'
})
export class PlayLotteryComponent implements OnInit {
  numbers: number[] = [];
  selectedNumbers: number[] = [];
  selectedBonusNumbers: number[] = [];
  lotteryType = 'monday';
  drawDate = '';
  currentSection = 1;
  isLoggedIn = false;
  showLoginModal = false;
  showSignupModal = false;
  showSuccessPopup = false;
  isLoading = false;

  constructor(
    private route: ActivatedRoute, 
    private router: Router,
    private backendService: BackendService,
    private modalService: ModalService
  ) {}

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      this.lotteryType = params['lottery'] || 'monday';
      this.drawDate = params['drawDate'] || new Date().toISOString().split('T')[0];
    });
    
    this.refreshAuthState();
    
    for (let i = 1; i <= 75; i++) {
      this.numbers.push(i);
    }

    setTimeout(() => {
      const bannerSection = document.querySelector('.banner-section');
      if (bannerSection) {
        bannerSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }, 100);
  }

  toggleNumber(num: number) {
    const index = this.selectedNumbers.indexOf(num);
    if (index > -1) {
      this.selectedNumbers.splice(index, 1);
    } else if (this.selectedNumbers.length < 5) {
      this.selectedNumbers.push(num);
    }
  }

  toggleBonusNumber(num: number) {
    if (this.selectedNumbers.includes(num)) {
      this.modalService.showError(`Number ${num} is already selected in main numbers. Please choose a different number.`, 'Invalid Selection');
      return;
    }
    
    const index = this.selectedBonusNumbers.indexOf(num);
    if (index > -1) {
      this.selectedBonusNumbers.splice(index, 1);
    } else if (this.selectedBonusNumbers.length < 2) {
      this.selectedBonusNumbers.push(num);
    }
  }

  nextSection() {
    if (this.currentSection === 1 && this.selectedNumbers.length === 5) {
      this.currentSection = 2;
      this.scrollToSectionTop();
    } else if (this.currentSection === 2 && this.selectedBonusNumbers.length === 2) {
      this.currentSection = 3;
      this.scrollToSectionTop();
    }
  }

  private scrollToSectionTop() {
    setTimeout(() => {
      const sectionHeader = document.querySelector('.section-header');
      if (sectionHeader) {
        const headerTop = sectionHeader.getBoundingClientRect().top + window.pageYOffset - 80;
        window.scrollTo({ top: headerTop, behavior: 'smooth' });
      }
    }, 100);
  }

  editEntry() {
    this.currentSection = 1;
    this.scrollToSectionTop();
  }

  showLogin() {
    this.router.navigate(['/login'], {
      queryParams: {
        returnUrl: this.router.url
      }
    });
  }

  onLoginSuccess(user: any) {
    this.isLoggedIn = true;
    this.showLoginModal = false;
  }

  onSignupSuccess(user: any) {
    this.isLoggedIn = true;
    this.showSignupModal = false;
  }

  onCloseLogin() {
    this.showLoginModal = false;
  }

  onCloseSignup() {
    this.showSignupModal = false;
  }

  onSwitchToSignup() {
    this.showLoginModal = false;
    this.showSignupModal = true;
  }

  onSwitchToLogin() {
    this.showSignupModal = false;
    this.showLoginModal = true;
  }

  async submitEntry() {
    this.refreshAuthState();

    if (!this.isLoggedIn) {
      this.showLogin();
      return;
    }

    if (!this.isLotteryOpen()) {
      this.modalService.showError('Lottery closed at 7:00 PM. Please wait for the next draw.', 'Lottery Closed');
      return;
    }
    
    if (this.selectedNumbers.length === 5 && this.selectedBonusNumbers.length === 2) {
      this.isLoading = true;
      
      try {
        const result = await new Promise<any>((resolve, reject) => {
          const request = this.backendService.playLottery({
            lottery: this.lotteryType,
            numbers: this.selectedNumbers,
            bonusNumbers: this.selectedBonusNumbers,
            drawDate: this.drawDate
          }).subscribe({
            next: (response) => {
              request.unsubscribe();
              resolve(response);
            },
            error: (error) => {
              request.unsubscribe();
              reject(error);
            }
          });
        });
        
        if (result.success) {
          window.dispatchEvent(new CustomEvent('jackpotUpdated'));
          
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
        } else {
          this.modalService.showError(result.error || 'Failed to submit entry', 'Submission Failed');
        }
      } catch (error: any) {
        if (error?.status === 401) {
          this.clearAuthState();
          this.showLogin();
          return;
        }

        this.modalService.showError(error?.error?.error || error?.error?.message || 'Network error. Please check your connection.', 'Connection Error');
      } finally {
        this.isLoading = false;
      }
    }
  }

  getLotteryName(): string {
    switch(this.lotteryType) {
      case 'mon':
      case 'monday': return 'Monday Lotto';
      case 'wed':
      case 'wednesday': return 'Wednesday Lotto';
      case 'fri':
      case 'friday': return 'Friday Lotto';
      default: return 'Monday Lotto';
    }
  }

  dismissSuccessPopup() {
    this.showSuccessPopup = false;
    this.router.navigate(['/lotteries']);
  }

  private isLotteryOpen(): boolean {
    if (!this.drawDate) {
      return true;
    }

    const drawClose = new Date(this.drawDate);
    drawClose.setHours(19, 0, 0, 0);

    return new Date() <= drawClose;
  }

  quickPick() {
    this.backendService.getQuickPickNumbers('main').subscribe({
      next: (response: any) => {
        if (response.success) {
          this.selectedNumbers = response.numbers || response.data?.numbers || [];
        } else {
          this.modalService.showError('Failed to generate quick pick numbers', 'Quick Pick Error');
        }
      },
      error: (err) => {
        this.modalService.showError('Failed to generate quick pick numbers', 'Quick Pick Error');
        console.error('Quick pick error:', err);
      }
    });
  }

  quickPickBonus() {
    this.backendService.getQuickPickNumbers('bonus', this.selectedNumbers).subscribe({
      next: (response: any) => {
        if (response.success) {
          this.selectedBonusNumbers = response.numbers || response.data?.numbers || [];
        } else {
          this.modalService.showError('Failed to generate quick pick bonus numbers', 'Quick Pick Error');
        }
      },
      error: (err) => {
        this.modalService.showError('Failed to generate quick pick bonus numbers', 'Quick Pick Error');
        console.error('Quick pick bonus error:', err);
      }
    });
  }
  
  isNumberSelected(num: number): boolean {
    return this.selectedNumbers.includes(num);
  }
  
  isBonusNumberSelected(num: number): boolean {
    return this.selectedBonusNumbers.includes(num);
  }
  
  isBonusNumberDisabled(num: number): boolean {
    return this.selectedNumbers.includes(num);
  }

  private refreshAuthState() {
    const token = localStorage.getItem('auth_token');
    const user = localStorage.getItem('user');
    this.isLoggedIn = !!token && !!user;
  }

  private clearAuthState() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    this.isLoggedIn = false;
  }
}
