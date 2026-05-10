import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-claim-form',
  imports: [CommonModule, FormsModule],
  templateUrl: './claim-form.component.html',
  styleUrl: './claim-form.component.css'
})
export class ClaimFormComponent {
  @Input() winner: any;
  @Output() cancel = new EventEmitter<void>();
  @Output() claim = new EventEmitter<any>();

  formData = {
    confirmed: false
  };

  onSubmit() {
    if (this.isFormValid()) {
      this.claim.emit({
        winner: this.winner
      });
    }
  }

  onCancel() {
    this.cancel.emit();
  }

  isFormValid(): boolean {
    return this.formData.confirmed;
  }
}
