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
    // Use setTimeout to avoid change detection issues
    setTimeout(() => {
      setInterval(() => {
        this.loadData();
        this.cdr.detectChanges();
      }, 30000);
    }, 0);
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
      const response = await fetch(`${environment.apiUrl}/results`);
      const data = await response.json();
      
      console.log('Raw results data:', data);
      
      if (Array.isArray(data)) {
        this.results = data.slice(0, 1).map((result: any) => {
          console.log('Processing result:', result);
          console.log('Raw winning_numbers:', result.winning_numbers);
          console.log('Raw bonus_numbers:', result.bonus_numbers);
          
          const processed = {
            id: result.id,
            name: result.lottery || 'Unknown Lottery',
            drawDate: result.draw_date || result.drawDate,
            winningNumbers: this.parseNumbers(result.winning_numbers || result.numbers),
            bonusNumbers: this.parseNumbers(result.bonus_numbers || result.bonusNumbers),
            poolMoney: result.jackpot || '$0.00',
            nextDraw: this.getNextDrawDate(result.lottery),
            currentPool: this.getCurrentPoolFromDraws(result.lottery)
          };
          
          console.log('Processed result:', processed);
          return processed;
        });
      } else {
        this.results = [];
      }
    } catch (error) {
      console.error('Error loading results:', error);
      this.results = [];
    }
  }

  private parseNumbers(numbers: any): number[] {
    console.log('Parsing numbers:', numbers, 'Type:', typeof numbers);
    
    if (!numbers) return [];
    
    // If it's already an array, return it
    if (Array.isArray(numbers)) {
      console.log('Already array:', numbers);
      return numbers;
    }
    
    // If it's a string, try to parse it as JSON
    if (typeof numbers === 'string') {
      try {
        // Remove any extra quotes or whitespace
        const cleaned = numbers.trim();
        console.log('Cleaned string:', cleaned);
        
        const parsed = JSON.parse(cleaned);
        console.log('Parsed result:', parsed);
        
        return Array.isArray(parsed) ? parsed : [];
      } catch (error) {
        console.warn('Failed to parse numbers as JSON:', numbers, error);
        
        // Try to extract numbers from concatenated string like "1345645127570"
        if (/^\d+$/.test(numbers)) {
          // Split into groups of 2 digits (assuming lottery numbers are 1-75)
          const nums = [];
          for (let i = 0; i < numbers.length; i += 2) {
            const num = parseInt(numbers.substr(i, 2));
            if (num >= 1 && num <= 75) {
              nums.push(num);
            }
          }
          console.log('Extracted from concatenated string:', nums);
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
    const draw = this.draws.find(d => d.name === lottery);
    return draw?.jackpot || '$10.00';
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
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    return `${days.toString().padStart(2, '0')} Days ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  }
}
