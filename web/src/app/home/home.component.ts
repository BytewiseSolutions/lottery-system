import { Component, OnInit, ChangeDetectorRef, NgZone, OnDestroy, ChangeDetectionStrategy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import { Draw } from '../lotteries/draw';
import { BackendService } from '../util/backend.service';
import { ApiResponse } from '../util/api-response';
import { LayoutComponent } from '../layout/layout.component';

@Component({
  selector: 'app-home',
  imports: [CommonModule, RouterLink, LayoutComponent],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class HomeComponent implements OnInit, OnDestroy {
  draws: Draw[] = [];
  results: any[] = [];
  paginatedDraws: Draw[] = [];
  currentPage = 1;
  itemsPerPage = 3;
  totalPages = 0;
  private countdownInterval: any;

  constructor(
    private backendService: BackendService,
    private cdr: ChangeDetectorRef, 
    private ngZone: NgZone
  ) {}

  ngOnInit() {
    this.loadData();
    this.startCountdownTimer();
  }

  ngOnDestroy() {
    if (this.countdownInterval) {
      clearInterval(this.countdownInterval);
    }
  }

  private startCountdownTimer() {
    this.ngZone.runOutsideAngular(() => {
      this.countdownInterval = setInterval(() => {
        this.ngZone.run(() => {
          this.cdr.markForCheck();
        });
      }, 1000);
    });
  }

  private loadData() {
    this.backendService.getUpcomingDraws().subscribe({
      next: (response: ApiResponse<Draw[]>) => {
        this.draws = response?.data ?? [];
        this.totalPages = Math.ceil(this.draws.length / this.itemsPerPage);
        this.updatePagination();
        this.cdr.markForCheck();
      },
      error: (error) => {
        console.error('Error loading draws:', error);
        this.draws = [];
        this.totalPages = 0;
        this.paginatedDraws = [];
        this.cdr.markForCheck();
      }
    });
    
    this.loadResults();
  }

  updatePagination() {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const endIndex = startIndex + this.itemsPerPage;
    this.paginatedDraws = this.draws.slice(startIndex, endIndex);
  }

  goToPage(page: number) {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.updatePagination();
    }
  }

  private async loadResults() {
    try {
      const response = await firstValueFrom(this.backendService.getResults()) as ApiResponse<any[]>;
      const results = Array.isArray(response?.data) ? response.data : [];

      this.results = results.slice(0, 1).map((result: any) => ({
        id: result.id,
        name: result.lottery || 'Unknown Lottery',
        drawDate: result.draw_date || result.drawDate,
        winningNumbers: this.parseNumbers(result.winning_numbers || result.numbers),
        bonusNumbers: this.parseNumbers(result.bonus_numbers || result.bonusNumbers),
        poolMoney: result.jackpot || '$0.00',
        nextDraw: this.getNextDrawDate(result.lottery),
        currentPool: this.getCurrentPoolFromDraws(result.lottery)
      }));
    } catch (error) {
      console.error('Error loading results:', error);
      this.results = [];
    }
  }

  private parseNumbers(numbers: any): number[] {
    if (!numbers) return [];
    
    if (Array.isArray(numbers)) {
      return numbers;
    }
    
    if (typeof numbers === 'string') {
      try {
        const cleaned = numbers.trim();
        const parsed = JSON.parse(cleaned);
        return Array.isArray(parsed) ? parsed : [];
      } catch (error) {
        console.warn('Failed to parse numbers as JSON:', numbers, error);
        
        if (/^\d+$/.test(numbers)) {
          const nums = [];
          for (let i = 0; i < numbers.length; i += 2) {
            const num = parseInt(numbers.substr(i, 2));
            if (num >= 1 && num <= 75) {
              nums.push(num);
            }
          }
          return nums;
        }
        
        return [];
      }
    }
    
    return [];
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
    const draw = this.draws.find(d => (d.name || d.lottery) === lottery);
    return String(draw?.jackpot || '$10.00');
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

  getLotteryImageNumber(name: string | undefined): number {
    if (!name || typeof name !== 'string') return 1;
    if (name.includes('Mon')) return 1;
    if (name.includes('Wed')) return 2;
    if (name.includes('Fri')) return 3;
    return 1;
  }

  getLotteryCode(name: string | undefined): string {
    if (!name || typeof name !== 'string') return 'monday';
    if (name.includes('Mon')) return 'monday';
    if (name.includes('Wed')) return 'wednesday';
    if (name.includes('Fri')) return 'friday';
    return 'monday';
  }

  getDateOnly(dateString: string | undefined): string {
    if (!dateString) return '';
    try {
      return dateString.split('T')[0];
    } catch (error) {
      console.warn('Error splitting date:', dateString);
      return '';
    }
  }

  getCountdown(targetDate: string | undefined): string {
    if (!targetDate) return '00 Days 00:00:00';
    
    const target = new Date(targetDate);
    
    if (isNaN(target.getTime())) {
      console.warn('Invalid target date:', targetDate);
      return '00 Days 00:00:00';
    }
    
    const now = new Date().getTime();
    const targetTime = target.getTime();
    const distance = targetTime - now;

    if (distance < 0) return '00 Days 00:00:00';

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    return `${days.toString().padStart(2, '0')} Days ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  }

  getLotteryCloseCountdown(draw: Draw): string {
    return this.getCountdown(draw.entry_closes_at || draw.lottery_closes_at || draw.nextDraw || draw.drawDate);
  }
}
