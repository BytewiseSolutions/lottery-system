import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LotteryService } from '../../../services/lottery.service';

@Component({
  selector: 'app-activity-logs',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './activity-logs.component.html',
  styleUrls: ['./activity-logs.component.scss']
})
export class ActivityLogsComponent implements OnInit {
  logs: any[] = [];
  paginatedLogs: any[] = [];
  currentPage = 1;
  totalPages = 1;
  itemsPerPage = 20;

  constructor(private lotteryService: LotteryService) {}

  ngOnInit() {
    this.loadLogs();
  }

  loadLogs() {
    this.lotteryService.getActivityLogs().subscribe({
      next: (logs) => {
        this.logs = logs;
        this.updatePagination();
      },
      error: (error) => {
        console.error('Error loading logs:', error);
        this.logs = [];
      }
    });
  }

  updatePagination() {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const endIndex = startIndex + this.itemsPerPage;
    this.paginatedLogs = this.logs.slice(startIndex, endIndex);
    this.totalPages = Math.ceil(this.logs.length / this.itemsPerPage);
  }

  onPageChange(page: number) {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.updatePagination();
    }
  }
}