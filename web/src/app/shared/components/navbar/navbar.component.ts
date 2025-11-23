import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { LotteryService } from '../../../services/lottery.service';
import { LoginComponent } from '../login/login.component';
import { SignupComponent } from '../signup/signup.component';

@Component({
  selector: 'app-navbar',
  imports: [CommonModule, RouterLink, RouterLinkActive, LoginComponent, SignupComponent],
  templateUrl: './navbar.component.html',
  styleUrl: './navbar.component.css'
})
export class NavbarComponent implements OnInit {
  totalPoolMoney = 0;
  isLoggedIn = false;
  userEmail = '';
  showLoginModal = false;
  showSignupModal = false;
  mobileMenuOpen = false;

  constructor(private lotteryService: LotteryService) {}

  ngOnInit() {
    this.checkAuthStatus();
    this.updatePoolMoney();
    setInterval(() => this.updatePoolMoney(), 30000);
    this.initStickyHeader();
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
    alert('Logged out successfully!');
  }

  private updatePoolMoney() {
    this.lotteryService.getDraws().subscribe({
      next: (draws) => {
        this.totalPoolMoney = draws.reduce((total, draw) => {
          const amount = parseFloat(draw.jackpot.replace('$', '')) || 0;
          return total + amount;
        }, 0);
      },
      error: (error) => {
        console.error('Error updating pool money:', error);
        this.totalPoolMoney = 0;
      }
    });
  }
}