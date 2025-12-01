import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { LayoutComponent } from '../layout/layout.component';
import { LoginComponent } from '../shared/components/login/login.component';
import { SignupComponent } from '../shared/components/signup/signup.component';
import { LotteryService } from '../services/lottery.service';
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
  lotteryType = 'mon';
  drawDate = '';
  currentSection = 1;
  isLoggedIn = false;
  showLoginModal = false;
  showSignupModal = false;

  constructor(private route: ActivatedRoute, private router: Router, private lotteryService: LotteryService) {}

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      this.lotteryType = params['lottery'] || 'mon';
      this.drawDate = params['drawDate'] || new Date().toISOString().split('T')[0];
    });
    
    // Check login status
    this.isLoggedIn = !!localStorage.getItem('token');
    
    // Generate numbers 1-99
    for (let i = 1; i <= 99; i++) {
      this.numbers.push(i);
    }
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
            drawDate: this.drawDate
          })
        });
        
        const result = await response.json();
        
        if (result.success) {
          alert(`Entry submitted! New pool amount: $${result.newPoolAmount}`);
          // Trigger immediate refresh of lottery balances
          this.lotteryService.refreshDraws();
          // Redirect to lotteries page
          this.router.navigate(['/lotteries']);
        } else {
          alert(result.error || 'Failed to submit entry');
        }
      } catch (error) {
        alert('Failed to submit entry. Please try again.');
      }
    }
  }
}
