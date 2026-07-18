import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import { LayoutComponent } from '../layout/layout.component';
import { BackendService } from '../util/backend.service';
import { ApiResponse } from '../util/api-response';

@Component({
  selector: 'app-results',
  imports: [CommonModule, LayoutComponent, RouterLink],
  templateUrl: './results.component.html',
  styleUrl: './results.component.css'
})
export class ResultsComponent implements OnInit {
  results: any[] = [];
  upcomingDraws: any = {};

  constructor(private backendService: BackendService) {}

  ngOnInit() {
    this.loadResults();
    this.loadUpcomingDraws();
  }

  async loadUpcomingDraws() {
    try {
      const response = await firstValueFrom(this.backendService.getUpcomingDraws()) as ApiResponse<any[]>;
      const draws = Array.isArray(response?.data) ? response.data : [];

      draws.forEach((draw: any) => {
          const lotteryCode = this.getLotteryCode(draw.lottery_type || draw.lottery || draw.name);
          this.upcomingDraws[lotteryCode] = draw.draw_date;
      });
    } catch (error) {
      console.error('Error loading upcoming draws:', error);
    }
  }

  async loadResults() {
    try {
      const response = await firstValueFrom(this.backendService.getResults()) as ApiResponse<any[]>;
      const results = Array.isArray(response?.data) ? response.data : [];

      this.results = results.map((result: any) => ({
        id: result.id,
        game: result.lottery || 'Unknown Lottery',
        date: result.drawDate || result.draw_date,
        numbers: result.numbers || result.winning_numbers || [],
        bonusNumbers: result.bonusNumbers || result.bonus_numbers || [],
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
