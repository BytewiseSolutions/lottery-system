import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { BackendService } from '../../../util/backend.service';
import { NotificationFormComponent } from './notification-form/notification-form.component';
import { Notification } from './notification';
import { NotificationCreateRequest } from './notification-request';
import { SuccessPopupService } from '../../../util/success-popup.service';
import { ErrorHandlerService } from '../../../util/error-handler.service';

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
  selectedTab = 'all'; 
  
  isCreateModalOpen = false;
  creating = false;

  constructor(
    private backendService: BackendService,
    private successPopupService: SuccessPopupService,
    private errorHandlerService: ErrorHandlerService
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

    unreadNotifications.forEach(notification => {
      notification.is_read = true;
    });
    
    this.unreadCount = 0;
    
    unreadNotifications.forEach(notification => {
      this.backendService.markNotificationAsRead(notification.id).subscribe({
        error: (error) => {
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
    const date = new Date(dateString);

    if (Number.isNaN(date.getTime())) {
      return dateString;
    }

    return date.toLocaleString('en-GB', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    });
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
          this.successPopupService.show('Notification created successfully.', 'Notification Sent');
          this.closeCreateModal();
          this.refreshNotifications();
        } else {
          this.errorHandlerService.showError(response?.message || 'Failed to create notification');
        }
        this.creating = false;
      },
      error: (error) => {
        console.error('Error creating notification:', error);
        this.errorHandlerService.showError(error?.error?.message || 'Error creating notification');
        this.creating = false;
      }
    });
  }
}
