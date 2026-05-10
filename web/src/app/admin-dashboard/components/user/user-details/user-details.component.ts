import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { SidebarComponent } from '../../../sidebar/sidebar.component';
import { BackendService } from '../../../../util/backend.service';
import { User } from '../user';

@Component({
  selector: 'app-user-details',
  imports: [CommonModule, SidebarComponent],
  templateUrl: './user-details.component.html',
  styleUrl: './user-details.component.css'
})
export class UserDetailsComponent implements OnInit {
  activeTab: 'profile' | 'contact' | 'account' = 'profile';
  loading = true;
  error = false;
  user: User | null = null;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private backendService: BackendService
  ) {}

  ngOnInit(): void {
    this.route.paramMap.subscribe((params) => {
      const id = Number(params.get('id'));

      if (!id) {
        this.loading = false;
        this.error = true;
        return;
      }

      this.loadUser(id);
    });
  }

  goBackToUsers(): void {
    this.router.navigate(['/admin-dashboard/user']);
  }

  retryUserLoad(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));

    if (id) {
      this.loadUser(id);
    }
  }

  getStatusLabel(): string {
    if (!this.user) {
      return 'Not available';
    }

    return Number(this.user.is_active) === 1 || this.user.is_active === true ? 'Active' : 'Inactive';
  }

  getRoleLabel(): string {
    if (!this.user?.role) {
      return 'User';
    }

    return this.user.role.charAt(0).toUpperCase() + this.user.role.slice(1);
  }

  formatDate(value?: string): string {
    if (!value) {
      return 'Not available';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
      return value;
    }

    return date.toLocaleDateString('en-GB', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
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

  private loadUser(userId: number): void {
    this.loading = true;
    this.error = false;

    this.backendService.getUserById(userId).subscribe({
      next: (response: any) => {
        if (!response?.success || !response.data) {
          this.user = null;
          this.error = true;
          this.loading = false;
          return;
        }

        this.user = response.data as User;
        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load user details:', error);
        this.user = null;
        this.error = true;
        this.loading = false;
      }
    });
  }
}
