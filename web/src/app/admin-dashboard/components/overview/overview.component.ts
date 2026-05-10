import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { BackendService } from '../../../util/backend.service';
import { ActivityLog } from '../../logs';
import { DashboardStats } from '../../stats';

@Component({
  selector: 'app-overview',
  imports: [CommonModule],
  templateUrl: './overview.component.html',
  styleUrl: './overview.component.css'
})
export class OverviewComponent implements OnInit {
  stats: DashboardStats = {
    totalUsers: 0,
    totalEntries: 0,
    totalPayouts: 0,
    winnersLastMonth: 0
  };
  
  recentActivity: ActivityLog[] = [];
  loading = true;
  error: string | null = null;

  constructor(
    private backendService: BackendService, 
    private router: Router
  ) {}

  ngOnInit() {
    this.loadDashboardData();
  }

  loadDashboardData() {
    this.loading = true;
    this.error = null;

    this.backendService.getAnalytics().subscribe({
      next: (response: any) => {
        if (response.success) {
          this.stats = response.data;
        } else {
          this.error = response.message;
        }
      },
      error: (err) => {
        this.error = 'Failed to load dashboard statistics';
        console.error('Stats error:', err);
      }
    });

    this.backendService.getActivityLogs(2).subscribe({
      next: (response: any) => {
        if (response.success) {
          this.recentActivity = response.data;
        }
        this.loading = false;
      },
      error: (err) => {
        console.error('Activity error:', err);
        this.loading = false;
      }
    });
  }

  formatCurrency(amount: number): string {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(amount);
  }

  formatNumber(num: number): string {
    return new Intl.NumberFormat('en-US').format(num);
  }

  getTimeAgo(dateString: string): string {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

    if (diffInSeconds < 60) {
      return `${diffInSeconds} seconds ago`;
    } else if (diffInSeconds < 3600) {
      const minutes = Math.floor(diffInSeconds / 60);
      return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) {
      const hours = Math.floor(diffInSeconds / 3600);
      return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else {
      const days = Math.floor(diffInSeconds / 86400);
      return `${days} day${days > 1 ? 's' : ''} ago`;
    }
  }

  getActivityIcon(action: string): string {
    switch (action.toLowerCase()) {
      case 'user_registered':
      case 'register':
      case 'user_registration':
        return 'fas fa-user-plus';
      case 'winner_announced':
      case 'winner':
      case 'winner_selected':
        return 'fas fa-trophy';
      case 'payment_processed':
      case 'payment':
      case 'payment_completed':
        return 'fas fa-money-bill';
      case 'entry_submitted':
      case 'entry':
      case 'lottery_entry':
        return 'fas fa-ticket-alt';
      case 'vote_submitted':
      case 'vote':
      case 'vote_cast':
        return 'fas fa-vote-yea';
      case 'login':
      case 'user_login':
        return 'fas fa-sign-in-alt';
      case 'logout':
      case 'user_logout':
        return 'fas fa-sign-out-alt';
      case 'draw_created':
      case 'draw':
        return 'fas fa-calendar-plus';
      case 'result_published':
      case 'result':
        return 'fas fa-bullhorn';
      default:
        return 'fas fa-info-circle';
    }
  }

  getActivityDescription(activity: ActivityLog): string {
    const action = activity.action.toLowerCase();
    const details = activity.details;
    const userName = activity.user_name || 'Unknown user';
    
    switch (action) {
      case 'user_registered':
      case 'register':
      case 'user_registration':
        return details || `${userName} registered`;
      
      case 'login':
      case 'user_login':
        return details || `${userName} logged in`;
      
      case 'logout':
      case 'user_logout':
        return details || `${userName} logged out`;
      
      case 'vote_submitted':
      case 'vote':
      case 'vote_cast':
        return details || `${userName} submitted a vote`;
      
      case 'entry_submitted':
      case 'entry':
      case 'lottery_entry':
        return details || `${userName} submitted lottery entry`;
      
      case 'winner_announced':
      case 'winner':
      case 'winner_selected':
        return details || `Winner announced: ${userName}`;
      
      case 'payment_processed':
      case 'payment':
      case 'payment_completed':
        return details || `Payment processed for ${userName}`;
      
      case 'draw_created':
      case 'draw':
        return details || 'New draw created';
      
      case 'result_published':
      case 'result':
        return details || 'Results published';
      
      default:
        const formattedAction = activity.action.replace(/_/g, ' ');
        return details || `${userName}: ${formattedAction}`;
    }
  }
  navigateTo(url: string): void {
    this.router.navigate([url]);
  }
  navigateToActivityLog() {
    this.router.navigate(['/admin-dashboard/activity-log']);
  }
}
