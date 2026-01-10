import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive, Router, NavigationEnd } from '@angular/router';
import { LotteryService } from '../../../services/lottery.service';
import { LoginComponent } from '../login/login.component';
import { SignupComponent } from '../signup/signup.component';
import { filter } from 'rxjs/operators';

@Component({
  selector: 'app-navbar',
  imports: [CommonModule, RouterLink, RouterLinkActive, LoginComponent, SignupComponent],
  templateUrl: './navbar.component.html',
  styleUrl: './navbar.component.css'
})
export class NavbarComponent implements OnInit {
  totalPoolMoney = 30;
  isLoggedIn = false;
  userEmail = '';
  showLoginModal = false;
  showSignupModal = false;
  verificationMode = false;
  passwordRecoveryMode = false;
  mobileMenuOpen = false;
  isLoading = true;

  isPlayLotteryPage = false;

  constructor(private lotteryService: LotteryService, private router: Router) {}

  ngOnInit() {
    this.checkAuthStatus();
    this.updatePoolMoney();
    setInterval(() => this.updatePoolMoney(), 60000);
    this.initStickyHeader();
    
    // Check current route
    this.router.events.pipe(
      filter(event => event instanceof NavigationEnd)
    ).subscribe((event: NavigationEnd) => {
      this.isPlayLotteryPage = event.url.includes('/play-lottery');
    });
    
    // Check initial route
    this.isPlayLotteryPage = this.router.url.includes('/play-lottery');
  }

  private initStickyHeader() {
    window.addEventListener('scroll', () => {
      const header = document.querySelector('.header-section');
      if (window.scrollY > 100) {
        header?.classList.add('header-active');
      } else {
        header?.classList.remove('header-active');
      }
    });
  }

  private checkAuthStatus() {
    const token = localStorage.getItem('token');
    const user = localStorage.getItem('user');
    if (token && user) {
      this.isLoggedIn = true;
      this.userEmail = JSON.parse(user).email;
    }
  }

  openLoginModal() {
    this.showLoginModal = true;
  }

  closeLoginModal() {
    this.showLoginModal = false;
  }

  openRegisterModal() {
    this.showSignupModal = true;
  }

  onLoginSuccess(user: any) {
    this.isLoggedIn = true;
    this.userEmail = user.email;
    this.showLoginModal = false;
  }

  onSignupSuccess(user: any) {
    this.isLoggedIn = true;
    this.userEmail = user.email;
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
    this.verificationMode = false;
  }

  onSwitchToVerification() {
    this.showLoginModal = false;
    this.showSignupModal = true;
    this.verificationMode = true;
    this.passwordRecoveryMode = false;
  }

  onSwitchToPasswordRecovery() {
    this.showLoginModal = false;
    this.showSignupModal = true;
    this.verificationMode = false;
    this.passwordRecoveryMode = true;
  }

  onSwitchToLogin() {
    this.showSignupModal = false;
    this.showLoginModal = true;
  }

  toggleMobileMenu() {
    this.mobileMenuOpen = !this.mobileMenuOpen;
  }

  logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.isLoggedIn = false;
    this.userEmail = '';
  }

  private updatePoolMoney() {
    this.lotteryService.getDraws().subscribe({
      next: (draws) => {
        this.isLoading = false;
        if (draws.length > 0) {
          // Show only the first (next) lottery jackpot, not the total
          this.totalPoolMoney = parseFloat(draws[0].jackpot.replace('$', ''));
        }
      },
      error: (error) => {
        this.isLoading = false;
        console.error('Error updating pool money:', error);
      }
    });
  }
}