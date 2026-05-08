import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { BackendService } from '../../../util/backend.service';
import { ActivityLog } from '../../logs';
import { SidebarComponent } from '../../sidebar/sidebar.component';

@Component({
  selector: 'app-activity-log',
  imports: [CommonModule, SidebarComponent],
  templateUrl: './activity-log.component.html',
  styleUrl: './activity-log.component.css'
})
export class ActivityLogComponent implements OnInit {
  activityLogs: ActivityLog[] = [];
  paginatedLogs: ActivityLog[] = [];
  loading = true;
  error: string | null = null;
  currentPage = 1;
  itemsPerPage = 20;
  totalItems = 0;
  totalPages = 0;

  constructor(private backendService: BackendService, private router: Router) {}

  ngOnInit() {
    this.loadActivityLogs();
  }

  loadActivityLogs() {
    this.loading = true;
    this.error = null;

    this.backendService.getActivityLogs(100).subscribe({
      next: (response: any) => {
        if (response.success) {
          this.activityLogs = response.data;
          this.totalItems = response.data.length;
          this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
          this.updatePaginatedLogs();
        } else {
          this.error = response.message;
        }
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Failed to load activity logs';
        this.loading = false;
        console.error('Activity logs error:', err);
      }
    });
  }

  updatePaginatedLogs() {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const endIndex = startIndex + this.itemsPerPage;
    this.paginatedLogs = this.activityLogs.slice(startIndex, endIndex);
  }

  onPageChange(page: number) {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.updatePaginatedLogs();
    }
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
      case 'user_create':
        return 'fas fa-user-plus';
      case 'user_update':
      case 'profile_update':
        return 'fas fa-user-pen';
      case 'user_password_reset':
      case 'password_change':
        return 'fas fa-key';
      case 'user_status_update':
        return 'fas fa-user-lock';
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
    const userName = activity.user_name || 'Unknown user';
    
    // Try to parse details as JSON to extract meaningful information
    let parsedDetails = null;
    try {
      if (activity.details && activity.details.startsWith('{')) {
        parsedDetails = JSON.parse(activity.details);
      }
    } catch (e) {
      // If parsing fails, use the original details
    }
    
    switch (action) {
      case 'user_registered':
      case 'register':
      case 'user_registration':
        return `${userName} registered`;
      
      case 'login':
      case 'user_login':
        return `${userName} logged in`;
      
      case 'logout':
      case 'user_logout':
        return `${userName} logged out`;

      case 'user_create':
        return activity.details || `${userName} created a user account`;

      case 'user_update':
        return activity.details || `${userName} updated a user account`;

      case 'user_password_reset':
        return activity.details || `${userName} reset a user password`;

      case 'user_status_update':
        return activity.details || `${userName} updated a user status`;

      case 'profile_update':
        return activity.details || `${userName} updated their profile`;

      case 'password_change':
        return activity.details || `${userName} changed their password`;
      
      case 'vote_submitted':
      case 'vote':
      case 'vote_cast':
      case 'vote_submit':
        if (parsedDetails && parsedDetails.lottery && parsedDetails.draw_date) {
          return `Vote submitted for ${parsedDetails.lottery} of ${parsedDetails.draw_date}`;
        }
        return `${userName} submitted a vote`;
      
      case 'entry_submitted':
      case 'entry':
      case 'lottery_entry':
      case 'entry_submit':
        if (parsedDetails && parsedDetails.lottery && parsedDetails.draw_date) {
          return `Entry submitted for ${parsedDetails.lottery} of ${parsedDetails.draw_date}`;
        }
        return `${userName} submitted lottery entry`;
      
      case 'winner_announced':
      case 'winner':
      case 'winner_selected':
        return `Winner announced: ${userName}`;
      
      case 'payment_processed':
      case 'payment':
      case 'payment_completed':
        return `Payment processed for ${userName}`;
      
      case 'draw_created':
      case 'draw':
        return 'New draw created';
      
      case 'result_published':
      case 'result':
        return 'Results published';
      
      default:
        const formattedAction = activity.action.replace(/_/g, ' ');
        return `${userName}: ${formattedAction}`;
    }
  }

  goBack() {
    this.router.navigate(['/admin-dashboard']);
  }

  trackByFn(index: number, item: ActivityLog): number {
    return item.id;
  }

  formatDateTime(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  formatAction(action: string): string {
    const actionLower = action.toLowerCase();

    switch (actionLower) {
      case 'user_create':
        return 'User Created';
      case 'user_update':
        return 'User Updated';
      case 'user_password_reset':
        return 'Password Reset';
      case 'user_status_update':
        return 'User Status Updated';
      case 'profile_update':
        return 'Profile Updated';
      case 'password_change':
        return 'Password Changed';
      default:
        return action.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
    }
  }

  getActionClass(action: string): string {
    const actionLower = action.toLowerCase();
    if (actionLower.includes('login')) return 'login';
    if (actionLower.includes('logout')) return 'logout';
    if (actionLower.includes('register')) return 'register';
    if (actionLower.includes('play') || actionLower.includes('entry')) return 'play';
    if (actionLower.includes('delete')) return 'delete';
    if (actionLower.includes('update') || actionLower.includes('edit')) return 'update';
    if (actionLower.includes('password')) return 'update';
    if (actionLower.includes('profile')) return 'update';
    if (actionLower.includes('payment')) return 'payment';
    if (actionLower.includes('winner')) return 'winner';
    return 'default';
  }
}
