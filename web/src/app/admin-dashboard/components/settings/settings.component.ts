import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { BackendService } from '../../../util/backend.service';
import { SuccessPopupService } from '../../../services/success-popup.service';
import { ErrorHandlerService } from '../../../services/error-handler.service';

@Component({
  selector: 'app-admin-settings',
  imports: [CommonModule, FormsModule, SidebarComponent],
  templateUrl: './settings.component.html',
  styleUrl: './settings.component.css'
})
export class AdminSettingsComponent {
  passwordForm = {
    current_password: '',
    new_password: '',
    confirm_password: ''
  };

  isChangingPassword = false;

  constructor(
    private backendService: BackendService,
    private successPopupService: SuccessPopupService,
    private errorHandlerService: ErrorHandlerService
  ) {}

  changePassword(): void {
    this.isChangingPassword = true;

    this.backendService.changeCurrentPassword(this.passwordForm).subscribe({
      next: (response: any) => {
        this.isChangingPassword = false;

        if (!response?.success) {
          this.errorHandlerService.showError(response?.message || 'Failed to change password');
          return;
        }

        this.passwordForm = {
          current_password: '',
          new_password: '',
          confirm_password: ''
        };
        this.successPopupService.show('Your password has been changed successfully!', 'Password Changed');
      },
      error: (error) => {
        console.error('Failed to change current password:', error);
        this.isChangingPassword = false;
        this.errorHandlerService.showError(error?.error?.message || 'Failed to change password');
      }
    });
  }
}
