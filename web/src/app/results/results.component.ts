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

  ngOnInit() {
    this.loadResults();
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
    if (!lottery || typeof lottery !== 'string') return 'mon';
    if (lottery.includes('Mon')) return 'mon';
    if (lottery.includes('Wed')) return 'wed';
    if (lottery.includes('Fri')) return 'fri';
    return 'mon';
  }
}
