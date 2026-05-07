import { Component, EventEmitter, Input, Output, SimpleChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { UserFormValue } from '../value';

@Component({
  selector: 'app-user-form',
   imports: [CommonModule, FormsModule],
  templateUrl: './user-form.component.html',
  styleUrl: './user-form.component.css'
})
export class UserFormComponent {
  @Input() initialValue: Partial<UserFormValue> | null = null;
  @Input() isEditMode = false;
  @Input() loading = false;
  @Input() errorMessage = '';

  @Output() cancel = new EventEmitter<void>();
  @Output() saveForm = new EventEmitter<UserFormValue>();

  formModel: UserFormValue = this.createEmptyForm();

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['initialValue'] || changes['isEditMode']) {
      this.formModel = {
        ...this.createEmptyForm(),
        ...(this.initialValue ?? {})
      };
    }
  }

  submit(): void {
    this.saveForm.emit({
      ...this.formModel,
      full_name: this.formModel.full_name.trim(),
      email: this.formModel.email.trim(),
      phone: this.formModel.phone.trim(),
      country: this.formModel.country.trim()
    });
  }

  private createEmptyForm(): UserFormValue {
    return {
      full_name: '',
      email: '',
      phone: '',
      country: '',
      password: '',
      role: 'user',
      is_active: true,
      email_verified: false,
      phone_verified: false
    };
  }
}
