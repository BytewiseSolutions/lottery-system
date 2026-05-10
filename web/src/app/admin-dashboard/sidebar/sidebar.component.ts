import { Component, HostListener, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { BackendService } from '../../util/backend.service';
import { Subscription, interval } from 'rxjs';

interface User {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  role: string;
}

@Component({
  selector: 'app-sidebar',
  imports: [CommonModule],
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css']
})
export class SidebarComponent implements OnInit, OnDestroy {
  isMobileMenuOpen = false;
  currentUser: User | null = null;
  unreadNotificationCount = 0;
  loading = true;
  private subscriptions: Subscription[] = [];

  constructor(
    private router: Router,
    private backendService: BackendService
  ) {}

  ngOnInit() {
    this.loadUserProfile();
    this.loadNotificationCount();
    
    // Refresh notification count every 30 seconds
    const notificationInterval = interval(30000).subscribe(() => {
      this.loadNotificationCount();
    });
    
    this.subscriptions.push(notificationInterval);
  }

  ngOnDestroy() {
    this.subscriptions.forEach(sub => sub.unsubscribe());
  }

  loadUserProfile() {
    this.backendService.getCurrentUser().subscribe({
      next: (response: any) => {
        if (response.success) {
          this.currentUser = response.data;
        }
        this.loading = false;
      },
      error: (err) => {
        console.error('Failed to load user profile:', err);
        this.loading = false;
      }
    });
  }

  loadNotificationCount() {
    this.backendService.getUnreadNotificationCount().subscribe({
      next: (response: any) => {
        if (response.success) {
          this.unreadNotificationCount = response.data.count || 0;
        }
      },
      error: (err) => {
        console.error('Failed to load notification count:', err);
      }
    });
  }

  getUserDisplayName(): string {
    if (!this.currentUser) return 'Admin';
    return `${this.currentUser.first_name} ${this.currentUser.last_name}`.trim() || this.currentUser.email;
  }

  getUserInitials(): string {
    if (!this.currentUser) return 'A';
    const firstName = this.currentUser.first_name || '';
    const lastName = this.currentUser.last_name || '';
    return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase() || this.currentUser.email.charAt(0).toUpperCase();
  }

  logout() {
    this.backendService.logout().subscribe({
      next: () => {
        this.backendService.clearAuthSession();
        this.currentUser = null;
        this.unreadNotificationCount = 0;
        this.router.navigate(['/login']);
      },
      error: (err) => {
        console.error('Logout error:', err);
        // Still remove token and redirect on error
        this.backendService.clearAuthSession();
        this.currentUser = null;
        this.unreadNotificationCount = 0;
        this.router.navigate(['/login']);
      }
    });
  }

  toggleMobileMenu() {
    this.isMobileMenuOpen = !this.isMobileMenuOpen;
  }

  closeMobileMenu() {
    this.isMobileMenuOpen = false;
  }

  navigateTo(route: string) {
    this.router.navigate([route]);
    this.closeMobileMenu();
  }

  isActive(route: string): boolean {
    return this.router.url === route;
  }

  @HostListener('document:click', ['$event'])
  onDocumentClick(event: Event) {
    const target = event.target as HTMLElement;
    const navbar = document.querySelector('.navbar');
    
    if (this.isMobileMenuOpen && navbar && !navbar.contains(target)) {
      this.closeMobileMenu();
    }
  }
}
