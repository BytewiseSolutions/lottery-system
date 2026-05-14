import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { LayoutComponent } from '../layout/layout.component';
import { SuccessPopupService } from '../util/success-popup.service';
import { BackendService } from '../util/backend.service';
import { ErrorHandlerService } from '../util/error-handler.service';

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
    private backendService: BackendService,
    private errorHandlerService: ErrorHandlerService,
    private successPopupService: SuccessPopupService,
    private router: Router
  ) {}

  ngOnInit() {}

  changePassword() {
    if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
      this.errorHandlerService.showError('New passwords do not match');
      return;
    }

    if (this.passwordForm.newPassword.length < 6) {
      this.errorHandlerService.showError('Password must be at least 6 characters');
      return;
    }

    this.isChangingPassword = true;

    this.backendService.changeCurrentPassword({
      current_password: this.passwordForm.currentPassword,
      new_password: this.passwordForm.newPassword,
      confirm_password: this.passwordForm.confirmPassword
    }).subscribe({
      next: (response: any) => {
        this.isChangingPassword = false;

        if (response?.success) {
          this.successPopupService.show('Your password has been changed successfully!', 'Password Changed');
          this.passwordForm = {
            currentPassword: '',
            newPassword: '',
            confirmPassword: ''
          };
          return;
        }

        this.errorHandlerService.showError(response?.message || 'Failed to change password');
      },
      error: (error) => {
        this.isChangingPassword = false;
        this.errorHandlerService.showError(error?.error?.message || 'Failed to change password');
      }
    });
  }

  confirmDeleteAccount() {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
      this.deleteAccount();
    }
  }

  deleteAccount() {
    this.backendService.deleteCurrentAccount().subscribe({
      next: (response: any) => {
        if (response?.success) {
          this.successPopupService.show('Your account has been deleted successfully.', 'Account Deleted');
          this.backendService.clearAuthSession();
          setTimeout(() => this.router.navigate(['/']), 1000);
          return;
        }
        this.errorHandlerService.showError(response?.message || 'Failed to delete account');
      },
      error: (error: any) => {
        if (error?.status === 401) {
          this.backendService.clearAuthSession();
          this.router.navigate(['/']);
          return;
        }
        this.errorHandlerService.showError(error?.error?.message || 'Failed to delete account');
      }
    });
  }
}
