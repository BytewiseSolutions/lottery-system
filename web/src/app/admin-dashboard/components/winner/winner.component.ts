import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Output } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { ClaimFormComponent } from '../results/claim-form/claim-form.component';
import { PayFormComponent } from '../results/pay-form/pay-form.component';

@Component({
  selector: 'app-winner',
  imports: [
    CommonModule,
    SidebarComponent,
    PayFormComponent,
    ClaimFormComponent
  ],
  templateUrl: './winner.component.html',
  styleUrl: './winner.component.css'
})
export class WinnerComponent {
  isPayModalOpen = false;
  isClaimModalOpen = false;
  selectedWinner: any = null;

  @Output() pay = new EventEmitter<any>();
  @Output() claim = new EventEmitter<any>();

  markClaimed(winner: any) {
    this.selectedWinner = winner;
    this.isClaimModalOpen = true;
  }
    payWinner(winner: any) {
    this.selectedWinner = winner;
    this.isPayModalOpen = true;
  }
  winners: any[] = [
    {
      id: 1,
      name: 'Thabo Monamane',
      email: 'thabo@gmail.com',
      phone: '+266 5123 4567',
      prize_amount: 5000,
      prize_tier: 1,
      claim_status: 'pending',
      payment_status: 'pending',
      date_won: '2024-01-15',
      draw_id: 1
    },
    {
      id: 2,
      name: 'Lerato Nkosi',
      email: 'lerato@gmail.com',
      phone: '+266 5234 5678',
      prize_amount: 3000,
      prize_tier: 2,
      claim_status: 'pending',
      payment_status: 'pending',
      date_won: '2024-01-15',
      draw_id: 1
    },
    {
      id: 3,
      name: 'Neo Mahlakeng',
      email: 'neo@gmail.com',
      phone: '+266 5345 6789',
      prize_amount: 1500,
      prize_tier: 3,
      claim_status: 'claimed',
      payment_status: 'paid',
      date_won: '2024-01-08',
      draw_id: 2
    },
    {
      id: 4,
      name: 'Kabelo Radebe',
      email: 'kabelo@gmail.com',
      phone: '+266 5456 7890',
      prize_amount: 2000,
      prize_tier: 2,
      claim_status: 'pending',
      payment_status: 'pending',
      date_won: '2024-01-15',
      draw_id: 1
    },
    {
      id: 5,
      name: 'Palesa Mokoena',
      email: 'palesa@gmail.com',
      phone: '+266 5567 8901',
      prize_amount: 1000,
      prize_tier: 3,
      claim_status: 'pending',
      payment_status: 'failed',
      date_won: '2024-01-08',
      draw_id: 2
    }
  ];
  handleClaim(claimData: any) {
    console.log('Processing claim:', claimData);
    if (this.selectedWinner) {
      this.selectedWinner.claim_status = 'claimed';
    }
    this.claim.emit(claimData);
    this.closeClaimModal();
  }
  handlePayment(paymentData: any) {
    console.log('Processing payment:', paymentData);
    if (this.selectedWinner) {
      this.selectedWinner.payment_status = 'paid';
    }
    this.pay.emit(paymentData);
    this.closePayModal();
  }
    closeClaimModal() {
    this.isClaimModalOpen = false;
    this.selectedWinner = null;
  }
   closePayModal() {
    this.isPayModalOpen = false;
    this.selectedWinner = null;
  }
}
