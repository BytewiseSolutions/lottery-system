import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { BackendService } from '../../../util/backend.service';
import { UserFormComponent } from './user-form/user-form.component';
import { SuccessPopupService } from '../../../services/success-popup.service';
import { ErrorHandlerService } from '../../../services/error-handler.service';
import { UserFormValue } from './value';
import { User } from './user';

@Component({
  selector: 'app-user',
  imports: [CommonModule, FormsModule, SidebarComponent, UserFormComponent],
  templateUrl: './user.component.html',
  styleUrl: './user.component.css'
})
export class UserComponent implements OnInit {
  users: User[] = [];
  loading = true;
  error = false;
  saving = false;
  exporting = false;
  showUserModal = false;
  isEditMode = false;
  currentUser: User | null = null;
  formError = '';
  userForm: Partial<UserFormValue> | null = null;

  currentPage = 1;
  itemsPerPage = 10;
  totalItems = 0;
  totalPages = 0;
  searchTerm = '';
  selectedRole = 'all';
  selectedStatus = 'all';
  sortOrder: 'newest' | 'oldest' = 'newest';

  constructor(
    private backendService: BackendService,
    private router: Router,
    private successPopupService: SuccessPopupService,
    private errorHandlerService: ErrorHandlerService
  ) {}

  ngOnInit(): void {
    this.loadUsers();
  }

  loadUsers(): void {
    this.loading = true;
    this.error = false;

    this.backendService.getUsers({
      page: this.currentPage,
      limit: this.itemsPerPage,
      search: this.searchTerm.trim() || undefined,
      role: this.selectedRole !== 'all' ? this.selectedRole : undefined,
      status: this.selectedStatus !== 'all' ? this.selectedStatus : undefined,
      sort_order: this.sortOrder
    }).subscribe({
      next: (response: any) => {
        this.users = response?.success && Array.isArray(response.data) ? response.data : [];
        const pagination = response?.meta?.pagination || {};

        this.totalItems = Number(pagination.total_items ?? this.users.length);
        this.totalPages = Number(pagination.total_pages ?? 0);
        this.currentPage = Number(pagination.current_page ?? this.currentPage);
        this.itemsPerPage = Number(pagination.per_page ?? this.itemsPerPage);
        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load users:', error);
        this.users = [];
        this.totalItems = 0;
        this.totalPages = 0;
        this.loading = false;
        this.error = true;
      }
    });
  }

  openAddUserModal(): void {
    this.isEditMode = false;
    this.currentUser = null;
    this.formError = '';
    this.userForm = {
      full_name: '',
      email: '',
      phone: '',
      country: '',
      password: '',
      role: 'user',
      is_active: true
    };
    this.showUserModal = true;
  }

  editUser(user: User): void {
    this.isEditMode = true;
    this.currentUser = user;
    this.formError = '';
    this.userForm = {
      full_name: user.full_name,
      email: user.email,
      phone: user.phone || '',
      country: user.country || '',
      password: '',
      role: user.role || 'user',
      is_active: this.isUserActive(user)
    };
    this.showUserModal = true;
  }

  closeUserModal(): void {
    if (this.saving) {
      return;
    }

    this.showUserModal = false;
    this.formError = '';
    this.currentUser = null;
    this.userForm = null;
  }

  saveUser(formValue: UserFormValue): void {
    this.saving = true;
    this.formError = '';

    const payload = this.buildUserPayload(formValue);
    const request$ = this.isEditMode && this.currentUser
      ? this.backendService.updateUser({ id: this.currentUser.id, ...payload })
      : this.backendService.createUser(payload);

    request$.subscribe({
      next: (response: any) => {
        this.saving = false;

        if (response?.success) {
          this.successPopupService.show(
            this.isEditMode ? 'User updated successfully.' : 'User created successfully.',
            this.isEditMode ? 'User Updated' : 'User Created'
          );
          this.showUserModal = false;
          this.currentUser = null;
          this.userForm = null;
          this.loadUsers();
          return;
        }

        this.formError = response?.message || 'Failed to save user';
      },
      error: (error) => {
        console.error('Failed to save user:', error);
        this.saving = false;
        this.formError = this.getErrorMessage(error, 'Failed to save user');
      }
    });
  }

