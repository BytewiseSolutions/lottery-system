import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { ToastService } from '../services/toast.service';

@Component({
  selector: 'app-change-password',
  templateUrl: './change-password.page.html',
  styleUrls: ['./change-password.page.scss'],
  standalone: false,
})
export class ChangePasswordPage {
  currentPassword = '';
  newPassword = '';
  confirmPassword = '';
  loading = false;

  constructor(
    private auth: AuthService,
    private toast: ToastService,
    private router: Router
  ) {}

  save() {
    if (this.newPassword !== this.confirmPassword) {
      this.toast.showError('Passwords do not match');
      return;
    }
    if (!this.currentPassword || !this.newPassword) {
      this.toast.showError('Please fill all fields');
      return;
    }
    this.loading = true;
    this.auth.changePassword(this.currentPassword, this.newPassword).subscribe({
      next: () => {
        this.toast.showSuccess('Password changed');
        this.router.navigate(['/settings']);
      },
      error: (err) => {
        this.toast.showError(err.error?.error || 'Failed to change password');
        this.loading = false;
      }
    });
  }
}
