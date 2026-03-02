import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LayoutComponent } from '../layout/layout.component';
import { LotteryService } from '../services/lottery.service';

@Component({
  selector: 'app-voting',
  imports: [CommonModule, LayoutComponent],
  templateUrl: './voting.component.html',
  styleUrl: './voting.component.css'
})
export class VotingComponent implements OnInit {
  activeTab: 'voting' | 'leading' | 'history' = 'voting';
  currentStep: 'select' | 'numbers' | 'bonus' | 'confirm' = 'select';
  selectedLottery = 'Monday Lotto';
  selectedNumbers: number[] = [];
  selectedBonus: number[] = [];
  numbers = Array.from({length: 75}, (_, i) => i + 1);
  votingHistory: any[] = [];
  leadingNumbers: any = null;
  countdown = '';
  isVotingTime = false;
  currentDate = '';
  
  constructor(private lotteryService: LotteryService) {
    this.updateDrawDate();
  }
  
  ngOnInit() {
    this.checkVotingTime();
    setInterval(() => this.checkVotingTime(), 1000);
    if (this.activeTab === 'leading') {
      this.loadLeadingNumbers();
      setInterval(() => this.loadLeadingNumbers(), 5000);
    }
  }
  
  updateDrawDate() {
    const now = new Date();
    const dayOfWeek = now.getDay();
    const daysUntilMonday = dayOfWeek === 0 ? 1 : dayOfWeek === 1 ? 0 : (8 - dayOfWeek) % 7;
    const nextMonday = new Date(now);
    nextMonday.setDate(now.getDate() + daysUntilMonday);
    this.currentDate = nextMonday.toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' });
  }
  
  checkVotingTime() {
    const now = new Date();
    const dayOfWeek = now.getDay();
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const currentTime = hours * 60 + minutes;
    const votingStart = 20 * 60;
    const votingEnd = 20 * 60;
    
    // Voting is open from Sunday 20:00 to Monday 19:59 for Monday Lotto
    const isSunday = dayOfWeek === 0;
    const isMonday = dayOfWeek === 1;
    this.isVotingTime = (isSunday && currentTime >= votingStart) || (isMonday && currentTime < votingEnd);
    
    // Calculate next Monday draw time
    const daysUntilMonday = dayOfWeek === 0 ? 1 : dayOfWeek === 1 ? 0 : (8 - dayOfWeek) % 7;
    const nextMonday = new Date(now);
    nextMonday.setDate(now.getDate() + daysUntilMonday);
    nextMonday.setHours(20, 0, 0, 0);
    
    const diff = nextMonday.getTime() - now.getTime();
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const s = Math.floor((diff % (1000 * 60)) / 1000);
    
    if (this.isVotingTime) {
      this.countdown = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    } else {
      if (days > 0) {
        this.countdown = `${days}d ${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
      } else {
        this.countdown = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
      }
    }
  }
  
  setTab(tab: 'voting' | 'leading' | 'history') {
    this.activeTab = tab;
    if (tab === 'history') this.loadHistory();
    if (tab === 'leading') this.loadLeadingNumbers();
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
  
  startVoting() {
    this.currentStep = 'numbers';
    this.selectedNumbers = [];
    this.selectedBonus = [];
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
  
  submitVote() {
    const token = localStorage.getItem('token');
    if (!token) {
      this.currentStep = 'select';
      return;
    }
    
    this.lotteryService.submitVote({
      lottery: this.selectedLottery,
      numbers: this.selectedNumbers,
      bonusNumbers: this.selectedBonus,
      voteDate: new Date().toISOString().split('T')[0]
    }).subscribe({
      next: () => {
        this.currentStep = 'select';
      },
      error: (err) => console.error(err)
    });
  }
  
  loadHistory() {
    const token = localStorage.getItem('token');
    if (!token) return;
    
    this.lotteryService.getVotingHistory().subscribe({
      next: (data) => this.votingHistory = data.votes,
      error: (err) => console.error(err)
    });
  }
  
  loadLeadingNumbers() {
    this.lotteryService.getLeadingNumbers(this.selectedLottery, new Date().toISOString().split('T')[0]).subscribe({
      next: (data) => this.leadingNumbers = data,
      error: (err) => console.error(err)
    });
  }
}
