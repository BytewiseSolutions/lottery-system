import { Component, OnInit } from '@angular/core';
import { AuthService } from '../services/auth.service';
import { ToastService } from '../services/toast.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-settings',
  templateUrl: './settings.page.html',
  styleUrls: ['./settings.page.scss'],
  standalone: false,
})
export class SettingsPage implements OnInit {
  profile: any = {};
  notificationEnabled = true;
  loading = false;

  constructor(
    private auth: AuthService,
    private toast: ToastService,
    private router: Router
  ) {}

  ngOnInit() {
    this.loadProfile();
  }

  loadProfile() {
    this.auth.getProfile().subscribe({
      next: (data) => {
        this.profile = data;
        this.notificationEnabled = data.notification_enabled === 1;
      },
      error: () => this.toast.showError('Failed to load profile')
    });
  }

  toggleNotifications() {
    this.auth.updateNotificationPreferences(this.notificationEnabled).subscribe({
      next: () => this.toast.showSuccess('Preferences updated'),
      error: () => {
        this.notificationEnabled = !this.notificationEnabled;
        this.toast.showError('Update failed');
      }
    });
  }

  openTerms() {
    this.router.navigate(['/terms']);
  }

  openPrivacy() {
    this.router.navigate(['/privacy']);
  }

  goBack() {
    this.router.navigate(['/home']);
  }
}
