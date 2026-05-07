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
  @Input() error = false;
  @Input() processingWinnerId: number | null = null;
  @Input() processingAction: 'pay' | 'claim' | null = null;

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
    this.pay.emit(paymentData);
    this.closePayModal();
  }

  handleClaim(claimData: any) {
    this.claim.emit(claimData);
    this.closeClaimModal();
  }

  canPay(winner: any): boolean {
    return winner?.claim_status === 'claimed' && winner?.payment_status !== 'paid' && !this.isProcessingAnotherWinner(winner, 'pay');
  }

  canClaim(winner: any): boolean {
    return winner?.claim_status !== 'claimed' && !this.isProcessingAnotherWinner(winner, 'claim');
  }

  getPayLabel(winner: any): string {
    if (this.processingWinnerId === Number(winner?.id) && this.processingAction === 'pay') {
      return 'Processing...';
    }

    if (winner?.payment_status === 'paid') {
      return 'Paid';
    }

    if (winner?.claim_status !== 'claimed') {
      return 'Awaiting Claim';
    }

    return 'Pay';
  }

  getClaimLabel(winner: any): string {
    if (this.processingWinnerId === Number(winner?.id) && this.processingAction === 'claim') {
      return 'Updating...';
    }

    return winner?.claim_status === 'claimed' ? 'Claimed' : 'Claim';
  }

  private isProcessingAnotherWinner(winner: any, action: 'pay' | 'claim'): boolean {
    if (!this.processingAction) {
      return false;
    }

    return this.processingAction === action || this.processingWinnerId === Number(winner?.id);
  }
}
