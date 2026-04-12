import { Component, DestroyRef, ElementRef, OnInit, ViewChild, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { Subject, debounceTime, distinctUntilChanged } from 'rxjs';
import { UserFormComponent } from './user-form/user-form.component';
import { ToastService } from '../../../services/toast.service';
import {
  UserFormPayload,
  UserRecord,
  UserStats
} from './user-management.models';
import { UserManagementApiService } from './user-management-api.service';

@Component({
  selector: 'app-user-management',
  standalone: true,
  imports: [CommonModule, FormsModule, UserFormComponent],
  templateUrl: './user-management.component.html',
  styleUrls: ['./user-management.component.scss']
})
export class UserManagementComponent implements OnInit {
  private readonly destroyRef = inject(DestroyRef);
  private readonly searchInput$ = new Subject<string>();

  readonly Math = Math;
  @ViewChild('userDialog') userDialog?: ElementRef<HTMLDialogElement>;
  @ViewChild('deleteDialog') deleteDialog?: ElementRef<HTMLDialogElement>;
  @ViewChild('bulkDialog') bulkDialog?: ElementRef<HTMLDialogElement>;

  users: UserRecord[] = [];
  userStats: UserStats = {
    total_users: 0,
    active_users: 0,
    verified_users: 0,
    new_users_today: 0,
    new_users_this_week: 0,
    new_users_this_month: 0
  };

  currentPage = 1;
  totalPages = 1;
  itemsPerPage = 10;
  totalUsers = 0;

  searchQuery = '';
  statusFilter = 'all'; 
  roleFilter = 'all';
  countryFilter = '';
  dateFromFilter = '';
  dateToFilter = '';
  sortBy = 'created_at';
  sortOrder = 'desc';

  loading = false;
  loadingMessage = '';
  loadError = '';
  formError = '';

  showUserModal = false;
  showDeleteModal = false;
  showBulkActionModal = false;

  currentUser: UserRecord | null = null;
  selectedUsers: number[] = [];
  userToDelete: UserRecord | null = null;

  userForm: UserFormPayload = {};
  isEditMode = false;

  bulkAction = '';
  bulkActionUsers: UserRecord[] = [];

  countries: string[] = [];
  private pendingEditUserId: number | null = null;
  private resolvingPendingEdit = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private toastService: ToastService,
    private userManagementApi: UserManagementApiService
  ) {}

  ngOnInit() {
    this.route.queryParamMap.pipe(
      takeUntilDestroyed(this.destroyRef)
    ).subscribe((params) => {
      const editUserId = Number(params.get('editUserId'));
      this.pendingEditUserId = Number.isFinite(editUserId) && editUserId > 0 ? editUserId : null;
      this.openRequestedUserEditorIfNeeded();
    });

    this.searchInput$.pipe(
      debounceTime(300),
      distinctUntilChanged(),
      takeUntilDestroyed(this.destroyRef)
    ).subscribe((query) => {
      this.searchQuery = query;
      this.currentPage = 1;
      this.loadUsers();
    });

    this.loadUsers();
    this.loadUserStats();
  }

  loadUsers() {
    this.loading = true;
    this.loadingMessage = 'Loading users...';
    this.loadError = '';

    this.userManagementApi.getUsers({
      page: this.currentPage,
      limit: this.itemsPerPage,
      search: this.searchQuery.trim(),
      status: this.statusFilter,
      role: this.roleFilter,
      country: this.countryFilter,
      date_from: this.dateFromFilter,
      date_to: this.dateToFilter,
      sort_by: this.sortBy,
      sort_order: this.sortOrder
    }).subscribe({
        next: (response) => {
          this.users = response.users || [];
          this.totalUsers = response.total || 0;
          this.totalPages = response.totalPages || 1;
          this.currentPage = response.currentPage || 1;
          this.countries = response.countries || [];
          const visibleUserIds = new Set(this.users.map((user) => user.id));
          this.selectedUsers = this.selectedUsers.filter((userId) => visibleUserIds.has(userId));
          this.loading = false;
          this.openRequestedUserEditorIfNeeded();
        },
        error: (error) => {
          console.error('Error loading users:', error);
          this.users = [];
          this.totalUsers = 0;
          this.totalPages = 1;
          this.selectedUsers = [];
          this.loadError = this.getErrorMessage(error, 'Failed to load users.');
          this.toastService.showError(this.loadError);
          this.loading = false;
        }
      });
  }

  loadUserStats() {
    this.userManagementApi.getUserStats().subscribe({
        next: (response) => {
          this.userStats = response.stats || this.userStats;
        },
        error: (error) => {
          console.error('Error loading user stats:', error);
          this.toastService.showError(this.getErrorMessage(error, 'Failed to load user statistics.'));
        }
      });
  }

  // Search and filter methods
  onSearch() {
    this.searchInput$.next(this.searchQuery.trim());
  }

  onFilterChange() {
    this.currentPage = 1;
    this.loadUsers();
  }

  clearFilters() {
    this.searchQuery = '';
    this.statusFilter = 'all';
    this.roleFilter = 'all';
    this.countryFilter = '';
    this.dateFromFilter = '';
    this.dateToFilter = '';
    this.sortBy = 'created_at';
    this.sortOrder = 'desc';
    this.currentPage = 1;
    this.loadUsers();
  }

  // Pagination methods
  goToPage(page: number) {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.loadUsers();
    }
  }

  changeItemsPerPage(items: number) {
    this.itemsPerPage = items;
    this.currentPage = 1;
    this.loadUsers();
  }

  // User CRUD operations
  openAddUserModal() {
    this.isEditMode = false;
    this.currentUser = null;
    this.formError = '';
    this.userForm = {
      is_active: true,
      email_verified: false,
      phone_verified: false,
      role: 'user'
    };
    this.showUserModal = true;
    this.openDialog('user');
  }

  openEditUserModal(user: UserRecord) {
    this.isEditMode = true;
    this.currentUser = user;
    this.formError = '';
    this.userForm = { ...user };
    this.showUserModal = true;
    this.openDialog('user');
  }

  closeUserModal() {
    this.closeDialog(this.userDialog);
    this.showUserModal = false;
    this.currentUser = null;
    this.userForm = {};
    this.formError = '';
  }

  saveUser(userForm: UserFormPayload) {
    this.userForm = { ...userForm };

    const validationError = this.validateUserForm();
    if (validationError) {
      this.formError = validationError;
      this.toastService.showError(validationError);
      return;
    }

    this.loading = true;
    this.loadingMessage = this.isEditMode ? 'Updating user...' : 'Creating user...';
    this.formError = '';

    const request = this.isEditMode
      ? this.userManagementApi.updateUser(this.currentUser!.id, this.userForm)
      : this.userManagementApi.createUser(this.userForm);

    request.subscribe({
      next: () => {
        this.loading = false;
        this.toastService.showSuccess(this.isEditMode ? 'User updated successfully.' : 'User created successfully.');
        this.closeUserModal();
        this.loadUsers();
        this.loadUserStats();
      },
      error: (error) => {
        console.error('Error saving user:', error);
        this.formError = this.getErrorMessage(error, 'Failed to save user. Please check the form and try again.');
        this.toastService.showError(this.formError);
        this.loading = false;
      }
    });
  }

  validateUserForm(): string | null {
    const trimmedPassword = this.userForm.password?.trim() ?? '';

    if (!this.userForm.full_name?.trim()) {
      return 'Full name is required.';
    }
    if (!this.userForm.email?.trim()) {
      return 'Email is required.';
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.userForm.email.trim())) {
      return 'Enter a valid email address.';
    }
    if (!this.isEditMode && !trimmedPassword) {
      return 'Password is required for new users.';
    }
    if (!this.isEditMode && trimmedPassword.length < 8) {
      return 'Password must be at least 8 characters long.';
    }
    return null;
  }

  // Delete user
  openDeleteModal(user: UserRecord) {
    this.userToDelete = user;
    this.showDeleteModal = true;
    this.openDialog('delete');
  }

  closeDeleteModal() {
    this.closeDialog(this.deleteDialog);
    this.showDeleteModal = false;
    this.userToDelete = null;
  }

  confirmDelete() {
    if (!this.userToDelete) return;

    this.loading = true;
    this.loadingMessage = 'Deleting user...';

    this.userManagementApi.deleteUser(this.userToDelete.id).subscribe({
        next: () => {
          this.loading = false;
          this.toastService.showSuccess('User deleted successfully.');
          this.closeDeleteModal();
          this.loadUsers();
          this.loadUserStats();
        },
        error: (error) => {
          console.error('Error deleting user:', error);
          this.toastService.showError(this.getErrorMessage(error, 'Failed to delete user.'));
          this.loading = false;
        }
      });
  }

  // User details page
  openUserDetailsModal(user: UserRecord) {
    this.router.navigate(['/dashboard/users', user.id], {
      queryParams: { back: 'users' }
    });
  }

  // Bulk actions
  toggleUserSelection(userId: number) {
    const index = this.selectedUsers.indexOf(userId);
    if (index > -1) {
      this.selectedUsers.splice(index, 1);
    } else {
      this.selectedUsers.push(userId);
    }
  }

  selectAllUsers() {
    if (this.selectedUsers.length === this.users.length) {
      this.selectedUsers = [];
    } else {
      this.selectedUsers = this.users.map(user => user.id);
    }
  }

  openBulkActionModal() {
    if (this.selectedUsers.length === 0) {
      this.toastService.showInfo('Select at least one user to run a bulk action.');
      return;
    }
    this.bulkActionUsers = this.users.filter(user => this.selectedUsers.includes(user.id));
    this.showBulkActionModal = true;
    this.openDialog('bulk');
  }

  closeBulkActionModal() {
    this.closeDialog(this.bulkDialog);
    this.showBulkActionModal = false;
    this.bulkAction = '';
    this.bulkActionUsers = [];
  }

  executeBulkAction() {
    if (!this.bulkAction || this.selectedUsers.length === 0) return;

    this.loading = true;
    this.loadingMessage = `Executing ${this.bulkAction} on ${this.selectedUsers.length} users...`;

    this.userManagementApi.bulkAction(this.bulkAction, this.selectedUsers).subscribe({
      next: () => {
        this.loading = false;
        this.toastService.showSuccess(`Bulk action "${this.formatBulkActionLabel(this.bulkAction)}" completed.`);
        this.closeBulkActionModal();
        this.selectedUsers = [];
        this.loadUsers();
        this.loadUserStats();
      },
      error: (error) => {
        console.error('Error executing bulk action:', error);
        this.toastService.showError(this.getErrorMessage(error, 'Failed to execute bulk action.'));
        this.loading = false;
      }
    });
  }

  // Quick actions
  toggleUserStatus(user: UserRecord) {
    this.userManagementApi.toggleUserStatus(user.id).subscribe({
        next: () => {
          user.is_active = !user.is_active;
          this.toastService.showSuccess(`User ${user.is_active ? 'activated' : 'deactivated'} successfully.`);
          this.loadUserStats();
        },
        error: (error) => {
          console.error('Error toggling user status:', error);
          this.toastService.showError(this.getErrorMessage(error, 'Failed to update user status.'));
        }
      });
  }

  sendVerificationEmail(user: UserRecord) {
    this.userManagementApi.sendVerificationEmail(user.id).subscribe({
        next: () => {
          this.toastService.showSuccess(`Verification email sent to ${user.email}.`);
        },
        error: (error) => {
          console.error('Error sending verification email:', error);
          this.toastService.showError(this.getErrorMessage(error, 'Failed to send verification email.'));
        }
      });
  }

  resetUserPassword(user: UserRecord) {
    if (!window.confirm(`Reset password for ${user.full_name}?`)) {
      return;
    }

    this.userManagementApi.resetUserPassword(user.id).subscribe({
        next: (response) => {
          this.toastService.showSuccess(
            response.message || 'Password reset requested successfully.'
          );
        },
        error: (error) => {
          console.error('Error resetting password:', error);
          this.toastService.showError(this.getErrorMessage(error, 'Failed to reset password.'));
        }
      });
  }

  // Export functionality
  exportUsers() {
    this.userManagementApi.exportUsers().subscribe({
      next: (blob) => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `users_export_${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
        this.toastService.showSuccess('User export started.');
      },
      error: (error) => {
        console.error('Error exporting users:', error);
        this.toastService.showError(this.getErrorMessage(error, 'Failed to export users.'));
      }
    });
  }

  // Utility methods
  getStatusClass(user: UserRecord): string {
    return user.is_active ? 'active' : 'inactive';
  }

  getVerificationStatus(user: UserRecord): string {
    if (user.email_verified && user.phone_verified) return 'fully-verified';
    if (user.email_verified || user.phone_verified) return 'partially-verified';
    return 'unverified';
  }

  formatDate(date: string): string {
    return new Date(date).toLocaleDateString();
  }

  formatDateTime(date: string): string {
    return new Date(date).toLocaleString();
  }

  getPageNumbers(): number[] {
    const pages: number[] = [];
    const start = Math.max(1, this.currentPage - 2);
    const end = Math.min(this.totalPages, this.currentPage + 2);
    
    for (let i = start; i <= end; i++) {
      pages.push(i);
    }
    return pages;
  }

  onDialogCancel(event: Event, kind: 'user' | 'delete' | 'bulk') {
    event.preventDefault();

    if (kind === 'user') {
      this.closeUserModal();
      return;
    }

    if (kind === 'delete') {
      this.closeDeleteModal();
      return;
    }

    this.closeBulkActionModal();
  }

  onDialogBackdropClick(event: MouseEvent, kind: 'user' | 'delete' | 'bulk') {
    if (event.target !== event.currentTarget) {
      return;
    }

    this.onDialogCancel(event, kind);
  }

  private openRequestedUserEditorIfNeeded() {
    if (!this.pendingEditUserId || this.showUserModal) {
      return;
    }

    const requestedUser = this.users.find((user) => user.id === this.pendingEditUserId);
    if (!requestedUser) {
      this.loadRequestedUserForEdit(this.pendingEditUserId);
      return;
    }

    this.openEditUserModal(requestedUser);
    this.clearPendingEditRequest();
  }

  private loadRequestedUserForEdit(userId: number) {
    if (this.resolvingPendingEdit) {
      return;
    }

    this.resolvingPendingEdit = true;
    this.userManagementApi.getUser(userId).subscribe({
      next: (response) => {
        this.resolvingPendingEdit = false;
        if (!response.user) {
          return;
        }

        this.openEditUserModal(response.user);
        this.clearPendingEditRequest();
      },
      error: (error) => {
        console.error('Error loading requested user for edit:', error);
        this.toastService.showError(this.getErrorMessage(error, 'Failed to load the requested user.'));
        this.resolvingPendingEdit = false;
      }
    });
  }

  private clearPendingEditRequest() {
    this.pendingEditUserId = null;
    this.router.navigate([], {
      relativeTo: this.route,
      queryParams: { editUserId: null },
      queryParamsHandling: 'merge',
      replaceUrl: true
    });
  }

  private openDialog(kind: 'user' | 'delete' | 'bulk') {
    window.setTimeout(() => {
      const dialogRef = kind === 'user'
        ? this.userDialog
        : kind === 'delete'
          ? this.deleteDialog
          : this.bulkDialog;

      const dialog = dialogRef?.nativeElement;
      if (!dialog || dialog.open) {
        return;
      }

      dialog.showModal();
    }, 0);
  }

  private closeDialog(dialogRef?: ElementRef<HTMLDialogElement>) {
    const dialog = dialogRef?.nativeElement;
    if (dialog?.open) {
      dialog.close();
    }
  }

  private getErrorMessage(error: { error?: { error?: string } }, fallback: string): string {
    return error.error?.error || fallback;
  }

  private formatBulkActionLabel(action: string): string {
    return action.replace(/_/g, ' ');
  }
}
