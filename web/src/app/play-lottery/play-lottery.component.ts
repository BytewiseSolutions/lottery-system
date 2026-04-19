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

  constructor(
    private route: ActivatedRoute, 
    private router: Router,
     private lotteryService: LotteryService,
      private toastService: ToastService
    ) {}

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
    
    // Load reCAPTCHA script if not loaded
    if (!(window as any).grecaptcha) {
      const script = document.createElement('script');
      script.src = 'https://www.google.com/recaptcha/api.js';
      script.async = true;
      script.defer = true;
      document.head.appendChild(script);
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
      // Let backend handle sorting when needed
    }
  }

  toggleBonusNumber(num: number) {
    // Check if number is already selected in main numbers
    if (this.selectedNumbers.includes(num)) {
      this.toastService.showError(`Number ${num} is already selected in main numbers. Please choose a different number.`);
      return;
    }
    
    const index = this.selectedBonusNumbers.indexOf(num);
    if (index > -1) {
      this.selectedBonusNumbers.splice(index, 1);
    } else if (this.selectedBonusNumbers.length < 2) {
      this.selectedBonusNumbers.push(num);
      // Let backend handle sorting when needed
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
    // Wait for DOM to update, then scroll to section header
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
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 8000); 
        
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
          }),
          signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        const result = await response.json();
        
        if (result.requireHumanVerification) {
          this.humanVerified = false;
          this.captchaVerified = false;
          this.showHumanVerification = true;
          this.isLoading = false;
          
          const renderCaptcha = () => {
            const container = document.getElementById('recaptcha-container');
            if (container && (window as any).grecaptcha && (window as any).grecaptcha.render) {
              try {
                container.innerHTML = '';
                (window as any).grecaptcha.render('recaptcha-container', {
                  'sitekey': '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI',
                  'callback': (token: string) => {
                    this.captchaVerified = true;
                  }
                });
              } catch (e) {
                console.error('reCAPTCHA render error:', e);
              }
            } else {
              setTimeout(renderCaptcha, 200);
            }
          };
          setTimeout(renderCaptcha, 300);
          return;
        }
        
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
          this.toastService.showError(result.error || 'Failed to submit entry');
        }
      } catch (error: any) {
        if (error.name === 'AbortError') {
          this.toastService.showError('Request timeout. Please try again.');
        } else {
          this.toastService.showError('Network error. Please check your connection.');
        }
      } finally {
        this.isLoading = false;
      }
    }
  }

  onHumanVerified() {
    if (!this.captchaVerified) {
      this.toastService.showError('Please complete the CAPTCHA verification');
      return;
    }
    this.humanVerified = true;
    this.showHumanVerification = false;
    this.submitEntry();
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
    this.router.navigate(['/lotteries']);
  }

  quickPick() {
    // Get quick pick numbers from backend
    this.lotteryService.getQuickPickNumbers('main').subscribe({
      next: (response: any) => {
        if (response.success) {
          this.selectedNumbers = response.numbers;
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
    // Get quick pick bonus numbers from backend, excluding main numbers
    this.lotteryService.getQuickPickNumbers('bonus', this.selectedNumbers).subscribe({
      next: (response: any) => {
        if (response.success) {
          this.selectedBonusNumbers = response.numbers;
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
  
  isNumberSelected(num: number): boolean {
    return this.selectedNumbers.includes(num);
  }
  
  isBonusNumberSelected(num: number): boolean {
    return this.selectedBonusNumbers.includes(num);
  }
  
  isBonusNumberDisabled(num: number): boolean {
    // Disable bonus numbers that are already selected in main numbers
    return this.selectedNumbers.includes(num);
  }
}
