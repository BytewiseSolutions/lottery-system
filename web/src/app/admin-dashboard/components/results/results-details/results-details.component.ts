import { Component } from '@angular/core';
import { SidebarComponent } from '../../../sidebar/sidebar.component';
import { CommonModule } from '@angular/common';
import { WinnerListComponent } from '../winner-list/winner-list.component';

@Component({
  selector: 'app-results-details',
  standalone: true,
  imports: [
    CommonModule, 
    SidebarComponent,
    WinnerListComponent
  ],
  templateUrl: './results-details.component.html',
  styleUrl: './results-details.component.css'
})
export class ResultsDetailsComponent {

  activeTab: string = 'winners';

  result: any = {
    lottery_name: 'Monday Lotto',
    draw_date: new Date(),
    jackpot: 1568,
    status: 'published',
    winning_numbers: [5, 10, 18, 27, 44],
    bonus_numbers: [3, 9],
    created_at: new Date(),
    updated_at: new Date(),
    total_entries: 1200
  };

  payWinner(w: any) {
    console.log('Paying winner:', w);
  }

  markClaimed(w: any) {
    console.log('Mark as claimed:', w);
  }
}