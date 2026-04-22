import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import { ClaimFormComponent } from '../claim-form/claim-form.component';
import { PayFormComponent } from '../pay-form/pay-form.component';

@Component({
  selector: 'app-winner-list',
  standalone: true,
  imports: [CommonModule, ClaimFormComponent, PayFormComponent],
  templateUrl: './winner-list.component.html',
  styleUrl: './winner-list.component.css'
})
export class WinnerListComponent {

  @Input() winners: any[] = [
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

  @Output() pay = new EventEmitter<any>();
  @Output() claim = new EventEmitter<any>();

  // Modal state
  isPayModalOpen = false;
  isClaimModalOpen = false;
  selectedWinner: any = null;

  payWinner(winner: any) {
    this.selectedWinner = winner;
    this.isPayModalOpen = true;
  }

  markClaimed(winner: any) {
    this.selectedWinner = winner;
    this.isClaimModalOpen = true;
  }

  closePayModal() {
    this.isPayModalOpen = false;
    this.selectedWinner = null;
  }

  closeClaimModal() {
    this.isClaimModalOpen = false;
    this.selectedWinner = null;
  }

  handlePayment(paymentData: any) {
    console.log('Processing payment:', paymentData);
    this.pay.emit(paymentData);
    this.closePayModal();
  }

  handleClaim(claimData: any) {
    console.log('Processing claim:', claimData);
    this.claim.emit(claimData);
    this.closeClaimModal();
  }
}