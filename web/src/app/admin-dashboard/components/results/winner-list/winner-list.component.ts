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

  @Input() winners: any[] = [];
  @Input() loading = false;

  @Output() pay = new EventEmitter<any>();
  @Output() claim = new EventEmitter<any>();

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
