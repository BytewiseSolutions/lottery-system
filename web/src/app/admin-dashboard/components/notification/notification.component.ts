import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { BackendService } from '../../../util/backend.service';
import { LotteryService } from '../../../services/lottery.service';
import { NotificationFormComponent } from './notification-form/notification-form.component';
import { Notification } from './notification';
import { NotificationCreateRequest } from './notification-request';

@Component({
  selector: 'app-notification',
  imports: [CommonModule, SidebarComponent, NotificationFormComponent],
  templateUrl: './notification.component.html',
  styleUrl: './notification.component.css'
})
export class NotificationComponent implements OnInit {
  notifications: Notification[] = [];
  unreadCount = 0;
  loading = true;
  error = false;
  selectedTab = 'all'; // 'all', 'unread', 'read'
  
  // Create notification modal
  isCreateModalOpen = false;
  creating = false;

  constructor(
    private backendService: BackendService,
    private lotteryService: LotteryService
  ) {}

  ngOnInit() {
    this.loadNotifications();
    this.loadUnreadCount();
  }

  loadNotifications() {
    this.loading = true;
    this.error = false;
    
    this.backendService.getNotifications().subscribe({
      next: (response: any) => {
        if (response.success) {
          this.notifications = response.data || [];
          // Recalculate unread count from actual notifications
          this.recalculateUnreadCount();
        } else {
          this.notifications = [];
          console.error('Failed to load notifications:', response.message);
        }
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading notifications:', error);
        this.loading = false;
        this.error = true;
      }
    });
  }

  recalculateUnreadCount() {
    this.unreadCount = this.notifications.filter(n => !n.is_read).length;
  }

  loadUnreadCount() {
    this.backendService.getUnreadNotificationCount().subscribe({
      next: (response: any) => {
        if (response.success && response.data) {
          const serverUnreadCount = response.data.count || 0;
          console.log('Server unread count:', serverUnreadCount);
          console.log('Local unread count:', this.unreadCount);
          // Use server count as the source of truth
          this.unreadCount = serverUnreadCount;
        }
      },
      error: (error) => {
        console.error('Error loading unread count:', error);
      }
    });
  }

  markAsRead(notification: Notification) {
    if (!notification.is_read) {
      this.backendService.markNotificationAsRead(notification.id).subscribe({
        next: (response: any) => {
          if (response.success) {
            notification.is_read = true;
            this.unreadCount = Math.max(0, this.unreadCount - 1);
            // Also reload unread count from server to ensure accuracy
            this.loadUnreadCount();
          } else {
            console.error('Failed to mark notification as read:', response.message);
          }
        },
        error: (error) => {
          console.error('Error marking notification as read:', error);
        }
      });
    }
  }

  markAllAsRead() {
    const unreadNotifications = this.notifications.filter(n => !n.is_read);
    
    if (unreadNotifications.length === 0) {
      return;
    }

    // Mark all unread notifications as read locally first
    unreadNotifications.forEach(notification => {
      notification.is_read = true;
    });
    
    // Update unread count
    this.unreadCount = 0;
    
    // TODO: Implement bulk mark as read API endpoint
    // For now, mark each one individually
    unreadNotifications.forEach(notification => {
      this.backendService.markNotificationAsRead(notification.id).subscribe({
        error: (error) => {
          console.error('Error marking notification as read:', error);
          // Revert on error
          notification.is_read = false;
          this.loadUnreadCount();
        }
      });
    });
  }

  getFilteredNotifications(): Notification[] {
    switch (this.selectedTab) {
      case 'unread':
        return this.notifications.filter(n => !n.is_read);
      case 'read':
        return this.notifications.filter(n => n.is_read);
      default:
        return this.notifications;
    }
  }

  getNotificationIcon(type: string): string {
    switch (type) {
      case 'success': return '✅';
      case 'warning': return '⚠️';
      case 'error': return '❌';
      case 'info': return 'ℹ️';
      default: return '📢';
    }
  }

  getNotificationClass(type: string): string {
    switch (type) {
      case 'success': return 'notification-success';
      case 'warning': return 'notification-warning';
      case 'error': return 'notification-error';
      case 'info': return 'notification-info';
      default: return 'notification-default';
    }
  }

  formatDate(dateString: string): string {
    try {
      const date = new Date(dateString);
      return date.toLocaleString();
    } catch {
      return dateString;
    }
  }

  refreshNotifications() {
    this.loadNotifications();
    this.loadUnreadCount();
  }

  openCreateModal() {
    this.isCreateModalOpen = true;
  }

  closeCreateModal() {
    this.isCreateModalOpen = false;
    this.creating = false;
  }

  onCreateNotification(formData: NotificationCreateRequest) {
    this.creating = true;

    this.backendService.createNotification(formData).subscribe({
      next: (response: any) => {
        if (response.success) {
          console.log('Notification created successfully');
          this.closeCreateModal();
          this.refreshNotifications();
        } else {
          console.error('Failed to create notification:', response.message);
          alert('Failed to create notification: ' + response.message);
        }
        this.creating = false;
      },
      error: (error) => {
        console.error('Error creating notification:', error);
        alert('Error creating notification');
        this.creating = false;
      }
    });
  }
}
