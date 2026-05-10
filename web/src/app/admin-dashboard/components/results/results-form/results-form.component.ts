import { Component, EventEmitter, Input, Output, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { BackendService } from '../../../../util/backend.service';

interface AdminDrawOption {
  id: number;
  lottery: string;
  draw_date: string;
  jackpot?: number | string;
}

interface ResultFormData {
  draw_id: string | number;
  winning_numbers: Array<number | null>;
  bonus_numbers: Array<number | null>;
  jackpot: number | null;
  winners_count: number;
  status: string;
}

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
  @Input() saving: boolean = false;

  @Output() cancel = new EventEmitter<void>();
  @Output() save = new EventEmitter<any>();

  draws: AdminDrawOption[] = [];
  drawsLoading = false;
  autofillLoading = false;
  autofillMessage = '';
  autofillMessageType: 'info' | 'success' | 'error' = 'info';

  constructor(private backendService: BackendService) {}

  formData: ResultFormData = {
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
    this.drawsLoading = true;

    this.backendService.getPastDraws().subscribe({
      next: (response: any) => {
        const drawList = Array.isArray(response?.data)
          ? response.data
          : Array.isArray(response)
            ? response
            : [];

        this.draws = drawList;

        if (this.editData?.draw_id && !this.draws.some(draw => Number(draw.id) === Number(this.editData.draw_id))) {
          this.draws = [
            {
              id: Number(this.editData.draw_id),
              lottery: this.editData.lottery || 'Existing Draw',
              draw_date: this.editData.draw_date || ''
            },
            ...this.draws
          ];
        }

        this.drawsLoading = false;

        if (!this.isEditMode && this.formData.draw_id) {
          this.onDrawChange(this.formData.draw_id);
        }
      },
      error: (error) => {
        console.error('Failed to load draws:', error);
        this.draws = [];
        this.drawsLoading = false;
      }
    });
  }

  onSubmit() {
    if (this.isFormValid()) {
      this.save.emit(this.formData);
    }
  }

  onCancel() {
    this.cancel.emit();
  }

  onDrawChange(drawId: string | number) {
    this.autofillMessage = '';
    this.autofillMessageType = 'info';

    const selectedDraw = this.draws.find(draw => Number(draw.id) === Number(drawId));

    if (selectedDraw?.jackpot !== undefined && selectedDraw?.jackpot !== null) {
      this.formData.jackpot = Number(selectedDraw.jackpot);
    }

    if (!drawId) {
      return;
    }

    this.autofillLoading = true;
    this.autofillMessage = 'Auto-filling numbers and jackpot for the selected draw...';
    this.autofillMessageType = 'info';

    this.backendService.getHighestVoteForDraw(drawId).subscribe({
      next: (response: any) => {
        if (response?.success && response.data) {
          const winningNumbers = Array.isArray(response.data.winning_numbers)
            ? response.data.winning_numbers
            : [];
          const bonusNumbers = Array.isArray(response.data.bonus_numbers)
            ? response.data.bonus_numbers
            : [];

          this.formData.winning_numbers = this.padNumbers(winningNumbers, 5);
          this.formData.bonus_numbers = this.padNumbers(bonusNumbers, 2);

          if (response.data.jackpot !== undefined && response.data.jackpot !== null) {
            this.formData.jackpot = Number(response.data.jackpot);
          }

          this.autofillMessage = 'Winning numbers, bonus numbers, and jackpot were auto-filled for this draw.';
          this.autofillMessageType = 'success';
        } else {
          this.autofillMessage = 'No highest-vote data was found for this draw.';
          this.autofillMessageType = 'error';
        }

        this.autofillLoading = false;
      },
      error: (error) => {
        console.error('Failed to auto-fill result data:', error);
        this.autofillLoading = false;
        this.autofillMessage = 'Auto-fill failed for this draw.';
        this.autofillMessageType = 'error';
      }
    });
  }

  isFormValid(): boolean {
    return !!(
      this.formData.draw_id &&
      this.hasValidMainNumbers() &&
      this.hasValidBonusNumbers() &&
      this.formData.jackpot !== null &&
      Number(this.formData.jackpot) >= 0
    );
  }

  getValidationMessage(): string {
    if (!this.hasValidMainNumbers()) {
      return 'Winning numbers must be 5 unique numbers between 1 and 75.';
    }

    if (!this.hasValidBonusNumbers()) {
      return 'Bonus numbers must be 2 unique numbers between 1 and 75 and different from the winning numbers.';
    }

    if (this.formData.jackpot === null || Number(this.formData.jackpot) < 0) {
      return 'Jackpot amount must be zero or more.';
    }

    return '';
  }

  formatDrawDate(dateString: string): string {
    const date = new Date(dateString);

    if (Number.isNaN(date.getTime())) {
      return dateString;
    }

    return date.toLocaleString('en-GB', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    });
  }

  private padNumbers(numbers: number[], size: number): Array<number | null> {
    const normalized = numbers.map(number => Number(number) || null).slice(0, size);

    while (normalized.length < size) {
      normalized.push(null);
    }

    return normalized;
  }

  private hasValidMainNumbers(): boolean {
    return this.isValidNumberGroup(this.formData.winning_numbers, 5);
  }

  private hasValidBonusNumbers(): boolean {
    if (!this.isValidNumberGroup(this.formData.bonus_numbers, 2)) {
      return false;
    }

    const mainNumbers = this.formData.winning_numbers.map((num) => Number(num));
    const bonusNumbers = this.formData.bonus_numbers.map((num) => Number(num));

    return !bonusNumbers.some((number) => mainNumbers.includes(number));
  }

  private isValidNumberGroup(numbers: Array<number | null>, expectedCount: number): boolean {
    const normalized = numbers.map((num) => Number(num));

    if (normalized.length !== expectedCount) {
      return false;
    }

    if (normalized.some((num) => !Number.isInteger(num) || num < 1 || num > 75)) {
      return false;
    }

    return new Set(normalized).size === expectedCount;
  }
}
