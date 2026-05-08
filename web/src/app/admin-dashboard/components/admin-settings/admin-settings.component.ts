import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { BackendService } from '../../../util/backend.service';
import { SuccessPopupService } from '../../../util/success-popup.service';
import { ErrorHandlerService } from '../../../util/error-handler.service';

@Component({
  selector: 'app-admin-settings-page',
  imports: [CommonModule, FormsModule, SidebarComponent],
  templateUrl: './admin-settings.component.html',
  styleUrl: './admin-settings.component.css'
})
export class DashboardSettingsComponent implements OnInit {
  loading = true;
  error = false;
  saving = false;

  settings = {
    site_name: '',
    support_email: '',
    support_phone: '',
    default_draw_jackpot: '10.00',
    maintenance_mode: false,
    registration_enabled: true,
    notifications_enabled: true
  };

  constructor(
    private backendService: BackendService,
    private successPopupService: SuccessPopupService,
    private errorHandlerService: ErrorHandlerService
  ) {}

  ngOnInit(): void {
    this.loadSettings();
  }

  loadSettings(): void {
    this.loading = true;
    this.error = false;

    this.backendService.getSettings().subscribe({
      next: (response: any) => {
        if (!response?.success || !response.data) {
          this.error = true;
          this.loading = false;
          return;
        }

        this.settings = {
          site_name: response.data.site_name || '',
          support_email: response.data.support_email || '',
          support_phone: response.data.support_phone || '',
          default_draw_jackpot: String(response.data.default_draw_jackpot || '10.00'),
          maintenance_mode: !!response.data.maintenance_mode,
          registration_enabled: !!response.data.registration_enabled,
          notifications_enabled: !!response.data.notifications_enabled
        };
        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load system settings:', error);
        this.error = true;
        this.loading = false;
      }
    });
  }

  retryLoad(): void {
    this.loadSettings();
  }

  saveSettings(): void {
    this.saving = true;

    this.backendService.updateSettings(this.settings).subscribe({
      next: (response: any) => {
        this.saving = false;

        if (!response?.success || !response.data) {
          this.errorHandlerService.showError(response?.message || 'Failed to update settings');
          return;
        }

        this.settings = {
          site_name: response.data.site_name || '',
          support_email: response.data.support_email || '',
          support_phone: response.data.support_phone || '',
          default_draw_jackpot: String(response.data.default_draw_jackpot || '10.00'),
          maintenance_mode: !!response.data.maintenance_mode,
          registration_enabled: !!response.data.registration_enabled,
          notifications_enabled: !!response.data.notifications_enabled
        };

        this.successPopupService.show('System settings updated successfully.', 'Settings Updated');
      },
      error: (error) => {
        console.error('Failed to update system settings:', error);
        this.saving = false;
        this.errorHandlerService.showError(error?.error?.message || 'Failed to update settings');
      }
    });
  }
}
