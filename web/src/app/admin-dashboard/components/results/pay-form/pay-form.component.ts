import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-pay-form',
  imports: [CommonModule, FormsModule],
  templateUrl: './pay-form.component.html',
  styleUrl: './pay-form.component.css'
})
export class PayFormComponent {
  @Input() winner: any;
  @Output() cancel = new EventEmitter<void>();
  @Output() pay = new EventEmitter<any>();

  formData = {
    amount: null as number | null,
    payment_method: '',
    transaction_id: ''
  };

  ngOnInit() {
    if (this.winner) {
      this.formData.amount = this.winner.prize_amount;
    }
  }

  onSubmit() {
    if (this.isFormValid()) {
      this.pay.emit({
        winner: this.winner,
        amount: this.formData.amount,
        payment_method: this.formData.payment_method,
        transaction_id: this.formData.transaction_id
      });
    }
  }

  onCancel() {
    this.cancel.emit();
  }

  isFormValid(): boolean {
    return !!(this.formData.amount && 
             this.formData.amount > 0 && 
             this.formData.payment_method);
  }
}
