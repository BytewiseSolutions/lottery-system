import { Component, OnDestroy, OnInit, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { BackendService } from '../util/backend.service';
import { ApiResponse } from '../util/api-response';
import { LayoutComponent } from '../layout/layout.component';
import { Draw } from './draw';
import { SiteSettingsService } from '../util/site-settings.service';

@Component({
  selector: 'app-lotteries',
  imports: [CommonModule, RouterLink, LayoutComponent],
  templateUrl: './lotteries.component.html',
  styleUrl: './lotteries.component.css',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class LotteriesComponent implements OnInit, OnDestroy {
  draws: Draw[] = [];
  private refreshIntervalId?: ReturnType<typeof setInterval>;

  constructor(
    private backendService: BackendService,
    private cdr: ChangeDetectorRef,
    private siteSettings: SiteSettingsService
  ) {}

  ngOnInit() {
    this.loadDraws();
    this.refreshIntervalId = setInterval(() => this.loadDraws(), 30000);
  }

  ngOnDestroy() {
    if (this.refreshIntervalId) {
      clearInterval(this.refreshIntervalId);
    }
  }

  private loadDraws() {
    this.backendService.getUpcomingDraws().subscribe({
      next: (response: ApiResponse<Draw[]>) => {
        const all = response?.data ?? [];
        this.draws = all.filter(d => !this.isDrawExpired(d));
        this.cdr.markForCheck();
      },
      error: (error) => {
        this.draws = [];
        this.cdr.markForCheck();
      }
    });
  }

  private addWeekToDate(dateString: string): string {
    const date = new Date(dateString);
    date.setDate(date.getDate() + 7);
    return date.toISOString();
  }

  formatJackpot(jackpot: string | number): string {
    const symbol = this.siteSettings.currencySymbol();
    const value = parseFloat(String(jackpot));
    if (isNaN(value)) return String(jackpot);
    return `${symbol}${value.toFixed(2)}`;
  }

  formatDate(dateString: string): string {
    if (!dateString) return 'TBA';
    
    const date = new Date(dateString);
    
    if (isNaN(date.getTime())) {
      return 'TBA';
    }
    
    return date.toLocaleDateString('en-US', { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  }

  getLotteryCode(name: string, date?: string): string {
    if (!name) return 'monday'; 
    
    const baseCode = name.includes('Monday') ? 'monday' : 
                    name.includes('Wednesday') ? 'wednesday' : 'friday';
    return date ? `${baseCode}-${date}` : baseCode;
  }

  getLotteryImageId(name: string): number {
    if (!name) return 1;
    if (name.includes('Monday')) return 1;
    if (name.includes('Wednesday')) return 2;
    if (name.includes('Friday')) return 3;
    return 1;
  }

  getDateOnly(dateString: string): string {
    if (!dateString) return '';
    try {
      return dateString.split('T')[0];
    } catch (error) {
      return '';
    }
  }

  getCountdown(targetDate: string): string {
    if (!targetDate) return '00 Days 00:00:00';
    
    const target = new Date(targetDate);
    
    if (isNaN(target.getTime())) {
      return '00 Days 00:00:00';
    }
    
    const now = new Date().getTime();
    const targetTime = target.getTime();
    const distance = targetTime - now;

    if (distance < 0) return '00 Days 00:00:00';

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

    return `${days.toString().padStart(2, '0')} Days ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:00`;
  }

  isEntryClosed(draw: Draw): boolean {
    if (draw.is_entry_open === false) return true;
    const closeTime = draw.entry_closes_at || draw.lottery_closes_at;
    if (!closeTime) return false;
    return new Date() > new Date(closeTime);
  }

  isDrawExpired(draw: Draw): boolean {
    const drawDate = draw.drawDate || draw.nextDraw || draw.draw_date;
    if (!drawDate) return false;
    const expiry = new Date(drawDate);
    expiry.setHours(20, 0, 0, 0);
    return new Date() > expiry;
  }

  getLotteryCloseCountdown(draw: Draw): string {
    return this.getCountdown(draw.entry_closes_at || draw.lottery_closes_at || draw.drawDate || draw.nextDraw || '');
  }
}
