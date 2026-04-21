import { Component } from '@angular/core';
import { SidebarComponent } from '../../../sidebar/sidebar.component';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-results-details',
  standalone: true,
  imports: [CommonModule, SidebarComponent],
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

winners = [
  {
    name: 'Thabo Monamane',
    email: 'thabo@gmail.com',
    prize_amount: 5000,
    claim_status: 'pending',
    payment_status: 'pending'
  },
  {
    name: 'Lerato Nkosi',
    email: 'lerato@gmail.com',
    prize_amount: 3000,
    claim_status: 'pending',
    payment_status: 'pending'
  },
  {
    name: 'Neo Mahlakeng',
    email: 'neo@gmail.com',
    prize_amount: 1500,
    claim_status: 'claimed',
    payment_status: 'paid'
  },
  {
    name: 'Kabelo Radebe',
    email: 'kabelo@gmail.com',
    prize_amount: 2000,
    claim_status: 'pending',
    payment_status: 'pending'
  },
  {
    name: 'Palesa Mokoena',
    email: 'palesa@gmail.com',
    prize_amount: 1000,
    claim_status: 'pending',
    payment_status: 'failed'
  }
];

payWinner(w: any) {
  console.log('Paying winner:', w);
}

markClaimed(w: any) {
  console.log('Mark as claimed:', w);
}
}