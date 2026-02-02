import { Component, OnInit } from '@angular/core';
import { NotificationService } from '../services/notification.service';
import { ToastService } from '../services/toast.service';
import { NavController } from '@ionic/angular';

@Component({
  selector: 'app-notifications',
  templateUrl: './notifications.page.html',
  styleUrls: ['./notifications.page.scss'],
  standalone: false,
})
export class NotificationsPage implements OnInit {
  notifications: any[] = [];
  loading = true;
  error = false;

  constructor(
    private notificationService: NotificationService,
    private navCtrl: NavController,
    private toast: ToastService
  ) { }

  ngOnInit() {
    this.loadNotifications();
  }

  loadNotifications() {
    this.loading = true;
    this.error = false;
    this.notificationService.getNotifications().subscribe({
      next: (data) => {
        this.notifications = data.notifications || [];
        this.loading = false;
      },
      error: () => {
        this.loading = false;
        this.error = true;
        this.toast.showError('Failed to load notifications');
      }
    });
  }

  markAsRead(notification: any) {
    if (!notification.is_read) {
      this.notificationService.markAsRead(notification.id).subscribe({
        next: () => notification.is_read = true,
        error: () => this.toast.showError('Failed to mark as read')
      });
    }
  }

  goBack() {
    this.navCtrl.back();
  }

  handleRefresh(event: any) {
    this.notificationService.getNotifications().subscribe({
      next: (data) => {
        this.notifications = data.notifications || [];
        event.target.complete();
      },
      error: () => {
        event.target.complete();
        this.toast.showError('Failed to refresh notifications');
      }
    });
  }
}
