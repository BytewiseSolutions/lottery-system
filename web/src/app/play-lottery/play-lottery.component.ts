import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { LayoutComponent } from '../layout/layout.component';
import { LoginComponent } from '../shared/components/login/login.component';
import { SignupComponent } from '../shared/components/signup/signup.component';
import { LotteryService } from '../services/lottery.service';
import { ToastService } from '../services/toast.service';
import { environment } from '../../environments/environment';

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
  showHumanVerification = false;
  humanVerified = false;
  captchaVerified = false;
  showSuccessPopup = false;
  isLoading = false;

  constructor(private route: ActivatedRoute, private router: Router, private lotteryService: LotteryService, private toastService: ToastService) {}

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      this.lotteryType = params['lottery'] || 'monday';
      this.drawDate = params['drawDate'] || new Date().toISOString().split('T')[0];
    });
    
    // Check login status
    this.isLoggedIn = !!localStorage.getItem('token');
    
    // Generate numbers 1-75
    for (let i = 1; i <= 75; i++) {
      this.numbers.push(i);
    }
    
    // Setup CAPTCHA callback
    (window as any).onCaptchaSuccess = () => {
      this.captchaVerified = true;
    };
    
    // Scroll to banner section on play-lottery page
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
      this.selectedNumbers.sort((a, b) => a - b);
    }
  }

  toggleBonusNumber(num: number) {
    const index = this.selectedBonusNumbers.indexOf(num);
    if (index > -1) {
      this.selectedBonusNumbers.splice(index, 1);
    } else if (this.selectedBonusNumbers.length < 2) {
      this.selectedBonusNumbers.push(num);
      this.selectedBonusNumbers.sort((a, b) => a - b);
    }
  }

  nextSection() {
    if (this.currentSection === 1 && this.selectedNumbers.length === 5) {
      this.currentSection = 2;
      this.scrollToTop();
    } else if (this.currentSection === 2 && this.selectedBonusNumbers.length === 2) {
      this.currentSection = 3;
      this.scrollToTop();
    }
  }

  private scrollToTop() {
    setTimeout(() => {
      const playSection = document.querySelector('.play-section');
      if (playSection) {
        playSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        // Different scroll offset for section 3 (review) to show full content
        if (this.currentSection === 3) {
          window.scrollBy(0, -250);
        } else {
          window.scrollBy(0, -200);
        }
      }
    }, 100);
  }

  editEntry() {
    this.currentSection = 1;
    this.scrollToTop();
  }

  showLogin() {
    this.showLoginModal = true;
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
    if (!this.isLoggedIn) {
      this.showLogin();
      return;
    }
    
    if (this.selectedNumbers.length === 5 && this.selectedBonusNumbers.length === 2) {
      this.isLoading = true;
      
      try {
        const response = await fetch(`${environment.apiUrl}/play`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify({
            lottery: this.lotteryType,
            numbers: this.selectedNumbers,
            bonusNumbers: this.selectedBonusNumbers,
            drawDate: this.drawDate,
            humanVerified: this.humanVerified
          })
        });
        
        const result = await response.json();
        
        if (result.requireHumanVerification) {
          this.humanVerified = false;
          this.captchaVerified = false;
          this.showHumanVerification = true;
          return;
        }
        
        if (result.success) {
          this.showSuccessPopup = true;
          // Hide popup and redirect after 30 seconds
          setTimeout(() => {
            this.showSuccessPopup = false;
            window.location.href = '/lotteries';
          }, 30000);
        } else {
          this.toastService.showError(result.error || 'Failed to submit entry');
        }
      } catch (error) {
        this.toastService.showError('Network error. Please check your connection.');
      } finally {
        this.isLoading = false;
      }
    }
  }

  onHumanVerified() {
    this.humanVerified = true;
    this.showHumanVerification = false;
    this.submitEntry(); // Retry submission
  }

  onCloseHumanVerification() {
    this.showHumanVerification = false;
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
    window.location.href = '/lotteries';
  }
}
