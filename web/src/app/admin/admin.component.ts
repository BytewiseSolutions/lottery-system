import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { Subject, takeUntil, interval } from 'rxjs';
import { AdminSidebarComponent } from './sidebar/admin-sidebar.component';
import { LotteryService } from '../services/lottery.service';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule, AdminSidebarComponent, HttpClientModule],
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.scss']
})
export class AdminComponent implements OnInit, OnDestroy {
  private destroy$ = new Subject<void>();
  
  isAuthenticated = true;
  isLoading = false;
  loginError = '';
  showPassword = false;
  
  loginForm: FormGroup;
  uploadForm: FormGroup;
  
  activeSection = 'dashboard';
  settingsTab = 'general';
  
  totalResults = 0;
  totalUsers = 0;
  activeLotteries = 0;
  pendingActions = 0;
  unreadNotifications = 0;
  resultsGrowth = 0;
  usersGrowth = 0;
  
  results: any[] = [];
  filteredResults: any[] = [];
  selectedResults = new Set<number>();
  bulkAction = '';
  
  filterStatus = '';
  filterLottery = '';
  filterDateFrom = '';
  filterDateTo = '';
  searchQuery = '';
  
  currentPage = 1;
  itemsPerPage = 10;
  totalPages = 1;
  
  isUploading = false;
  winningNumbers: number[] = [0, 0, 0, 0, 0, 0];
  recentUploads: any[] = [];
  
  systemStatus = 'online';
  isConnected = true;
  isRefreshing = false;
  storageUsed = '1.2GB';
  storageTotal = '10GB';
  storagePercentage = 12;
  memoryUsage = 45;
  lastLogin = 'Today, 09:30';
  currentDate = '';
  currentTime = '';
  
  showNotificationsPanel = false;
  notifications: any[] = [];
  
  resultsChart: any;
  lotteryChart: any;
  engagementChart: any;
  chartTimeRange = '7d';
  
  analyticsRange = '30d';
  topLotteries: any[] = [];
  
  siteSettings = {
    name: 'Total Free Lotto',
    timezone: 'UTC',
    resultsPerPage: 10
  };
  
  timezones = ['UTC', 'America/New_York', 'Europe/London', 'Asia/Tokyo'];
  apiKeys: any[] = [];
  
  lotteryTypes = ['Monday Lotto', 'Wednesday Lotto', 'Friday Lotto'];
  upcomingDraws: any[] = [];
  recentActivities: any[] = [];
  isMobileSidebarOpen = false;

  editingDraw: any = null;
  editDrawForm: FormGroup;

