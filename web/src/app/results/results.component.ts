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
      const response = await fetch(`${environment.apiUrl}/results.php`);
      this.results = await response.json();
    } catch (error) {
      console.error('Error loading results:', error);
    }
  }

  formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }

  getLotteryCode(lottery: string): string {
    if (lottery.includes('Mon')) return 'mon';
    if (lottery.includes('Wed')) return 'wed';
    if (lottery.includes('Fri')) return 'fri';
    return 'mon';
  }
}
