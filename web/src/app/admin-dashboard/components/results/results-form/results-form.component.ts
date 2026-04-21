import { Component, EventEmitter, Input, Output, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-results-form',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './results-form.component.html',
  styleUrl: './results-form.component.css'
})
export class ResultsFormComponent implements OnInit {

  @Input() editData: any = null;
  @Input() isEditMode: boolean = false;

  @Output() cancel = new EventEmitter<void>();
  @Output() save = new EventEmitter<any>();

  draws: any[] = [];

  formData = {
    draw_id: '',
    winning_numbers: [null, null, null, null, null],
    bonus_numbers: [null, null],
    jackpot: null,
    winners_count: 0,
    status: 'published'
  };

  ngOnInit(): void {
    this.loadDraws();

    if (this.editData) {
      this.formData = {
        draw_id: this.editData.draw_id || '',
        winning_numbers: [...this.editData.winning_numbers],
        bonus_numbers: [...this.editData.bonus_numbers],
        jackpot: this.editData.jackpot,
        winners_count: this.editData.winners_count,
        status: this.editData.status
      };
    }
  }

  loadDraws() {
    this.draws = [];
  }

  onSubmit() {
    if (this.isFormValid()) {
      this.save.emit(this.formData);
    }
  }

  onCancel() {
    this.cancel.emit();
  }

  isFormValid(): boolean {
    return !!(
      this.formData.draw_id &&
      this.formData.winning_numbers.every(
        (num) => num !== null && Number(num) > 0
      ) &&
      this.formData.jackpot !== null &&
      Number(this.formData.jackpot) >= 0
    );
  }
}