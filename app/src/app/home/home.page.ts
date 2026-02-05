import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { LotteryService } from '../services/lottery.service';
import { ToastService } from '../services/toast.service';
import { NotificationService } from '../services/notification.service';

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
  standalone: false,
})
export class HomePage implements OnInit, OnDestroy {
  userName = '';
  selectedTab = 'home';
  upcomingDraws: any[] = [];
  totalWinnings = 0;
  unreadNotifications = 0;
  private countdownInterval: any;

  constructor(
    private auth: AuthService,
    private router: Router,
    private lottery: LotteryService,
    private notificationService: NotificationService,
    private toast: ToastService
  ) {}

  ngOnInit() {
    if (!this.auth.isAuthenticated()) {
      this.router.navigate(['/login']);
      return;
    }
    
    this.auth.user$.subscribe(user => {
      if (user) {
        this.userName = user.fullName || user.name || 'Player';
      }
    });
    
    this.notificationService.unreadCount$.subscribe(count => {
      this.unreadNotifications = count;
    });
    
    this.loadUpcomingDraws();
    this.countdownInterval = setInterval(() => {
      setTimeout(() => {
        this.upcomingDraws = [...this.upcomingDraws];
      });
    }, 1000);
    
    window.addEventListener('entrySubmitted', () => this.loadUpcomingDraws());
  }

  loadUpcomingDraws() {
    this.lottery.getUpcomingDraws().subscribe({
      next: (response: any) => {
        this.upcomingDraws = Array.isArray(response) ? response.slice(0, 3) : (response.draws || []).slice(0, 3);
        if (this.upcomingDraws.length > 0) {
          this.totalWinnings = parseFloat(this.upcomingDraws[0].jackpot) || 0;
        }
      },
      error: () => this.toast.showError('Failed to load draws')
    });
  }

  playLottery(draw: any) {
    this.router.navigate(['/play'], { state: { lottery: draw } });
  }

  getCountdown(drawDate: string): string {
    const now = new Date().getTime();
    const draw = new Date(drawDate).getTime();
    const diff = draw - now;

    if (diff <= 0) return '00 Days 00:00:00';

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    return `${String(days).padStart(2, '0')} Days ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
  }

  ngOnDestroy() {
    if (this.countdownInterval) clearInterval(this.countdownInterval);
  }

  handleRefresh(event: any) {
    this.loadUpcomingDraws();
    
    // Trigger refresh on child components
    if (this.selectedTab === 'entries' || this.selectedTab === 'results') {
      window.dispatchEvent(new CustomEvent('refreshData'));
    }
    
    setTimeout(() => {
      event.target.complete();
    }, 1000);
  }

  logout() {
    this.toast.confirm('Are you sure you want to logout?', 'Logout').then(confirmed => {
      if (confirmed) {
        this.auth.logout();
        this.router.navigate(['/landing']);
      }
    });
  }
}
