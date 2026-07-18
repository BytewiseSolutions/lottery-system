import { Component, OnInit, OnDestroy, HostListener } from '@angular/core';
import { Router } from '@angular/router';
import { BackendService } from '../../../util/backend.service';

interface UserNotification {
  id: number;
  title: string;
  message: string;
  type: string;
  is_read: boolean;
  created_at: string;
}

@Component({
  selector: 'app-notification',
  imports: [],
  templateUrl: './notification.component.html',
  styleUrl: './notification.component.css'
})
export class NotificationComponent implements OnInit, OnDestroy {
  unreadCount = 0;
  private pollingIntervalId?: number;

  constructor(
    private backendService: BackendService,
    private router: Router
  ) {}

  ngOnInit() {
    this.loadNotifications();
    this.pollingIntervalId = window.setInterval(() => this.loadNotifications(), 30000);
  }

  ngOnDestroy() {
    if (this.pollingIntervalId) {
      window.clearInterval(this.pollingIntervalId);
    }
  }

  @HostListener('document:click', ['$event'])
  onDocumentClick(event: Event) {}

  toggleDropdown() {
    this.router.navigate(['/notifications']);
  }

  loadNotifications() {
    const token = localStorage.getItem('auth_token') || localStorage.getItem('token');

    const request = token
      ? this.backendService.getNotifications()
      : this.backendService.getPublicNotifications();

    request.subscribe({
      next: (response: any) => {
        if (response?.success) {
          const notifications = response.data || [];
          if (token) {
            this.unreadCount = notifications.filter((n: UserNotification) => !n.is_read).length;
          } else {
            const readIds: number[] = JSON.parse(localStorage.getItem('read_notification_ids') || '[]');
            this.unreadCount = notifications.filter((n: UserNotification) => !readIds.includes(n.id)).length;
          }
        }
      },
      error: () => {}
    });
  }

  getIcon(type: string): string {
    switch (type) {
      case 'success': return '✅';
      case 'warning': return '⚠️';
      case 'error': return '❌';
      default: return 'ℹ️';
    }
  }

  formatDate(dateString: string): string {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleString('en-GB', {
      day: 'numeric', month: 'short', year: 'numeric',
      hour: '2-digit', minute: '2-digit', hour12: false
    });
  }
}
