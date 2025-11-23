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
    this.lotteryService.getDraws().subscribe(draws => {
      this.draws = [...draws, ...draws]; // Duplicate for 6 items display
    });
  }

  formatDate(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  }

  getLotteryCode(name: string): string {
    if (name.includes('Mon')) return 'mon';
    if (name.includes('Wed')) return 'wed';
    if (name.includes('Fri')) return 'fri';
    return 'mon';
  }

  getCountdown(targetDate: string): string {
    const now = new Date().getTime();
    const target = new Date(targetDate).getTime();
    const distance = target - now;

    if (distance < 0) return '00 Days 00:00:00';

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

    return `${days.toString().padStart(2, '0')} Days ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:00`;
  }
}
