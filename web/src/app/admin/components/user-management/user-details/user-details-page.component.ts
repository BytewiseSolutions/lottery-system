import { Component, DestroyRef, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { AdminSidebarComponent } from '../../../sidebar/admin-sidebar.component';
import { UserDetailsComponent, UserDetailsData } from '../user-details.component';
import { ToastService } from '../../../../services/toast.service';
import { UserManagementApiService } from '../user-management-api.service';

@Component({
  selector: 'app-user-details-page',
  standalone: true,
  imports: [CommonModule, AdminSidebarComponent, UserDetailsComponent],
  templateUrl: './user-details-page.component.html',
  styleUrls: ['./user-details-page.component.scss']
})
export class UserDetailsPageComponent implements OnInit {
  private readonly destroyRef = inject(DestroyRef);

  user: UserDetailsData | null = null;
  loading = true;
  errorMessage = '';
  adminName = 'Administrator';
  lastLogin = 'Today, 09:30';
  isConnected = true;
  isMobileSidebarOpen = false;
  totalResults = 0;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private toastService: ToastService,
    private userManagementApi: UserManagementApiService
  ) {}

  ngOnInit(): void {
    this.setAdminMeta();
    this.route.paramMap.pipe(
      takeUntilDestroyed(this.destroyRef)
    ).subscribe((params) => {
      const userId = Number(params.get('id'));
      if (!Number.isFinite(userId) || userId <= 0) {
        this.errorMessage = 'Invalid user ID.';
        this.loading = false;
        return;
      }

      this.loadUser(userId);
    });
  }

  loadUser(userId: number): void {
    this.loading = true;
    this.errorMessage = '';

    this.userManagementApi.getUser(userId).subscribe({
      next: (response) => {
        this.user = response.user;
        this.totalResults = response.user ? 1 : 0;
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading user details:', error);
        this.errorMessage = error?.error?.error || 'Failed to load user details.';
        this.toastService.showError(this.errorMessage);
        this.loading = false;
      }
    });
  }

  navigateToSection(section: string): void {
    this.router.navigate(['/dashboard'], { queryParams: { section } });
  }

  goBackToUsers(): void {
    this.navigateToSection('users');
  }

  onEditUser(user: UserDetailsData): void {
    this.router.navigate(['/dashboard'], {
      queryParams: {
        section: 'users',
        editUserId: user.id
      }
    });
  }

  onLogout(): void {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.router.navigate(['/']);
  }

  toggleMobileSidebar(): void {
    this.isMobileSidebarOpen = !this.isMobileSidebarOpen;
  }

  closeMobileSidebar(): void {
    this.isMobileSidebarOpen = false;
  }

  private setAdminMeta(): void {
    const user = localStorage.getItem('user');
    if (!user) {
      return;
    }

    try {
      const parsed = JSON.parse(user);
      this.adminName = parsed.fullName || parsed.full_name || parsed.email?.split('@')[0] || 'Administrator';
      const now = new Date();
      this.lastLogin = now.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    } catch (error) {
      console.error('Error parsing admin user data:', error);
    }
  }
}
