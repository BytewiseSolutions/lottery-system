import { Component, OnInit, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { LotteryService, Draw } from '../services/lottery.service';
import { LayoutComponent } from '../layout/layout.component';

@Component({
  selector: 'app-lotteries',
  imports: [CommonModule, RouterLink, LayoutComponent],
  templateUrl: './lotteries.component.html',
  styleUrl: './lotteries.component.css',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class LotteriesComponent implements OnInit {
  draws: Draw[] = [];

  constructor(private lotteryService: LotteryService, private cdr: ChangeDetectorRef) {}

  ngOnInit() {
    this.loadDraws();
    setInterval(() => this.loadDraws(), 30000);
  }

  private loadDraws() {
    this.lotteryService.getDraws().subscribe({
      next: (draws) => {
        console.log('Loaded draws:', draws); // Debug log
        this.draws = draws;
        this.cdr.detectChanges();
      },
      error: (error) => {
        console.error('Error loading draws:', error);
      }
    });
  }

  private addWeekToDate(dateString: string): string {
    const date = new Date(dateString);
    date.setDate(date.getDate() + 7);
    return date.toISOString();
  }

  formatDate(dateString: string): string {
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

  getLotteryCode(name: string, date?: string): string {
    if (!name) return 'monday'; // Default fallback
    
    const baseCode = name.includes('Monday') ? 'monday' : 
                    name.includes('Wednesday') ? 'wednesday' : 'friday';
    return date ? `${baseCode}-${date}` : baseCode;
  }

  getDateOnly(dateString: string): string {
    if (!dateString) return '';
    try {
      return dateString.split('T')[0];
    } catch (error) {
      console.warn('Error splitting date:', dateString);
      return '';
    }
  }

  getCountdown(targetDate: string): string {
    if (!targetDate) return '00 Days 00:00:00';
    
    const target = new Date(targetDate);
    
    // Check if date is valid
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

    return `${days.toString().padStart(2, '0')} Days ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:00`;
  }
}
