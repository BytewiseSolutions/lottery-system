import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div class="admin-container">
      @if (!isAuthenticated) {
        <div class="login-screen">
          <div class="login-card">
            <h2>Admin Access Required</h2>
            <p>Please login with admin credentials first</p>
            <button (click)="goHome()" class="btn btn-primary">Go to Main Website</button>
          </div>
        </div>
      }

      @if (isAuthenticated) {
        <div class="admin-dashboard">
          <aside class="sidebar">
            <div class="sidebar-header">
              <h2><i class="fas fa-crown"></i> Admin Panel</h2>
            </div>
            <nav>
              <ul class="nav-menu">
                <li><a (click)="showSection('dashboard')" [class.active]="activeSection === 'dashboard'">
                  <i class="fas fa-tachometer-alt"></i> Dashboard
                </a></li>
                <li><a (click)="showSection('upload')" [class.active]="activeSection === 'upload'">
                  <i class="fas fa-upload"></i> Upload Results
                </a></li>
                <li><a (click)="showSection('manage')" [class.active]="activeSection === 'manage'">
                  <i class="fas fa-list"></i> Manage Results
                </a></li>
              </ul>
            </nav>
            <div class="sidebar-footer">
              <div class="system-info">
                <div class="info-item">
                  <i class="fas fa-calendar"></i>
                  <span>{{currentDate}}</span>
                </div>
                <div class="info-item">
                  <i class="fas fa-clock"></i>
                  <span>{{currentTime}}</span>
                </div>
                <div class="info-item">
                  <i class="fas fa-circle" [class.online]="systemStatus === 'online'"></i>
                  <span>System {{systemStatus}}</span>
                </div>
              </div>
              <div class="user-info">
                <i class="fas fa-user-shield"></i> Administrator
              </div>
              <a (click)="logout()" class="sidebar-link logout">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </aside>

          <main class="main-content">
            <header class="header">
              <h1>{{getPageTitle()}}</h1>
            </header>

            @if (activeSection === 'dashboard') {
              <section class="section">
                <div class="stats-grid">
                  <div class="stat-card">
                    <div class="stat-value">{{totalResults}}</div>
                    <div class="stat-label">Total Results</div>
                  </div>
                  <div class="stat-card">
                    <div class="stat-value">3</div>
                    <div class="stat-label">Active Lotteries</div>
                  </div>
                </div>
              </section>
            }

            @if (activeSection === 'upload') {
              <section class="section">
                <div class="card">
                  <div class="card-header">
                    <h2><i class="fas fa-plus-circle"></i> Upload New Result</h2>
                  </div>
                  <form (ngSubmit)="uploadResult()">
                    <div class="form-grid">
                      <div class="form-group">
                        <label>Lottery Type</label>
                        <select [(ngModel)]="formData.lottery" name="lottery" class="form-control" required>
                          <option value="">Select Lottery</option>
                          <option value="Monday Lotto">Monday Lotto</option>
                          <option value="Wednesday Lotto">Wednesday Lotto</option>
                          <option value="Friday Lotto">Friday Lotto</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Draw Date</label>
                        <input type="date" [(ngModel)]="formData.drawDate" name="drawDate" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label>Jackpot Amount</label>
                        <input type="text" [(ngModel)]="formData.jackpot" name="jackpot" class="form-control" placeholder="e.g., $15.50" required>
                      </div>
                      <div class="form-group">
                        <label>Number of Winners</label>
                        <input type="number" [(ngModel)]="formData.winners" name="winners" class="form-control" min="0" required>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                      <i class="fas fa-save"></i> Upload Result
                    </button>
                  </form>
                </div>
              </section>
            }

            @if (activeSection === 'manage') {
              <section class="section">
                <div class="card">
                  <div class="card-header">
                    <h2><i class="fas fa-cog"></i> Manage Results</h2>
                  </div>
                  <div class="results-list">
                    @for (result of results; track result.id) {
                      <div class="result-item">
                        <div class="result-info">
                          <h4>{{result.lottery}}</h4>
                          <p>{{result.drawDate}} | {{result.jackpot}} | Winners: {{result.winners}}</p>
                        </div>
                        <button (click)="deleteResult(result.id)" class="btn btn-danger">
                          <i class="fas fa-trash"></i> Delete
                        </button>
                      </div>
                    } @empty {
                      <p class="text-muted text-center">No results found</p>
                    }
                  </div>
                </div>
              </section>
            }
          </main>
        </div>
      }
    </div>
  `,
  styles: [`
    .admin-container { min-height: 100vh; }
    .login-screen { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: linear-gradient(135deg, #6366f1, #4f46e5); }
    .login-card { background: white; padding: 3rem; border-radius: 1rem; text-align: center; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .admin-dashboard { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
    .sidebar { background: white; border-right: 1px solid #e2e8f0; padding: 2rem 0; box-shadow: 4px 0 6px -1px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; }
    .sidebar-header { padding: 0 2rem 2rem; border-bottom: 1px solid #e2e8f0; margin-bottom: 2rem; }
    .sidebar-header h2 { color: #6366f1; font-size: 1.5rem; font-weight: 700; }
    .nav-menu { list-style: none; padding: 0 1rem; flex: 1; }
    .nav-menu li { margin-bottom: 0.5rem; }
    .nav-menu a { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; color: #64748b; text-decoration: none; border-radius: 0.5rem; transition: all 0.2s; cursor: pointer; }
    .nav-menu a:hover, .nav-menu a.active { background: #6366f1; color: white; }
    .sidebar-footer { padding: 1rem 2rem; border-top: 1px solid #e2e8f0; }
    .sidebar-footer .system-info { margin-bottom: 1rem; }
    .sidebar-footer .info-item { display: flex; align-items: center; gap: 0.5rem; color: #64748b; font-size: 0.75rem; margin-bottom: 0.25rem; }
    .sidebar-footer .info-item .fa-circle { font-size: 0.5rem; color: #ef4444; }
    .sidebar-footer .info-item .fa-circle.online { color: #10b981; }
    .sidebar-footer .user-info { color: #64748b; font-size: 0.875rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .sidebar-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0; color: #64748b; text-decoration: none; font-size: 0.875rem; cursor: pointer; transition: all 0.2s; }
    .sidebar-link:hover { color: #6366f1; }
    .sidebar-link.logout { color: #ef4444; }
    .sidebar-link.logout:hover { color: #dc2626; }
    .main-content { padding: 2rem; background: #f8fafc; }
    .header { background: white; padding: 1.5rem 2rem; border-radius: 1rem; margin-bottom: 2rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); }
    .header h1 { color: #1f2937; font-size: 1.875rem; font-weight: 700; margin: 0; }
    .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; cursor: pointer; text-decoration: none; font-weight: 500; transition: all 0.2s; }
    .btn-primary { background: #6366f1; color: white; }
    .btn-outline { background: transparent; border: 1px solid #d1d5db; color: #374151; }
    .btn-danger { background: #ef4444; color: white; }
    .user-info { color: #64748b; font-size: 0.875rem; }

    .card { background: white; border-radius: 1rem; padding: 2rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); margin-bottom: 2rem; }
    .card-header { margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0; }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151; }
    .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { background: white; padding: 1.5rem; border-radius: 1rem; border-left: 4px solid #6366f1; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); }
    .stat-value { font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; color: #1f2937; }
    .stat-label { color: #64748b; font-size: 0.875rem; }
    .result-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem; margin: 1rem 0; background: #f8fafc; border-radius: 0.5rem; }
  `]
})
export class AdminComponent implements OnInit {
  isAuthenticated = false;
  activeSection = 'dashboard';
  totalResults = 0;
  results: any[] = [];
  formData = {
    lottery: '',
    drawDate: '',
    jackpot: '',
    winners: 0
  };

  currentDate = '';
  currentTime = '';
  systemStatus = 'online';

  constructor(private router: Router) {}

  ngOnInit() {
    this.checkAuth();
    if (this.isAuthenticated) {
      this.loadResults();
      this.updateDateTime();
      setInterval(() => this.updateDateTime(), 1000);
    }
  }

  updateDateTime() {
    const now = new Date();
    this.currentDate = now.toLocaleDateString();
    this.currentTime = now.toLocaleTimeString();
  }

  getPageTitle(): string {
    switch(this.activeSection) {
      case 'dashboard': return 'Dashboard';
      case 'upload': return 'Upload Results';
      case 'manage': return 'Manage Results';
      default: return 'Dashboard';
    }
  }

  checkAuth() {
    const token = localStorage.getItem('token');
    const user = localStorage.getItem('user');
    
    if (token && user) {
      const userData = JSON.parse(user);
      this.isAuthenticated = userData.email === 'admin@totalfreelotto.com';
    }
  }

  showSection(section: string) {
    this.activeSection = section;
    // Update active nav link
    setTimeout(() => {
      document.querySelectorAll('.nav-menu a').forEach(link => {
        link.classList.remove('active');
      });
      document.querySelector(`[onclick*="${section}"]`)?.classList.add('active');
    });
  }

  goHome() {
    this.router.navigate(['/']);
  }

  logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.router.navigate(['/']);
  }

  async uploadResult() {
    // Implementation for uploading results
    console.log('Upload result:', this.formData);
  }

  async loadResults() {

    try {
      const response = await fetch('/api/results.php');
      this.results = await response.json();
      this.totalResults = this.results.length;
    } catch (error) {
      console.error('Error loading results:', error);
    }
  }

  async deleteResult(id: number) {
    if (confirm('Are you sure you want to delete this result?')) {
      // Implementation for deleting results
      console.log('Delete result:', id);
    }
  }
}