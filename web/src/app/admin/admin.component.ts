import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { Subject, takeUntil, interval } from 'rxjs';
import { AdminSidebarComponent } from './sidebar/admin-sidebar.component';
import { AnalyticsComponent } from './components/analytics/analytics.component';
import { ManageResultsComponent } from './components/manage-results/manage-results.component';
import { UserManagementComponent } from './components/user-management/user-management.component';
import { ActivityLogsComponent } from './components/activity-logs/activity-logs.component';
import { SettingsComponent } from './components/settings/settings.component';
import { LotteryService } from '../services/lottery.service';
import { environment } from '../../environments/environment';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [
    CommonModule, 
    FormsModule, 
    ReactiveFormsModule, 
    AdminSidebarComponent, 
    AnalyticsComponent,
    ManageResultsComponent,
    UserManagementComponent,
    ActivityLogsComponent,
    SettingsComponent,
    HttpClientModule
  ],
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
  resultsGrowth = 0;
  usersGrowth = 0;
  
  results: any[] = [];
  filteredResults: any[] = [];
  selectedResults = new Set<number>();
  bulkAction = '';
  
  searchQuery = '';
  currentPage = 1;
  itemsPerPage = 10;
  totalPages = 1;
  
  isUploading = false;
  winningNumbers: number[] = [0, 0, 0, 0, 0];
  bonusNumbers: number[] = [0, 0];
  
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
  
  analyticsRange = '30d';
  analyticsData = {
    totalPlays: 0,
    totalRevenue: 0,
    averagePlayersPerDraw: 0,
    winnerRate: 0,
    popularNumbers: [] as number[],
    lotteryPerformance: [] as any[],
    userEngagement: [] as any[],
    revenueByMonth: [] as any[]
  };
  
  users: any[] = [];
  totalUsersCount = 0;
  usersCurrentPage = 1;
  usersTotalPages = 1;
  usersSearchQuery = '';
  paginatedResults: any[] = [];
  editingResult: any = null;
  editResultForm: FormGroup;
  editWinningNumbers: number[] = [0, 0, 0, 0, 0];
  editBonusNumbers: number[] = [0, 0];
  
  showUploadModal = false;
  showSuccessModal = false;
  showConfirmModal = false;
  uploadSuccessData: any = null;
  confirmModalData: any = null;
  
  siteSettings = {
    name: 'Total Free Lotto',
    timezone: 'UTC',
    resultsPerPage: 10
  };
  
  timezones = ['UTC', 'America/New_York', 'Europe/London', 'Asia/Tokyo'];
  apiKeys: any[] = [];
  
  upcomingDraws: any[] = [];
  isMobileSidebarOpen = false;
  
  customDateFrom = '';
  customDateTo = '';
  showCustomDateModal = false;

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
      notes: [''],
      publishNow: [true]
    });

    this.editResultForm = this.fb.group({
      id: [''],
      lottery: ['', Validators.required],
      drawDate: ['', Validators.required],
      jackpot: ['', Validators.required],
      status: ['published']
    });
  }

  ngOnInit() {
    this.checkAuth();
    if (this.isAuthenticated) {
      this.initializeDashboard();
      this.startSystemUpdates();
    }
    this.loadSampleData();
    // Initialize analytics data with test values
    this.analyticsData = {
      totalPlays: 1250,
      totalRevenue: 0,
      averagePlayersPerDraw: 85,
      winnerRate: 12.5,
      popularNumbers: [],
      lotteryPerformance: [],
      userEngagement: [],
      revenueByMonth: []
    };
    this.loadAnalytics();
  }

  ngOnDestroy() {
    this.destroy$.next();
    this.destroy$.complete();
  }

  initializeDashboard() {
    this.updateDateTime();
    this.loadResults();
    this.loadAnalytics();
  }

  startSystemUpdates() {
    interval(1000).pipe(takeUntil(this.destroy$)).subscribe(() => this.updateDateTime());
    interval(30000).pipe(takeUntil(this.destroy$)).subscribe(() => this.updateSystemStats());
    interval(10000).pipe(takeUntil(this.destroy$)).subscribe(() => this.checkConnection());
  }

  checkAuth() {
    const token = localStorage.getItem('token');
    const user = localStorage.getItem('user');
    
    if (!token || !user) {
      this.isAuthenticated = false;
      return;
    }
    
    try {
      const userData = JSON.parse(user);
      this.isAuthenticated = userData.role === 'admin' || userData.email === 'admin@totalfreelotto.com';
      
      if (!this.isAuthenticated) {
        this.router.navigate(['/']);
      }
    } catch (error) {
      this.isAuthenticated = false;
      this.router.navigate(['/']);
    }
  }

  async login() {
    if (this.loginForm.invalid) return;
    
    this.isLoading = true;
    this.loginError = '';
    
    try {
      const { email, password } = this.loginForm.value;
      
      const response = await fetch(`${environment.apiUrl}/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ identifier: email, password: password })
      });
      
      const result = await response.json();
      
      if (result.success && (result.user.role === 'admin' || result.user.email === 'admin@totalfreelotto.com')) {
        localStorage.setItem('token', result.token);
        localStorage.setItem('user', JSON.stringify(result.user));
        
        this.isAuthenticated = true;
        this.initializeDashboard();
      } else if (result.success) {
        this.loginError = 'Access denied. Admin privileges required.';
      } else {
        this.loginError = result.error || 'Invalid credentials';
      }
    } catch (error) {
      this.loginError = 'Network error. Please try again.';
      console.error('Login error:', error);
    } finally {
      this.isLoading = false;
    }
  }

  togglePassword() {
    this.showPassword = !this.showPassword;
  }

  logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
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
      case 'manage':
        this.loadResults();
        break;
      case 'analytics':
        this.loadAnalytics();
        break;
      case 'users':
        this.loadUsers();
        break;
    }
  }

  getPageTitle(): string {
    const titles: { [key: string]: string } = {
      'dashboard': 'Dashboard',
      'upload': 'Upload Results',
      'manage': 'Manage Results',
      'analytics': 'Analytics & Reports',
      'users': 'User Management',
      'logs': 'Activity Logs',
      'settings': 'Settings'
    };
    return titles[this.activeSection] || 'Dashboard';
  }

  // Component event handlers
  onSectionChange(section: string) {
    this.showSection(section);
  }

  onSidebarLogout() {
    this.logout();
  }

  // Analytics handlers
  onAnalyticsRangeChange(range: string) {
    this.analyticsRange = range;
    this.loadAnalytics();
  }

  onCustomDateRange() {
    this.showCustomDateModal = true;
  }

  // Manage Results handlers
  onUploadClick() {
    this.showUploadModal = true;
  }

  onExportResults() {
    this.exportResults();
  }

  onSelectAllChange(event: Event) {
    this.toggleSelectAll(event);
  }

  onSelectResultChange(id: number) {
    this.toggleSelectResult(id);
  }

  onViewResult(result: any) {
    this.viewResult(result);
  }

  onEditResult(result: any) {
    this.editResult(result);
  }

  onToggleResultStatus(result: any) {
    this.toggleResultStatus(result);
  }

  onDeleteResult(id: number) {
    this.deleteResult(id);
  }

  onBulkActionApply() {
    this.applyBulkAction();
  }

  onPageChange(page: number) {
    this.currentPage = page;
    this.updatePagination();
  }

  // User Management handlers
  onSearchUsers(query: string) {
    this.usersSearchQuery = query;
    this.searchUsers();
  }

  onAddUser() {
    this.addUser();
  }

  onEditUser(user: any) {
    this.editUser(user);
  }

  onDeleteUser(id: number) {
    this.deleteUser(id);
  }

  onUsersPageChange(page: number) {
    this.usersCurrentPage = page;
    this.loadUsers();
  }

  // Settings handlers
  onSettingsTabChange(tab: string) {
    this.settingsTab = tab;
  }

  onSaveSettings() {
    this.saveSettings();
  }

  onGenerateApiKey() {
    this.generateApiKey();
  }

  onCopyApiKey(key: string) {
    this.copyApiKey(key);
  }

  onRevokeApiKey(id: number) {
    this.revokeApiKey(id);
  }

  // Mobile sidebar
  toggleMobileSidebar() {
    this.isMobileSidebarOpen = !this.isMobileSidebarOpen;
  }

  closeMobileSidebar() {
    this.isMobileSidebarOpen = false;
  }

  goHome() {
    this.router.navigate(['/']);
  }

  onSearch() {
    this.filterResults();
  }

  refreshData() {
    this.isRefreshing = true;
    this.loadDashboardStats();
    this.loadResults();
    this.loadUpcomingDraws();
    if (this.activeSection === 'users') {
      this.loadUsers();
    }
    if (this.activeSection === 'analytics') {
      this.loadAnalytics();
    }
    setTimeout(() => this.isRefreshing = false, 1000);
  }

  // Simplified methods - keeping only essential functionality
  loadSampleData() {
    // Load actual data from API instead of hardcoded values
    this.loadDashboardStats();
    this.loadResults();
    this.loadUsers();
    this.loadUpcomingDraws();
  }

  loadUpcomingDraws() {
    this.lotteryService.getUpcomingDraws()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (response: any) => {
          const draws = response.draws || response;
          this.upcomingDraws = draws.map((draw: any) => ({
            id: draw.id || Math.random(),
            lottery: this.formatLotteryName(draw.lottery_type || draw.lottery),
            drawDate: draw.draw_date || draw.drawDate,
            jackpot: '$' + (draw.jackpot || '10.00') + 'M',
            status: 'scheduled'
          }));
        },
        error: (error) => {
          console.error('Error loading upcoming draws:', error);
          this.upcomingDraws = [];
        }
      });
  }

  formatLotteryName(type: string): string {
    const names: { [key: string]: string } = {
      'monday': 'Monday Lotto',
      'wednesday': 'Wednesday Lotto', 
      'friday': 'Friday Lotto'
    };
    return names[type.toLowerCase()] || type;
  }

  loadDashboardStats() {
    this.lotteryService.getDashboardStats()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (response: any) => {
          const stats = response.stats || response;
          this.totalUsers = stats.totalUsers || 0;
          this.activeLotteries = 3; // Fixed number of lottery types
          this.pendingActions = 0; // Calculate based on actual data
          this.resultsGrowth = 12; // Calculate from historical data
          this.usersGrowth = -5; // Calculate from historical data
        },
        error: (error) => {
          console.error('Error loading dashboard stats:', error);
        }
      });
  }

  loadResults() {
    this.lotteryService.getResults()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (results) => {
          console.log('Raw results from API:', results);
          this.results = results.map(result => ({
            ...result,
            drawDate: result.draw_date || result.drawDate,
            updatedAt: result.created_at || result.updated_at || result.updatedAt,
            numbers: result.winning_numbers || result.numbers,
            bonusNumbers: result.bonus_numbers || result.bonusNumbers
          }));
          console.log('Processed results:', this.results);
          this.filteredResults = this.results;
          this.totalResults = this.results.length;
          this.updatePagination();
        },
        error: (error) => {
          console.error('Error loading results:', error);
          this.results = [];
          this.filteredResults = [];
          this.totalResults = 0;
        }
      });
  }

  loadUsers() {
    this.lotteryService.getUsers(this.usersCurrentPage, this.itemsPerPage, this.usersSearchQuery)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (response) => {
          this.users = response.users || [];
          this.totalUsersCount = response.total || 0;
          this.usersTotalPages = response.totalPages || 1;
        },
        error: (error) => {
          console.error('Error loading users:', error);
          this.users = [];
        }
      });
  }

  loadAnalytics() {
    console.log('Loading analytics for range:', this.analyticsRange);
    const dateFrom = this.analyticsRange === 'custom' ? this.customDateFrom : undefined;
    const dateTo = this.analyticsRange === 'custom' ? this.customDateTo : undefined;
    
    this.lotteryService.getAnalytics(this.analyticsRange, dateFrom, dateTo)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (data) => {
          console.log('Analytics data received:', data);
          this.analyticsData = {
            totalPlays: data.totalPlays || 0,
            totalRevenue: data.totalRevenue || 0,
            averagePlayersPerDraw: data.averagePlayersPerDraw || 0,
            winnerRate: data.winnerRate || 0,
            popularNumbers: data.popularNumbers || [],
            lotteryPerformance: data.lotteryPerformance || [],
            userEngagement: data.userEngagement || [],
            revenueByMonth: data.revenueByMonth || []
          };
          console.log('Analytics data set:', this.analyticsData);
        },
        error: (error) => {
          console.error('Error loading analytics:', error);
          // Set default values on error
          this.analyticsData = {
            totalPlays: 0,
            totalRevenue: 0,
            averagePlayersPerDraw: 0,
            winnerRate: 0,
            popularNumbers: [],
            lotteryPerformance: [],
            userEngagement: [],
            revenueByMonth: []
          };
        }
      });
  }

  updatePagination() {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const endIndex = startIndex + this.itemsPerPage;
    this.paginatedResults = this.filteredResults.slice(startIndex, endIndex);
    this.totalPages = Math.ceil(this.filteredResults.length / this.itemsPerPage);
  }

  filterResults() {
    this.filteredResults = this.results.filter(result => 
      result.lottery.toLowerCase().includes(this.searchQuery.toLowerCase())
    );
    this.currentPage = 1;
    this.updatePagination();
  }

  // Placeholder methods for functionality
  exportResults() {
    if (this.results.length === 0) {
      alert('No results to export');
      return;
    }
    const csvData = this.convertToCSV(this.results);
    const blob = new Blob([csvData], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `lottery-results-${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
    window.URL.revokeObjectURL(url);
  }

  convertToCSV(data: any[]): string {
    const headers = ['ID', 'Lottery', 'Draw Date', 'Winning Numbers', 'Bonus Numbers', 'Jackpot', 'Winners', 'Status'];
    const csvRows = [headers.join(',')];
    data.forEach(result => {
      const row = [
        result.id,
        `"${result.lottery}"`,
        `"${result.drawDate}"`,
        `"${result.numbers ? result.numbers.join(', ') : 'N/A'}"`,
        `"${result.bonusNumbers ? result.bonusNumbers.join(', ') : 'N/A'}"`,
        `"${result.jackpot}"`,
        result.winners,
        `"${result.status}"`
      ];
      csvRows.push(row.join(','));
    });
    return csvRows.join('\n');
  }
  
  toggleSelectAll(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.checked) {
      this.results.forEach(result => this.selectedResults.add(result.id));
    } else {
      this.selectedResults.clear();
    }
  }
  
  toggleSelectResult(id: number) {
    if (this.selectedResults.has(id)) {
      this.selectedResults.delete(id);
    } else {
      this.selectedResults.add(id);
    }
  }
  
  viewResult(result: any) {
    this.uploadSuccessData = {
      lottery: result.lottery,
      drawDate: result.drawDate,
      jackpot: result.jackpot,
      winningNumbers: result.numbers || [],
      bonusNumbers: result.bonusNumbers || [],
      winners: result.winners,
      status: result.status
    };
    this.showSuccessModal = true;
  }
  
  editResult(result: any) {
    this.editingResult = result;
    this.editResultForm.patchValue({
      id: result.id,
      lottery: result.lottery,
      drawDate: this.formatDateTimeForInput(result.drawDate),
      jackpot: result.jackpot.replace('$', '').replace('M', ''),
      status: result.status
    });
    this.editWinningNumbers = [...(result.numbers || [0, 0, 0, 0, 0])];
    this.editBonusNumbers = [...(result.bonusNumbers || [0, 0])];
  }
  
  toggleResultStatus(result: any) {
    const newStatus = result.status === 'published' ? 'draft' : 'published';
    this.lotteryService.updateResultStatus(result.id, newStatus)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          result.status = newStatus;
          result.updatedAt = new Date().toISOString();
          console.log('Status updated successfully');
        },
        error: (error) => {
          console.error('Error updating status:', error);
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
            console.log('Result deleted successfully');
          },
          error: (error) => {
            console.error('Error deleting result:', error);
          }
        });
    }
  }
  
  applyBulkAction() {
    if (this.selectedResults.size === 0) {
      alert('Please select results first');
      return;
    }
    
    const selectedIds = Array.from(this.selectedResults);
    
    switch (this.bulkAction) {
      case 'publish':
        this.bulkUpdateStatus(selectedIds, 'published');
        break;
      case 'unpublish':
        this.bulkUpdateStatus(selectedIds, 'draft');
        break;
      case 'delete':
        if (confirm(`Delete ${selectedIds.length} selected results?`)) {
          this.bulkDeleteResults(selectedIds);
        }
        break;
      case 'export':
        const selectedResults = this.results.filter(r => this.selectedResults.has(r.id));
        const csvData = this.convertToCSV(selectedResults);
        const blob = new Blob([csvData], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `selected-results-${new Date().toISOString().split('T')[0]}.csv`;
        link.click();
        window.URL.revokeObjectURL(url);
        break;
    }
  }

  bulkUpdateStatus(ids: number[], status: string) {
    let completed = 0;
    ids.forEach(id => {
      this.lotteryService.updateResultStatus(id, status)
        .pipe(takeUntil(this.destroy$))
        .subscribe({
          next: () => {
            completed++;
            if (completed === ids.length) {
              this.loadResults();
              this.selectedResults.clear();
              this.bulkAction = '';
              alert(`${ids.length} results updated successfully`);
            }
          },
          error: (error) => {
            console.error('Error updating status:', error);
          }
        });
    });
  }

  bulkDeleteResults(ids: number[]) {
    let completed = 0;
    ids.forEach(id => {
      this.lotteryService.deleteResult(id)
        .pipe(takeUntil(this.destroy$))
        .subscribe({
          next: () => {
            completed++;
            if (completed === ids.length) {
              this.loadResults();
              this.selectedResults.clear();
              this.bulkAction = '';
              alert(`${ids.length} results deleted successfully`);
            }
          },
          error: (error) => {
            console.error('Error deleting result:', error);
          }
        });
    });
  }
  
  searchUsers() {
    this.usersCurrentPage = 1;
    this.loadUsers();
  }
  
  addUser() {
    console.log('Adding new user');
  }
  
  editUser(user: any) {
    console.log('Editing user:', user);
  }
  
  deleteUser(id: number) {
    if (confirm('Are you sure you want to delete this user?')) {
      this.lotteryService.deleteUser(id)
        .pipe(takeUntil(this.destroy$))
        .subscribe({
          next: () => {
            this.loadUsers();
            console.log('User deleted successfully');
          },
          error: (error) => {
            console.error('Error deleting user:', error);
          }
        });
    }
  }
  
  saveSettings() {
    console.log('Saving settings:', this.siteSettings);
  }
  
  generateApiKey() {
    console.log('Generating new API key');
  }
  
  copyApiKey(key: string) {
    navigator.clipboard.writeText(key);
    console.log('API key copied to clipboard');
  }
  
  revokeApiKey(id: number) {
    if (confirm('Are you sure you want to revoke this API key?')) {
      console.log('Revoking API key:', id);
    }
  }

  // Edit result methods
  saveEditResult() {
    if (this.editResultForm.invalid) return;
    
    const validWinningNumbers = this.editWinningNumbers.filter(n => n > 0);
    const validBonusNumbers = this.editBonusNumbers.filter(n => n > 0);
    
    if (validWinningNumbers.length !== 5 || validBonusNumbers.length !== 2) {
      alert('Please enter exactly 5 winning numbers and 2 bonus numbers');
      return;
    }
    
    const formData = {
      ...this.editResultForm.value,
      numbers: validWinningNumbers,
      bonusNumbers: validBonusNumbers
    };
    
    this.lotteryService.updateResult(formData.id, formData)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.loadResults();
          this.cancelEditResult();
          alert('Result updated successfully!');
        },
        error: (error) => {
          console.error('Error updating result:', error);
          alert('Failed to update result.');
        }
      });
  }

  cancelEditResult() {
    this.editingResult = null;
    this.editResultForm.reset();
    this.editWinningNumbers = [0, 0, 0, 0, 0];
    this.editBonusNumbers = [0, 0];
  }

  validateEditNumber(event: any, index: number, type: 'winning' | 'bonus') {
    const value = parseInt(event.target.value);
    if (value > 75) {
      if (type === 'winning') {
        this.editWinningNumbers[index] = 75;
      } else {
        this.editBonusNumbers[index] = 75;
      }
      event.target.value = 75;
    } else if (value < 1 && value !== 0) {
      if (type === 'winning') {
        this.editWinningNumbers[index] = 1;
      } else {
        this.editBonusNumbers[index] = 1;
      }
      event.target.value = 1;
    }
  }
  applyCustomDateRange() {
    if (this.customDateFrom && this.customDateTo) {
      this.analyticsRange = 'custom';
      this.loadAnalytics();
      this.showCustomDateModal = false;
    }
  }

  cancelCustomDateRange() {
    this.showCustomDateModal = false;
    this.customDateFrom = '';
    this.customDateTo = '';
  }

  // Upload and modal methods
  uploadResult() {
    if (this.uploadForm.invalid) return;
    
    const validWinningNumbers = this.winningNumbers.filter(n => n > 0);
    const validBonusNumbers = this.bonusNumbers.filter(n => n > 0);
    
    if (validWinningNumbers.length !== 5) {
      alert('Please enter exactly 5 winning numbers (1-75)');
      return;
    }
    
    if (validBonusNumbers.length !== 2) {
      alert('Please enter exactly 2 bonus numbers (1-75)');
      return;
    }
    
    this.isUploading = true;
    const formData = {
      ...this.uploadForm.value,
      numbers: validWinningNumbers,
      bonusNumbers: validBonusNumbers
    };
    
    this.lotteryService.createResult(formData)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (result) => {
          this.uploadForm.reset({
            lottery: '',
            drawDate: '',
            jackpot: '',
            notes: '',
            publishNow: true
          });
          this.winningNumbers = [0, 0, 0, 0, 0];
          this.bonusNumbers = [0, 0];
          this.loadResults();
          this.isUploading = false;
          this.showUploadModal = false;
          
          this.uploadSuccessData = {
            lottery: formData.lottery,
            drawDate: formData.drawDate,
            jackpot: formData.jackpot,
            winningNumbers: formData.numbers,
            bonusNumbers: formData.bonusNumbers,
            winners: result.winners || 0,
            totalEntries: result.totalEntries || 0
          };
          this.showSuccessModal = true;
        },
        error: (error) => {
          console.error('Error uploading result:', error);
          this.isUploading = false;
          alert('Error uploading result. Please try again.');
        }
      });
  }

  saveDraft() {
    if (this.uploadForm.invalid) return;
    
    this.isUploading = true;
    const formData = {
      ...this.uploadForm.value,
      numbers: this.winningNumbers.filter(n => n > 0),
      bonusNumbers: this.bonusNumbers.filter(n => n > 0),
      publishNow: false
    };
    
    this.lotteryService.createResult(formData)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          alert('Draft saved successfully!');
          this.isUploading = false;
          this.showUploadModal = false;
          this.loadResults();
        },
        error: (error) => {
          console.error('Error saving draft:', error);
          this.isUploading = false;
          alert('Error saving draft. Please try again.');
        }
      });
  }

  validateNumber(event: any, index: number, type: string) {
    const value = parseInt(event.target.value);
    if (value > 75) {
      if (type === 'winning') {
        this.winningNumbers[index] = 75;
      } else {
        this.bonusNumbers[index] = 75;
      }
      event.target.value = 75;
    } else if (value < 1 && value !== 0) {
      if (type === 'winning') {
        this.winningNumbers[index] = 1;
      } else {
        this.bonusNumbers[index] = 1;
      }
      event.target.value = 1;
    }
  }
  clearNumbers() { this.winningNumbers = [0, 0, 0, 0, 0]; }
  clearBonusNumbers() { this.bonusNumbers = [0, 0]; }
  
  // Modal methods
  cancelAction() {
    this.showConfirmModal = false;
    this.confirmModalData = null;
  }
  
  confirmAction() {
    if (this.confirmModalData?.onConfirm) {
      this.confirmModalData.onConfirm();
    }
    this.showConfirmModal = false;
    this.confirmModalData = null;
  }

  // Dashboard methods
  formatDateTimeForInput(dateString: string): string {
    if (!dateString) return '';
    try {
      return new Date(dateString).toISOString().slice(0, 16);
    } catch (error) {
      return '';
    }
  }

  updateDrawTime(draw: any, event: any) {}
  announceWinner(draw: any) {}
  editDrawTime(draw: any) {}
  deleteDraw(id: number) {}
}