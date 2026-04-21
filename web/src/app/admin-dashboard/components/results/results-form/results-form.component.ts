import { Component, EventEmitter, OnInit, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-results-form',
  imports: [CommonModule, FormsModule],
  templateUrl: './results-form.component.html',
  styleUrl: './results-form.component.css'
})
export class ResultsFormComponent implements OnInit {
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

  ngOnInit() {
    this.loadDraws();
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
    return !!(this.formData.draw_id && 
             this.formData.winning_numbers.every(n => n !== null && n > 0) &&
             this.formData.jackpot !== null && 
             this.formData.jackpot >= 0);
  }
}
