import { Component, OnInit } from '@angular/core';
import { BackendService } from '../util/backend.service';
import { LayoutComponent } from '../layout/layout.component';

interface UserNotification {
  id: number;
  title: string;
  message: string;
  type: string;
  is_read: boolean;
  created_at: string;
}

@Component({
  selector: 'app-notifications',
  imports: [LayoutComponent],
  templateUrl: './notifications.component.html',
  styleUrl: './notifications.component.css'
})
export class NotificationsComponent implements OnInit {
  notifications: UserNotification[] = [];
  loading = true;
  error = false;
  isLoggedIn = false;

  constructor(private backendService: BackendService) {}

  ngOnInit() {
    const token = localStorage.getItem('auth_token') || localStorage.getItem('token');
    this.isLoggedIn = !!token;
    this.loadNotifications();
  }

  loadNotifications() {
    this.loading = true;
    this.error = false;

    const request = this.isLoggedIn
      ? this.backendService.getNotifications()
      : this.backendService.getPublicNotifications();

    request.subscribe({
      next: (response: any) => {
        if (response?.success) {
          this.notifications = response.data || [];
          if (this.isLoggedIn) {
            this.backendService.markAllNotificationsAsRead().subscribe();
            this.notifications.forEach(n => n.is_read = true);
          } else {
            const ids = this.notifications.map(n => n.id);
            localStorage.setItem('read_notification_ids', JSON.stringify(ids));
          }
        }
        this.loading = false;
      },
      error: (err) => {
        if (err.status === 401) {
          // token expired, fall back to public
          this.isLoggedIn = false;
          this.backendService.getPublicNotifications().subscribe({
            next: (res: any) => {
              if (res?.success) this.notifications = res.data || [];
              this.loading = false;
            },
            error: () => { this.error = true; this.loading = false; }
          });
        } else {
          this.error = true;
          this.loading = false;
        }
      }
    });
  }

  markAsRead(notification: UserNotification) {
    if (!this.isLoggedIn || notification.is_read) return;

    notification.is_read = true;
    this.backendService.markNotificationAsRead(notification.id).subscribe({
      error: () => { notification.is_read = false; }
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

  getUnreadCount(): number {
    if (!this.isLoggedIn) return this.notifications.length;
    return this.notifications.filter(n => !n.is_read).length;
  }

  formatDate(dateString: string): string {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleString('en-GB', {
      day: 'numeric', month: 'long', year: 'numeric',
      hour: '2-digit', minute: '2-digit', hour12: false
    });
  }
}
