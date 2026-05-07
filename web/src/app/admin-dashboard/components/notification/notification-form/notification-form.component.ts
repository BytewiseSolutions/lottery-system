import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { NotificationCreateRequest } from '../notification-request';
import { NotificationType } from '../notification-type';
import { RecipientType } from '../recipient-type';

@Component({
  selector: 'app-notification-form',
  imports: [CommonModule, FormsModule],
  templateUrl: './notification-form.component.html',
  styleUrl: './notification-form.component.css'
})
export class NotificationFormComponent {
  @Input() isOpen = false;
  @Input() loading = false;
  @Output() cancel = new EventEmitter<void>();
  @Output() save = new EventEmitter<NotificationCreateRequest>();

  formData: NotificationCreateRequest = {
    title: '',
    message: '',
    type: 'info' as NotificationType,
    recipient_type: 'all' as RecipientType
  };

  onCancel() {
    this.resetForm();
    this.cancel.emit();
  }

  onSubmit() {
    if (!this.formData.title.trim() || !this.formData.message.trim()) {
      return;
    }
    this.save.emit({ ...this.formData });
  }

  resetForm() {
    this.formData = {
      title: '',
      message: '',
      type: 'info' as NotificationType,
      recipient_type: 'all' as RecipientType
    };
  }
}