import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { LayoutComponent } from '../layout/layout.component';
import { SuccessPopupService } from '../services/success-popup.service';
import { environment } from '../../environments/environment';

@Component({
  selector: 'app-settings',
  imports: [CommonModule, FormsModule, LayoutComponent],
  templateUrl: './settings.component.html',
  styleUrl: './settings.component.css'
})
export class SettingsComponent implements OnInit {
  passwordForm = {
    currentPassword: '',
    newPassword: '',
    confirmPassword: ''
  };

  isChangingPassword = false;

  constructor(
    private successPopupService: SuccessPopupService,
    private router: Router
  ) {}

  ngOnInit() {}

  async changePassword() {
    if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
      alert('New passwords do not match');
      return;
    }

    if (this.passwordForm.newPassword.length < 6) {
      alert('Password must be at least 6 characters');
      return;
    }

    this.isChangingPassword = true;
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    try {
      const response = await fetch(`${environment.apiUrl}/change-password`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          userId: user.id,
          currentPassword: this.passwordForm.currentPassword,
          newPassword: this.passwordForm.newPassword
        })
      });

      const result = await response.json();

      if (result.success) {
        this.successPopupService.show('Your password has been changed successfully!', 'Password Changed');
        this.passwordForm = {
          currentPassword: '',
          newPassword: '',
          confirmPassword: ''
        };
      } else {
        alert(result.error || 'Failed to change password');
      }
    } catch (error) {
      alert('Network error. Please try again.');
    } finally {
      this.isChangingPassword = false;
    }
  }

  confirmDeleteAccount() {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
      this.deleteAccount();
    }
  }

  async deleteAccount() {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    try {
      const response = await fetch(`${environment.apiUrl}/delete-account`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({ userId: user.id })
      });

      const result = await response.json();

      if (result.success) {
        this.successPopupService.show('Your account has been deleted successfully.', 'Account Deleted');
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        setTimeout(() => {
          this.router.navigate(['/']);
        }, 1000);
      } else {
        alert(result.error || 'Failed to delete account');
      }
    } catch (error) {
      alert('Network error. Please try again.');
    }
  }
}
