import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { BackendService } from '../../../util/backend.service';
import { SuccessPopupService } from '../../../util/success-popup.service';
import { UserFormComponent } from '../user/user-form/user-form.component';
import { UserFormValue } from '../user/value';

@Component({
  selector: 'app-admin-profile',
  imports: [CommonModule, SidebarComponent, UserFormComponent],
  templateUrl: './profile.component.html',
  styleUrl: './profile.component.css'
})
export class AdminProfileComponent implements OnInit {
  loading = true;
  error = false;
  saving = false;
  isEditModalOpen = false;
  formError = '';

  profile = {
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    country: '',
    role: '',
    is_active: true,
    created_at: '',
    updated_at: ''
  };

  originalProfile = { ...this.profile };

  constructor(
    private backendService: BackendService,
    private successPopupService: SuccessPopupService
  ) {}

  ngOnInit(): void {
    this.loadProfile();
  }

  loadProfile(): void {
    this.loading = true;
    this.error = false;

    this.backendService.getUserProfile().subscribe({
      next: (response: any) => {
        if (!response?.success || !response.data) {
          this.error = true;
          this.loading = false;
          return;
        }

        this.applyProfile(response.data);
        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load admin profile:', error);
        this.error = true;
        this.loading = false;
      }
    });
  }

  retryLoad(): void {
    this.loadProfile();
  }

  startEdit(): void {
    this.formError = '';
    this.isEditModalOpen = true;
  }

  cancelEdit(): void {
    if (this.saving) {
      return;
    }

    this.formError = '';
    this.isEditModalOpen = false;
  }

  saveProfile(formValue: UserFormValue): void {
    this.saving = true;
    this.formError = '';

    this.backendService.updateUserProfile({
      first_name: formValue.first_name,
      last_name: formValue.last_name,
      email: formValue.email,
      phone: formValue.phone,
      country: formValue.country
    }).subscribe({
      next: (response: any) => {
        this.saving = false;

        if (!response?.success || !response.data) {
          this.formError = response?.message || 'Failed to update profile';
          return;
        }

        this.applyProfile(response.data);
        this.isEditModalOpen = false;
        this.successPopupService.show('Your profile information has been updated successfully!', 'Profile Updated');
      },
      error: (error) => {
        console.error('Failed to update admin profile:', error);
        this.saving = false;
        this.formError = error?.error?.message || 'Failed to update profile';
      }
    });
  }

  getStatusLabel(): string {
    return this.profile.is_active ? 'Active' : 'Inactive';
  }

  getRoleLabel(): string {
    const role = (this.profile.role || 'admin').toLowerCase();
    return role.charAt(0).toUpperCase() + role.slice(1);
  }

  formatDateTime(value?: string): string {
    if (!value) {
      return 'Not available';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
      return value;
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

  get profileFormValue(): Partial<UserFormValue> {
    return {
      first_name: this.profile.first_name,
      last_name: this.profile.last_name,
      email: this.profile.email,
      phone: this.profile.phone,
      country: this.profile.country,
      password: '',
      role: this.profile.role || 'admin',
      is_active: this.profile.is_active
    };
  }

  private applyProfile(data: any): void {
    this.profile = {
      first_name: data.first_name || '',
      last_name: data.last_name || '',
      email: data.email || '',
      phone: data.phone || '',
      country: data.country || '',
      role: data.role || 'admin',
      is_active: Number(data.is_active) === 1 || data.is_active === true,
      created_at: data.created_at || '',
      updated_at: data.updated_at || ''
    };

    this.originalProfile = { ...this.profile };

    const storedUser = JSON.parse(localStorage.getItem('user') || '{}');
    localStorage.setItem('user', JSON.stringify({
      ...storedUser,
      first_name: this.profile.first_name,
      last_name: this.profile.last_name,
      full_name: `${this.profile.first_name} ${this.profile.last_name}`.trim(),
      fullName: `${this.profile.first_name} ${this.profile.last_name}`.trim(),
      email: this.profile.email,
      phone: this.profile.phone,
      country: this.profile.country,
      role: this.profile.role
    }));
  }
}
