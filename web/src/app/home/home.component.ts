import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { LotteryService, Draw } from '../services/lottery.service';
import { LayoutComponent } from '../layout/layout.component';
import { environment } from '../../environments/environment';

@Component({
  selector: 'app-home',
  imports: [CommonModule, RouterLink, LayoutComponent],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent implements OnInit {
  draws: Draw[] = [];
  results: any[] = [];

  constructor(private lotteryService: LotteryService, private cdr: ChangeDetectorRef) {}

  ngOnInit() {
    this.loadData();
    setInterval(() => {
      this.loadData();
      this.cdr.detectChanges();
    }, 30000);
  }

  private loadData() {
    this.lotteryService.getDraws().subscribe({
      next: (draws) => {
        this.draws = draws.slice(0, 3);
      },
      error: (error) => {
        console.error('Error loading draws:', error);
        this.draws = [];
      }
    });
    
    this.loadResults();
  }

  private async loadResults() {
    try {
      const response = await fetch(`${environment.apiUrl}/results.php`);
      const data = await response.json();
      this.results = data.slice(0, 1).map((result: any) => ({
        id: result.id,
        name: result.game,
        drawDate: result.date,
        winningNumbers: [...result.numbers, ...result.bonusNumbers],
        poolMoney: result.poolMoney,
        nextDraw: this.getNextDrawDate(result.game),
        currentPool: this.getCurrentPoolFromDraws(result.game)
      }));
    } catch (error) {
      console.error('Error loading results:', error);
      this.results = [];
    }
  }

  private getNextDrawDate(lottery: string): string {
    const now = new Date();
    let targetDay = 1;
    
    if (lottery?.includes('Wed')) targetDay = 3;
    else if (lottery?.includes('Fri')) targetDay = 5;
    
    const currentDay = now.getDay();
    let daysUntilNext = targetDay - currentDay;
    
    if (daysUntilNext <= 0) daysUntilNext += 7;
    
    const nextDate = new Date(now);
    nextDate.setDate(now.getDate() + daysUntilNext);
    
    return nextDate.toISOString().split('T')[0];
  }

  private getCurrentPoolFromDraws(lottery: string): string {
    const draw = this.draws.find(d => d.name === lottery);
    return draw?.jackpot || '$10.00';
  }

  formatDate(dateString: string | undefined): string {
    if (!dateString) return 'TBA';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  }

  getLotteryCode(name: string | undefined): string {
    if (!name) return 'mon';
    if (name.includes('Mon')) return 'mon';
    if (name.includes('Wed')) return 'wed';
    if (name.includes('Fri')) return 'fri';
    return 'mon';
  }

  getCountdown(targetDate: string | undefined): string {
    if (!targetDate) return '00 Days 00:00:00';
    const now = new Date().getTime();
    const target = new Date(targetDate).getTime();
    const distance = target - now;

    if (distance < 0) return '00 Days 00:00:00';

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    return `${days.toString().padStart(2, '0')} Days ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  }
}