  viewUser(user: User): void {
    this.router.navigate(['/admin-dashboard/user', user.id]);
  }

  onFiltersChange(): void {
    this.currentPage = 1;
    this.loadUsers();
  }

  clearFilters(): void {
    this.searchTerm = '';
    this.selectedRole = 'all';
    this.selectedStatus = 'all';
    this.sortOrder = 'newest';
    this.currentPage = 1;
    this.loadUsers();
  }

  onPageChange(page: number): void {
    if (page < 1 || page > this.totalPages || page === this.currentPage) {
      return;
    }

    this.currentPage = page;
    this.loadUsers();
  }

  retryLoad(): void {
    this.loadUsers();
  }

  exportCsv(): void {
    if (this.totalItems === 0 || this.exporting) {
      return;
    }

    this.exporting = true;
    this.fetchUsersForExport(1, [], 100);
  }

  getStatusLabel(user: User): string {
    return this.isUserActive(user) ? 'Active' : 'Inactive';
  }

  getRoleLabel(user: User): string {
    const role = (user.role || 'user').toLowerCase();
    return role.charAt(0).toUpperCase() + role.slice(1);
  }

  getContactLabel(user: User): string {
    const details = [user.email, user.phone].filter(Boolean);
    return details.length > 0 ? details.join(' | ') : 'Not available';
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

  private buildUserPayload(formValue: UserFormValue): any {
    const [firstName, lastName] = this.splitFullName(formValue.full_name);

    return {
      first_name: firstName,
      last_name: lastName,
      email: formValue.email.trim(),
      phone: formValue.phone.trim(),
      country: formValue.country.trim(),
      password: formValue.password,
      role: formValue.role,
      is_active: formValue.is_active
    };
  }

  private splitFullName(fullName: string): [string, string] {
    const parts = fullName.trim().split(/\s+/).filter(Boolean);
    const firstName = parts[0] || '';
    const lastName = parts.length > 1 ? parts.slice(1).join(' ') : '';

    return [firstName, lastName];
  }

  private isUserActive(user: User): boolean {
    return Number(user.is_active) === 1 || user.is_active === true;
  }

  private fetchUsersForExport(page: number, collectedUsers: User[], limit: number): void {
    this.backendService.getUsers({
      page,
      limit,
      search: this.searchTerm.trim() || undefined,
      role: this.selectedRole !== 'all' ? this.selectedRole : undefined,
      status: this.selectedStatus !== 'all' ? this.selectedStatus : undefined,
      sort_order: this.sortOrder
    }).subscribe({
      next: (response: any) => {
        const pageUsers = response?.success && Array.isArray(response.data) ? response.data as User[] : [];
        const pagination = response?.meta?.pagination || {};
        const nextUsers = [...collectedUsers, ...pageUsers];
        const totalPages = Number(pagination.total_pages ?? page);

        if (page < totalPages) {
          this.fetchUsersForExport(page + 1, nextUsers, limit);
          return;
        }

        this.downloadCsv(nextUsers);
        this.exporting = false;
      },
      error: (error) => {
        console.error('Failed to export users:', error);
        this.exporting = false;
        this.errorHandlerService.showError(this.getErrorMessage(error, 'Failed to export users'));
      }
    });
  }

  private downloadCsv(users: User[]): void {
    const rows = [
      ['ID', 'Name', 'Email', 'Phone', 'Country', 'Role', 'Status', 'Created At'],
      ...users.map((user) => [
        String(user.id),
        user.full_name || '',
        user.email || '',
        user.phone || '',
        user.country || '',
        this.getRoleLabel(user),
        this.getStatusLabel(user),
        this.formatDateTime(user.created_at)
      ])
    ];

    const csvContent = rows
      .map((row) => row.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(','))
      .join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');

    link.href = url;
    link.download = 'users.csv';
    link.click();

    window.URL.revokeObjectURL(url);
  }

  private getErrorMessage(error: any, fallback: string): string {
    const fieldErrors = error?.error?.data?.errors;

    if (fieldErrors && typeof fieldErrors === 'object') {
      const firstError = Object.values(fieldErrors).find((value) => typeof value === 'string') as string | undefined;

      if (firstError) {
        return firstError;
      }
    }

    return error?.error?.message || fallback;
  }
}
