import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-voting-management',
  imports: [CommonModule, FormsModule],
  templateUrl: './voting-management.component.html',
  styleUrl: './voting-management.component.css'
})
export class VotingManagementComponent implements OnInit {
  lottery = 'Monday Lotto';
  selectedNumbers: number[] = [];
  selectedBonus: number[] = [];
  allocatedVotes = 1000;
  numbers = Array.from({length: 75}, (_, i) => i + 1);
  adminVotes: any[] = [];
  currentStep: 'numbers' | 'bonus' | 'confirm' = 'numbers';
  
  constructor(private http: HttpClient) {}
  
  ngOnInit() {
    this.loadAdminVotes();
  }
  
  selectNumber(num: number) {
    if (this.currentStep === 'numbers') {
      const idx = this.selectedNumbers.indexOf(num);
      if (idx > -1) {
        this.selectedNumbers.splice(idx, 1);
      } else if (this.selectedNumbers.length < 5) {
        this.selectedNumbers.push(num);
      }
    } else if (this.currentStep === 'bonus') {
      const idx = this.selectedBonus.indexOf(num);
      if (idx > -1) {
        this.selectedBonus.splice(idx, 1);
      } else if (this.selectedBonus.length < 2) {
        this.selectedBonus.push(num);
      }
    }
  }
  
  isSelected(num: number): boolean {
    if (this.currentStep === 'numbers') return this.selectedNumbers.includes(num);
    if (this.currentStep === 'bonus') return this.selectedBonus.includes(num);
    return false;
  }
  
  nextStep() {
    if (this.currentStep === 'numbers' && this.selectedNumbers.length === 5) {
      this.currentStep = 'bonus';
    } else if (this.currentStep === 'bonus' && this.selectedBonus.length === 2) {
      this.currentStep = 'confirm';
    }
  }
  
  editNumbers() {
    this.currentStep = 'numbers';
  }
  
  allocateVotes() {
    const token = localStorage.getItem('token');
    if (!token) return;
    
    this.http.post(`${environment.apiUrl}/admin-vote`, {
      lottery: this.lottery,
      numbers: this.selectedNumbers,
      bonusNumbers: this.selectedBonus,
      allocatedVotes: this.allocatedVotes,
      voteDate: new Date().toISOString().split('T')[0]
    }, {
      headers: { 'Authorization': `Bearer ${token}` }
    }).subscribe({
      next: () => {
        alert('Votes allocated successfully!');
        this.resetForm();
        this.loadAdminVotes();
      },
      error: (err) => alert(err.error?.error || 'Failed to allocate votes')
    });
  }
  
  resetForm() {
    this.selectedNumbers = [];
    this.selectedBonus = [];
    this.currentStep = 'numbers';
    this.allocatedVotes = 1000;
  }
  
  loadAdminVotes() {
    const token = localStorage.getItem('token');
    if (!token) return;
    
    this.http.get<any>(`${environment.apiUrl}/admin-vote`, {
      headers: { 'Authorization': `Bearer ${token}` }
    }).subscribe({
      next: (data) => this.adminVotes = data.adminVotes,
      error: (err) => console.error(err)
    });
  }
}
