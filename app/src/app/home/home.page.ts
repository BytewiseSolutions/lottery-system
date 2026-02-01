import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { LotteryService } from '../services/lottery.service';

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
  standalone: false,
})
export class HomePage implements OnInit {
  userName = '';
  selectedTab = 'home';
  upcomingDraws: any[] = [];
  totalWinnings = 0;
  private countdownInterval: any;

  constructor(
    private auth: AuthService,
    private router: Router,
    private lottery: LotteryService
  ) {}

  ngOnInit() {
    // Check if user is authenticated
    if (!this.auth.isAuthenticated()) {
      this.router.navigate(['/login']);
      return;
    }
    
    this.auth.user$.subscribe(user => {
      if (user) {
        this.userName = user.name || 'Player';
      }
    });
    this.loadUpcomingDraws();
    this.countdownInterval = setInterval(() => {
      this.upcomingDraws = [...this.upcomingDraws];
    }, 1000);
  }

  loadUpcomingDraws() {
    this.lottery.getUpcomingDraws().subscribe({
      next: (response: any) => {
        this.upcomingDraws = Array.isArray(response) ? response.slice(0, 3) : (response.draws || []).slice(0, 3);
        if (this.upcomingDraws.length > 0) {
          this.totalWinnings = parseFloat(this.upcomingDraws[0].jackpot) || 0;
        }
      },
      error: (err) => console.error('Error loading draws:', err)
    });
  }

  loadWinnings() {
    // Removed - using next draw jackpot instead
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

  playLottery(draw: any) {
    this.router.navigate(['/play'], { state: { lottery: draw } });
  }

  logout() {
    this.auth.logout();
    this.router.navigate(['/landing']);
  }
}