  constructor(
    private router: Router,
    private fb: FormBuilder,
    private lotteryService: LotteryService
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      rememberMe: [false]
    });
    
    this.uploadForm = this.fb.group({
      lottery: ['', Validators.required],
      drawDate: ['', Validators.required],
      jackpot: ['', [Validators.required, Validators.pattern(/^\d+(\.\d{1,2})?$/)]],
      winners: [0, [Validators.required, Validators.min(0), Validators.max(1000)]],
      notes: [''],
      publishNow: [true]
    });

    this.editDrawForm = this.fb.group({
      lottery: ['', Validators.required],
      drawDate: ['', Validators.required],
      jackpot: ['', Validators.required]
    });
  }

  ngOnInit() {
    this.checkAuth();
    if (this.isAuthenticated) {
      this.initializeDashboard();
      this.startSystemUpdates();
    }
    
    this.loadSampleData();
  }

  ngOnDestroy() {
    this.destroy$.next();
    this.destroy$.complete();
  }

  initializeDashboard() {
    this.updateDateTime();
    
    this.loadResults();
    this.loadNotifications();
    this.loadAnalytics();
    
    setTimeout(() => {
      this.initializeCharts();
    }, 100);
  }

  startSystemUpdates() {
    interval(1000)
      .pipe(takeUntil(this.destroy$))
      .subscribe(() => {
        this.updateDateTime();
      });
    
    interval(30000)
      .pipe(takeUntil(this.destroy$))
      .subscribe(() => {
        this.updateSystemStats();
      });
    
    interval(10000)
      .pipe(takeUntil(this.destroy$))
      .subscribe(() => {
        this.checkConnection();
      });
  }

  checkAuth() {
    const token = localStorage.getItem('adminToken');
    const user = localStorage.getItem('adminUser');
    
    if (!token && !user) {
      this.isAuthenticated = true;
      localStorage.setItem('adminToken', 'demo-token-12345');
      localStorage.setItem('adminUser', JSON.stringify({
        email: 'admin@totalfreelotto.com',
        name: 'Administrator',
        role: 'admin'
      }));
    } else if (token && user) {
      const userData = JSON.parse(user);
      this.isAuthenticated = userData.role === 'admin';
    }
  }

  async login() {
    if (this.loginForm.invalid) return;
    
    this.isLoading = true;
    this.loginError = '';
    
    try {
      const { email, password, rememberMe } = this.loginForm.value;
      
      if (email === 'admin@totalfreelotto.com' && password === 'admin123') {
        const userData = {
          email: email,
          name: 'Administrator',
          role: 'admin',
          lastLogin: new Date().toISOString()
        };
        
        localStorage.setItem('adminToken', 'secure-token-' + Date.now());
        localStorage.setItem('adminUser', JSON.stringify(userData));
        
        if (rememberMe) {
          localStorage.setItem('rememberMe', 'true');
        }
        
        this.isAuthenticated = true;
        this.initializeDashboard();
      } else {
        this.loginError = 'Invalid email or password. Try admin@totalfreelotto.com / admin123';
      }
    } catch (error) {
      this.loginError = 'An error occurred. Please try again.';
      console.error('Login error:', error);
    } finally {
      this.isLoading = false;
    }
  }

  togglePassword() {
    this.showPassword = !this.showPassword;
  }

  logout() {
    localStorage.removeItem('adminToken');
    localStorage.removeItem('adminUser');
    this.isAuthenticated = false;
    this.router.navigate(['/']);
  }

  updateDateTime() {
    const now = new Date();
    this.currentDate = now.toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
    this.currentTime = now.toLocaleTimeString('en-US', {
      hour12: true,
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    });
  }

  updateSystemStats() {
    this.storagePercentage = Math.min(100, this.storagePercentage + Math.random() * 2);
    this.storageUsed = (1.2 + Math.random() * 0.1).toFixed(1) + 'GB';
    this.memoryUsage = Math.floor(40 + Math.random() * 20);
  }

  checkConnection() {
    this.isConnected = navigator.onLine;
    this.systemStatus = this.isConnected ? 'online' : 'offline';
  }

  showSection(section: string) {
    this.activeSection = section;
    
    switch (section) {
      case 'dashboard':
        this.loadDashboardStats();
        break;
      case 'upload':
        // Load upload data if needed
        break;
      case 'manage':
        this.loadResults();
        break;
      case 'analytics':
        this.loadAnalytics();
        break;
    }
  }

  getPageTitle(): string {
    const titles: { [key: string]: string } = {
      'dashboard': 'Dashboard Overview',
      'upload': 'Upload Results',
      'manage': 'Manage Results',
      'analytics': 'Analytics & Reports',
      'scheduled': 'Scheduled Uploads',
      'users': 'User Management',
      'logs': 'Activity Logs',
      'settings': 'Settings'
    };
    return titles[this.activeSection] || 'Dashboard';
  }

  loadSampleData() {
    this.loadResults();
    this.loadUpcomingDraws();
    this.loadDashboardStats();
  }

  onSectionChange(section: string) {
    this.showSection(section);
  }

  onSidebarLogout() {
    this.logout();
  }

  goHome() {
    this.router.navigate(['/']);
  }

  uploadResult() {
    if (this.uploadForm.invalid) return;
    
    this.isUploading = true;
    const formData = {
      ...this.uploadForm.value,
      numbers: this.winningNumbers.filter(n => n > 0)
    };
    
    this.lotteryService.createResult(formData)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (result) => {
          console.log('Result uploaded successfully:', result);
          this.uploadForm.reset();
          this.winningNumbers = [0, 0, 0, 0, 0, 0];
          this.loadResults();
          this.isUploading = false;
        },
        error: (error) => {
          console.error('Error uploading result:', error);
          this.isUploading = false;
        }
      });
  }

  deleteResult(id: number) {
    if (confirm('Are you sure you want to delete this result?')) {
      this.lotteryService.deleteResult(id)
        .pipe(takeUntil(this.destroy$))
        .subscribe({
          next: () => {
            this.loadResults();
          },
          error: (error) => {
            console.error('Error deleting result:', error);
          }
        });
    }
  }

  loadResults() {
    this.lotteryService.getResults()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (results) => {
          this.results = results;
          this.filteredResults = results;
          this.totalResults = results.length;
        },
        error: (error) => {
          console.error('Error loading results:', error);
          this.results = [];
          this.filteredResults = [];
          this.totalResults = 0;
        }
      });
  }

  loadUpcomingDraws() {
    this.lotteryService.getUpcomingDraws()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (draws) => {
          // Map the API response to match the expected format
          this.upcomingDraws = draws.map(draw => ({
            id: draw.id,
            lottery: draw.name,
            drawDate: draw.nextDraw,
            jackpot: draw.jackpot,
            status: 'scheduled'
          }));
        },
        error: (error) => {
          console.error('Error loading upcoming draws:', error);
          this.upcomingDraws = [];
        }
      });
  }

  loadDashboardStats() {
    this.lotteryService.getDashboardStats()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (stats) => {
          this.totalUsers = stats.totalUsers || 0;
          this.activeLotteries = stats.activeLotteries || 0;
          this.pendingActions = stats.pendingActions || 0;
          this.resultsGrowth = stats.resultsGrowth || 12;
          this.usersGrowth = stats.usersGrowth || -2;
          this.unreadNotifications = stats.unreadNotifications || 0;
        },
        error: (error) => {
          console.error('Error loading dashboard stats:', error);
          // Set default values on error
          this.resultsGrowth = 12;
          this.usersGrowth = -2;
          this.unreadNotifications = 0;
        }
      });
  }

  loadNotifications() {
    // Load notifications from API or use static data
    this.notifications = [
      {
        id: 1,
        type: 'success',
        icon: 'fas fa-check',
        title: 'Upload Successful',
        message: 'Friday Lotto results have been published',
        time: '10 minutes ago',
        read: false
      }
    ];
  }

  loadAnalytics() {
    // Load analytics data
  }

  initializeCharts() {
    // Initialize charts
  }

  onSearch() {
    // Search functionality
  }

  addNumber() {
    if (this.winningNumbers.length < 10) {
      this.winningNumbers.push(0);
    }
  }

  clearNumbers() {
    this.winningNumbers = [0, 0, 0, 0, 0, 0];
  }

  saveDraft() {
    // Save as draft functionality
  }

  // Placeholder methods for manage section
  applyFilters() {}
  exportResults() {}
  toggleSelectAll(event: any) {}
  toggleSelectResult(id: number) {}
  viewResult(result: any) {}
  editResult(result: any) {}
  toggleResultStatus(result: any) {}
  applyBulkAction() {}
  prevPage() {}
  nextPage() {}
  
  // Mobile sidebar methods
  toggleMobileSidebar() {
    this.isMobileSidebarOpen = !this.isMobileSidebarOpen;
  }

  closeMobileSidebar() {
    this.isMobileSidebarOpen = false;
  }

  refreshData() {
    this.isRefreshing = true;
    this.loadResults();
    this.loadUpcomingDraws();
    this.loadDashboardStats();
    setTimeout(() => {
      this.isRefreshing = false;
    }, 1000);
  }

  setAnalyticsRange(range: string) {
    this.analyticsRange = range;
  }

  openDateRangePicker() {
    // Open date range picker logic
  }

  saveSettings() {
    // Save settings logic
  }

  copyApiKey(key: string) {
    // Copy API key logic
  }

  revokeApiKey(id: number) {
    // Revoke API key logic
  }

  formatDateTimeForInput(dateString: string): string {
    return new Date(dateString).toISOString().slice(0, 16);
  }

  updateDrawTime(draw: any, event: any) {
    const newDateTime = event.target.value;
    if (newDateTime) {
      const updatedDraw = {
        ...draw,
        drawDate: new Date(newDateTime).toISOString()
      };
      
      this.lotteryService.updateDrawTime(draw.id, updatedDraw)
        .pipe(takeUntil(this.destroy$))
        .subscribe({
          next: () => {
            this.loadUpcomingDraws();
          },
          error: (error) => {
            console.error('Error updating draw time:', error);
            alert('Failed to update draw time');
          }
        });
    }
  }

  editDrawTime(draw: any) {
    this.editingDraw = draw;
    this.editDrawForm.patchValue({
      lottery: draw.lottery,
      drawDate: this.formatDateTimeForInput(draw.drawDate),
      jackpot: draw.jackpot.replace('$', '').replace(',', '')
    });
  }

  saveDrawTime() {
    if (this.editDrawForm.invalid) return;
    
    const formData = this.editDrawForm.value;
    const updatedDraw = {
      ...this.editingDraw,
      lottery: formData.lottery,
      drawDate: new Date(formData.drawDate).toISOString(),
      jackpot: '$' + parseFloat(formData.jackpot).toFixed(2)
    };
    
    this.lotteryService.updateDrawTime(this.editingDraw.id, updatedDraw)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.loadUpcomingDraws();
          this.cancelEditDraw();
        },
        error: (error) => {
          console.error('Error updating draw:', error);
        }
      });
  }

  cancelEditDraw() {
    this.editingDraw = null;
    this.editDrawForm.reset();
  }

  deleteDraw(id: number) {
    if (confirm('Are you sure you want to delete this draw?')) {
      this.lotteryService.deleteDraw(id)
        .pipe(takeUntil(this.destroy$))
        .subscribe({
          next: () => {
            this.loadUpcomingDraws();
          },
          error: (error) => {
            console.error('Error deleting draw:', error);
          }
        });
    }
  }

  generateApiKey() {
    // Generate API key logic
  }
}