import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { LayoutComponent } from '../layout/layout.component';
import { environment } from '../../environments/environment';

@Component({
  selector: 'app-results',
  imports: [CommonModule, LayoutComponent, RouterLink],
  templateUrl: './results.component.html',
  styleUrl: './results.component.css'
})
export class ResultsComponent implements OnInit {
  results: any[] = [];
  upcomingDraws: any = {};

  ngOnInit() {
    this.loadResults();
    this.loadUpcomingDraws();
  }

  async loadUpcomingDraws() {
    try {
      const response = await fetch(`${environment.apiUrl}/upcoming-draws`);
      const data = await response.json();
      if (data.success && data.draws) {
        data.draws.forEach((draw: any) => {
          const lotteryCode = this.getLotteryCode(draw.lottery_type);
          this.upcomingDraws[lotteryCode] = draw.draw_date;
        });
      }
    } catch (error) {
      console.error('Error loading upcoming draws:', error);
    }
  }

  async loadResults() {
    try {
      const response = await fetch(`${environment.apiUrl}/results`);
      const data = await response.json();
      this.results = data.map((result: any) => ({
        id: result.id,
        game: result.lottery || 'Unknown Lottery',
        date: result.drawDate,
        numbers: result.numbers || [],
        bonusNumbers: result.bonusNumbers || [],
        poolMoney: result.jackpot || '$0.00'
      }));
    } catch (error) {
      console.error('Error loading results:', error);
      this.results = [];
    }
  }

  formatDate(dateString: string | undefined): string {
    if (!dateString) return 'TBA';
    
    const date = new Date(dateString);
    
    // Check if date is valid
    if (isNaN(date.getTime())) {
      console.warn('Invalid date string:', dateString);
      return 'TBA';
    }
    
    return date.toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }

  getLotteryCode(lottery: string | undefined): string {
    if (!lottery || typeof lottery !== 'string') return 'monday';
    if (lottery.includes('Mon')) return 'monday';
    if (lottery.includes('Wed')) return 'wednesday';
    if (lottery.includes('Fri')) return 'friday';
    return 'monday';
  }

  getDrawDate(lottery: string | undefined): string | undefined {
    const code = this.getLotteryCode(lottery);
    return this.upcomingDraws[code];
  }
}
