import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive, Router, NavigationEnd } from '@angular/router';
import { of } from 'rxjs';
import { LoginComponent } from '../login/login.component';
import { SignupComponent } from '../signup/signup.component';
import { PasswordResetComponent } from '../password-reset/password-reset.component';
import { filter } from 'rxjs/operators';
import { timeout, catchError } from 'rxjs/operators';
import { BackendService } from '../../../util/backend.service';
import { ApiResponse } from '../../../util/api-response';
import { Draw } from '../../../lotteries/draw';

@Component({
  selector: 'app-navbar',
  imports: [CommonModule, RouterLink, RouterLinkActive, LoginComponent, SignupComponent,  PasswordResetComponent ],
  templateUrl: './navbar.component.html',
  styleUrl: './navbar.component.css'
})
export class NavbarComponent implements OnInit {
  totalPoolMoney = 0;
  isLoggedIn = false;
  userEmail = '';
  currentUser: any = null;
  showLoginModal = false;
  showSignupModal = false;
  verificationMode = false;
  passwordRecoveryMode = false;
  mobileMenuOpen = false;
  isLoading = true;
  isScrolled = false;
  notificationCount = 0;
  showNotifications = false;
  notifications: any[] = [];

  showPasswordResetModal = false;

  isPlayLotteryPage = false;

  constructor(private backendService: BackendService, private router: Router) {}

  ngOnInit() {
    this.checkAuthStatus();
    
    // Load cached jackpot immediately
    const cached = localStorage.getItem('cachedJackpot');
    if (cached) {
      this.totalPoolMoney = parseFloat(cached);
    }
    
    this.updatePoolMoney();
    setInterval(() => this.updatePoolMoney(), 60000);
    this.initStickyHeader();
    
    // Listen for jackpot update events
    window.addEventListener('jackpotUpdated', () => {
      this.updatePoolMoney();
    });
    
    // Check current route
    this.router.events.pipe(
      filter(event => event instanceof NavigationEnd)
    ).subscribe((event: NavigationEnd) => {
      this.isPlayLotteryPage = event.url.includes('/play-lottery');
    });
    
    // Check initial route
    this.isPlayLotteryPage = this.router.url.includes('/play-lottery');
    
    // Load notifications if logged in
    if (this.isLoggedIn) {
      this.loadNotifications();
      // Check for new notifications every 30 seconds
      setInterval(() => this.loadNotifications(), 30000);
    }
  }

  private initStickyHeader() {
    window.addEventListener('scroll', () => {
      const header = document.querySelector('.header-section');
      this.isScrolled = window.scrollY > 50;
      if (window.scrollY > 100) {
        header?.classList.add('header-active');
      } else {
        header?.classList.remove('header-active');
      }
    });
  }

  private checkAuthStatus() {
    const token = localStorage.getItem('auth_token') || localStorage.getItem('token');
    const user = localStorage.getItem('user');
    console.log('Auth check - token:', token, 'user:', user);
    if (token && user) {
      this.isLoggedIn = true;
      this.currentUser = JSON.parse(user);
      console.log('Current user:', this.currentUser);
      this.userEmail = this.currentUser.email || this.currentUser.phone || 'User';
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
    this.currentUser = user ?? this.currentUser;
    this.userEmail = user?.email || user?.phone || 'User';
    this.showLoginModal = false;
    this.loadNotifications();
  }

  onSignupSuccess(user: any) {
    this.isLoggedIn = true;
    this.currentUser = user;
    this.userEmail = user.email || user.phone || 'User';
    this.showSignupModal = false;
    this.loadNotifications();
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
  this.showSignupModal = false;
  this.showPasswordResetModal = true;
}
onClosePasswordReset() {
  this.showPasswordResetModal = false;
}

  onSwitchToLogin() {
    this.showSignupModal = false;
    this.showLoginModal = true;
  }

  toggleMobileMenu() {
    this.mobileMenuOpen = !this.mobileMenuOpen;
  }

  logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.isLoggedIn = false;
    this.currentUser = null;
    this.userEmail = '';
    this.notificationCount = 0;
    this.showNotifications = false;
  }

  toggleNotifications() {
    this.showNotifications = !this.showNotifications;
    if (this.showNotifications) {
      // Mark notifications as read when opened
      this.markNotificationsAsRead();
    }
  }

  private loadNotifications() {
    if (!this.isLoggedIn) return;
    
    const token = localStorage.getItem('auth_token') || localStorage.getItem('token');
    if (!token) return;
    
    this.checkUserCountry();
  }

  private checkUserCountry() {
    const user = this.currentUser;
    if (!user) return;
    
    // Check if user has country field
    if (!user.country || user.country === '' || user.country === null) {
      this.notifications = [{
        type: 'profile_incomplete',
        message: 'Please update your profile to include your country information.',
        action: 'Update Profile',
        read: false
      }];
      this.notificationCount = 1;
    } else {
      this.notifications = [];
      this.notificationCount = 0;
    }
  }

  private markNotificationsAsRead() {
    // Don't reset count for profile incomplete notification
    // User must complete profile to dismiss it
  }

  navigateToProfile() {
    this.showNotifications = false;
    this.router.navigate(['/profile']);
  }

  getUserDisplayName(): string {
    const user = this.currentUser;

    if (!user) {
      return 'User';
    }

    if (user.fullName) {
      return user.fullName;
    }

    const firstName = user.first_name || user.firstName || '';
    const lastName = user.last_name || user.lastName || '';
    const fullName = `${firstName} ${lastName}`.trim();

    return fullName || user.email || user.phone || 'User';
  }

  private updatePoolMoney() {
    this.backendService.getUpcomingDraws().pipe(
      timeout(5000),
      catchError(error => {
        console.error('Error fetching jackpot:', error);
        this.isLoading = false;
        // Try to use cached value
        const cached = localStorage.getItem('cachedJackpot');
        if (cached) {
          this.totalPoolMoney = parseFloat(cached);
        }
        return of({ success: false, message: 'Failed to fetch draws', data: [] } as ApiResponse<Draw[]>);
      })
    ).subscribe({
      next: (response: ApiResponse<Draw[]>) => {
        this.isLoading = false;
        const draws = response?.data ?? [];

        if (draws.length > 0) {
          const jackpotValue = String(draws[0].jackpot ?? '0');
          const jackpot = parseFloat(jackpotValue.replace('$', ''));

          this.totalPoolMoney = jackpot;
          // Cache the value
          localStorage.setItem('cachedJackpot', jackpot.toString());
        }
      },
      error: (error) => {
        this.isLoading = false;
        console.error('Error updating pool money:', error);
      }
    });
  }
}
