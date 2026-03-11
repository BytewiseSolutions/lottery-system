import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LayoutComponent } from '../layout/layout.component';
import { environment } from '../../environments/environment';

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

  ngOnInit() {
    this.isLoggedIn = !!localStorage.getItem('token');
    if (this.isLoggedIn) {
      this.loadWinnings();
    } else {
      this.isLoading = false;
    }
  }

  async loadWinnings() {
    this.isLoading = true;
    this.errorMessage = '';
    
    try {
      const token = localStorage.getItem('token');
      const response = await fetch(`${environment.apiUrl}/my-winnings`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });

      if (!response.ok) {
        throw new Error('Failed to load winnings');
      }

      const data = await response.json();
      
      if (data.success) {
        this.winnings = data.winnings.map((w: any) => ({
          ...w,
          entry_numbers: typeof w.entry_numbers === 'string' ? JSON.parse(w.entry_numbers) : w.entry_numbers,
          entry_bonus: typeof w.entry_bonus === 'string' ? JSON.parse(w.entry_bonus) : w.entry_bonus
        }));
        
        this.calculateTotals();
      }
    } catch (error) {
      console.error('Error loading winnings:', error);
      this.errorMessage = 'Failed to load winnings. Please try again.';
    } finally {
      this.isLoading = false;
    }
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
