import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { Subject, takeUntil, interval } from 'rxjs';
import { AdminSidebarComponent } from './sidebar/admin-sidebar.component';
import { LotteryService } from '../services/lottery.service';
import { environment } from '../../environments/environment';

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
  bonusNumbers: number[] = [0, 0];
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
  
  // User Management
  users: any[] = [];
  totalUsersCount = 0;
  usersCurrentPage = 1;
  usersTotalPages = 1;
  usersSearchQuery = '';
  editingUser: any = null;
  userForm: FormGroup;
  showUploadModal = false;
  
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
  announcingWinner: any = null;
  announceWinnerForm: FormGroup;
  winnerNumbers: number[] = [0, 0, 0, 0, 0];
  winnerBonusNumbers: number[] = [0, 0];

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

    this.announceWinnerForm = this.fb.group({
      drawId: ['', Validators.required]
    });

    this.userForm = this.fb.group({
      id: [''],
      full_name: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      phone: [''],
      is_active: [true]
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
      case 'upload':
        // Load upload data if needed
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
      numbers: this.winningNumbers.filter(n => n > 0),
      bonusNumbers: this.bonusNumbers.filter(n => n > 0)
    };
    
    console.log('Uploading data:', formData);
    
    this.lotteryService.createResult(formData)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (result) => {
          console.log('Result uploaded successfully:', result);
          this.uploadForm.reset({
            lottery: '',
            drawDate: '',
            jackpot: '',
            winners: 0,
            notes: '',
            publishNow: true
          });
          this.winningNumbers = [0, 0, 0, 0, 0, 0];
          this.bonusNumbers = [0, 0];
          this.loadResults();
          this.isUploading = false;
          this.showUploadModal = false;
          alert('Result uploaded successfully!');
        },
        error: (error) => {
          console.error('Error uploading result:', error);
          this.isUploading = false;
          alert('Network error. Check if API is accessible.');
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
            alert('Result deleted successfully!');
          },
          error: (error) => {
            console.error('Error deleting result:', error);
            alert('Failed to delete result.');
          }
        });
    }
  }

  loadResults() {
    this.lotteryService.getResults()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (results) => {
          this.results = results.map(result => ({
            id: result.id,
            lottery: result.lottery || 'Unknown',
            drawDate: result.drawDate || result.draw_date,
            numbers: result.numbers || [],
            bonusNumbers: result.bonusNumbers || [],
            jackpot: result.jackpot || '$0.00',
            winners: result.winners || 0,
            status: result.status || 'published',
            updatedAt: result.updatedAt || result.created_at
          }));
          this.filteredResults = this.results;
          this.totalResults = this.results.length;
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
          this.upcomingDraws = draws.map(draw => ({
            id: draw.id,
            lottery: draw.lottery || draw.name,
            drawDate: draw.drawDate || draw.nextDraw,
            jackpot: draw.jackpot,
            status: draw.status || 'scheduled'
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

  addBonusNumber() {
    if (this.bonusNumbers.length < 4) {
      this.bonusNumbers.push(0);
    }
  }

  clearBonusNumbers() {
    this.bonusNumbers = [0, 0];
  }

  validateNumber(event: any, index: number, type: 'winning' | 'bonus') {
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

  saveDraft() {
    if (this.uploadForm.invalid) return;
    
    this.isUploading = true;
    const formData = {
      ...this.uploadForm.value,
      numbers: this.winningNumbers.filter(n => n > 0),
      publishNow: false
    };
    
    this.lotteryService.createResult(formData)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (result) => {
          console.log('Draft saved successfully:', result);
          alert('Draft saved successfully!');
          this.isUploading = false;
        },
        error: (error) => {
          console.error('Error saving draft:', error);
          this.isUploading = false;
          alert('Error saving draft. Please try again.');
        }
      });
  }

  // Placeholder methods for manage section
  applyFilters() {}
  exportResults() {}
  toggleSelectAll(event: any) {
    if (event.target.checked) {
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
    const numbers = result.numbers ? result.numbers.join(', ') : 'N/A';
    const bonusNumbers = result.bonusNumbers ? result.bonusNumbers.join(', ') : 'N/A';
    alert(`Result Details:\n\nLottery: ${result.lottery}\nDraw Date: ${result.drawDate}\nWinning Numbers: ${numbers}\nBonus Numbers: ${bonusNumbers}\nJackpot: ${result.jackpot}\nWinners: ${result.winners}\nStatus: ${result.status}`);
  }
  editResult(result: any) {
    alert(`Edit functionality coming soon for: ${result.lottery}`);
  }
  toggleResultStatus(result: any) {
    const newStatus = result.status === 'published' ? 'draft' : 'published';
    alert(`Status change functionality coming soon. Would change to: ${newStatus}`);
  }
  applyBulkAction() {
    if (this.selectedResults.size === 0) {
      alert('Please select results first');
      return;
    }
    alert(`Applying ${this.bulkAction} to ${this.selectedResults.size} results`);
  }
  prevPage() {
    if (this.currentPage > 1) {
      this.currentPage--;
      this.loadResults();
    }
  }
  nextPage() {
    if (this.currentPage < this.totalPages) {
      this.currentPage++;
      this.loadResults();
    }
  }
  
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
    if (!dateString) return '';
    try {
      return new Date(dateString).toISOString().slice(0, 16);
    } catch (error) {
      return '';
    }
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

  // User Management Methods
  loadUsers() {
    this.lotteryService.getUsers(this.usersCurrentPage, this.itemsPerPage, this.usersSearchQuery)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (response) => {
          this.users = response.users;
          this.totalUsersCount = response.total;
          this.usersTotalPages = response.totalPages;
        },
        error: (error) => {
          console.error('Error loading users:', error);
        }
      });
  }

  searchUsers() {
    this.usersCurrentPage = 1;
    this.loadUsers();
  }

  addUser() {
    this.editingUser = { id: null };
    this.userForm.reset({
      id: null,
      full_name: '',
      email: '',
      phone: '',
      is_active: true
    });
  }

  editUser(user: any) {
    this.editingUser = user;
    this.userForm.patchValue({
      id: user.id,
      full_name: user.full_name,
      email: user.email,
      phone: user.phone,
      is_active: user.is_active
    });
  }

  saveUser() {
    if (this.userForm.invalid) return;
    
    const userData = this.userForm.value;
    const isEditing = userData.id;
    
    const request = isEditing 
      ? this.lotteryService.updateUser(userData)
      : this.lotteryService.createUser(userData);
    
    request.pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.loadUsers();
          this.cancelEditUser();
        },
        error: (error) => {
          console.error('Error saving user:', error);
        }
      });
  }

  deleteUser(id: number) {
    if (confirm('Are you sure you want to delete this user?')) {
      this.lotteryService.deleteUser(id)
        .pipe(takeUntil(this.destroy$))
        .subscribe({
          next: () => {
            this.loadUsers();
          },
          error: (error) => {
            console.error('Error deleting user:', error);
          }
        });
    }
  }

  cancelEditUser() {
    this.editingUser = null;
    this.userForm.reset();
  }

  prevUsersPage() {
    if (this.usersCurrentPage > 1) {
      this.usersCurrentPage--;
      this.loadUsers();
    }
  }

  nextUsersPage() {
    if (this.usersCurrentPage < this.usersTotalPages) {
      this.usersCurrentPage++;
      this.loadUsers();
    }
  }

  announceWinner(draw: any) {
    this.announcingWinner = draw;
    this.winnerNumbers = [0, 0, 0, 0, 0];
    this.winnerBonusNumbers = [0, 0];
    this.announceWinnerForm.patchValue({ drawId: draw.id });
  }

  submitWinners() {
    if (this.winnerNumbers.filter(n => n > 0).length !== 5 || this.winnerBonusNumbers.filter(n => n > 0).length !== 2) {
      alert('Please enter 5 winning numbers and 2 bonus numbers');
      return;
    }

    const winningNums = this.winnerNumbers.filter(n => n > 0);
    const bonusNums = this.winnerBonusNumbers.filter(n => n > 0);

    this.lotteryService.announceWinners(this.announcingWinner.id, winningNums, bonusNums)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (result) => {
          alert(`Winners Announced!\n\nTotal Entries: ${result.totalEntries}\nTotal Winners: ${result.totalWinners}\nPrize Pool: ${result.prizePool}\nPrize Per Winner: ${result.prizePerWinner}`);
          this.cancelAnnounceWinner();
          this.loadUpcomingDraws();
          this.loadResults();
        },
        error: (error) => {
          console.error('Error announcing winners:', error);
          alert('Failed to announce winners');
        }
      });
  }

  cancelAnnounceWinner() {
    this.announcingWinner = null;
    this.winnerNumbers = [0, 0, 0, 0, 0];
    this.winnerBonusNumbers = [0, 0];
    this.announceWinnerForm.reset();
  }

  validateWinnerNumber(event: any, index: number, type: 'winning' | 'bonus') {
    const value = parseInt(event.target.value);
    if (value > 75) {
      if (type === 'winning') {
        this.winnerNumbers[index] = 75;
      } else {
        this.winnerBonusNumbers[index] = 75;
      }
      event.target.value = 75;
    } else if (value < 1 && value !== 0) {
      if (type === 'winning') {
        this.winnerNumbers[index] = 1;
      } else {
        this.winnerBonusNumbers[index] = 1;
      }
      event.target.value = 1;
    }
  }
}