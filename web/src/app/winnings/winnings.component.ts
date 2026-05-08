import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LayoutComponent } from '../layout/layout.component';
import { BackendService } from '../util/backend.service';

interface Winning {
  id: number;
  lottery: string;
  prize_amount: string;
  payment_status: string;
  created_at: string;
  paid_at: string | null;
  entry_numbers: number[];
  entry_bonus: number[];
}

@Component({
  selector: 'app-winnings',
  imports: [CommonModule, LayoutComponent],
  templateUrl: './winnings.component.html',
  styleUrl: './winnings.component.css'
})
export class WinningsComponent implements OnInit {
  isLoggedIn = false;
  winnings: Winning[] = [];
  isLoading = true;
  totalWinnings = 0;
  pendingWinnings = 0;
  paidWinnings = 0;
  errorMessage = '';

  constructor(private backendService: BackendService) {}

  ngOnInit() {
    this.isLoggedIn = !!(localStorage.getItem('auth_token') || localStorage.getItem('token'));
    if (this.isLoggedIn) {
      this.loadWinnings();
    } else {
      this.isLoading = false;
    }
  }

  loadWinnings() {
    this.isLoading = true;
    this.errorMessage = '';

    this.backendService.getMyWinnings().subscribe({
      next: (response: any) => {
        this.isLoading = false;

        if (response?.success) {
          this.winnings = (response.data || []).map((w: any) => ({
            ...w,
            entry_numbers: typeof w.entry_numbers === 'string' ? JSON.parse(w.entry_numbers) : (w.entry_numbers || []),
            entry_bonus: typeof w.entry_bonus === 'string' ? JSON.parse(w.entry_bonus) : (w.entry_bonus || [])
          }));

          this.calculateTotals();
          return;
        }

        this.errorMessage = response?.message || 'Failed to load winnings. Please try again.';
      },
      error: (error) => {
        console.error('Error loading winnings:', error);
        this.isLoading = false;
        this.errorMessage = error?.error?.message || 'Failed to load winnings. Please try again.';
      }
    });
  }

  calculateTotals() {
    this.totalWinnings = this.winnings.reduce((sum, w) => sum + parseFloat(w.prize_amount), 0);
    this.pendingWinnings = this.winnings
      .filter(w => w.payment_status === 'pending')
      .reduce((sum, w) => sum + parseFloat(w.prize_amount), 0);
    this.paidWinnings = this.winnings
      .filter(w => w.payment_status === 'paid')
      .reduce((sum, w) => sum + parseFloat(w.prize_amount), 0);
  }

  formatDate(dateString: string): string {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }

  getStatusClass(status: string): string {
    switch(status) {
      case 'paid': return 'status-paid';
      case 'pending': return 'status-pending';
      case 'failed': return 'status-failed';
      default: return '';
    }
  }

  getStatusText(status: string): string {
    return status.charAt(0).toUpperCase() + status.slice(1);
  }
}
