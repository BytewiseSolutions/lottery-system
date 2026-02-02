import { Component, OnInit } from '@angular/core';
import { LotteryService } from '../../services/lottery.service';

@Component({
  selector: 'app-results',
  templateUrl: './results.component.html',
  styleUrls: ['./results.component.scss'],
  standalone: false,
})
export class ResultsComponent  implements OnInit {
  results: any[] = [];

  constructor(private lottery: LotteryService) {}

  ngOnInit() {
    this.loadResults();
  }

  loadResults() {
    this.lottery.getResults().subscribe({
      next: (results: any) => this.results = Array.isArray(results) ? results : [],
      error: (err) => console.error('Error loading results:', err)
    });
  }

  parseNumbers(numbersJson: any): number[] {
    if (Array.isArray(numbersJson)) return numbersJson;
    try {
      return JSON.parse(numbersJson);
    } catch {
      return [];
    }
  }
}
