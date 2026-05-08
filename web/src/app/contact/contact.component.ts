import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { LayoutComponent } from '../layout/layout.component';
import { BackendService } from '../util/backend.service';
import { ErrorHandlerService } from '../util/error-handler.service';
import { SuccessPopupService } from '../util/success-popup.service';

@Component({
  selector: 'app-contact',
  imports: [CommonModule, FormsModule, LayoutComponent],
  templateUrl: './contact.component.html',
  styleUrl: './contact.component.css'
})
export class ContactComponent {
  contactForm = {
    name: '',
    email: '',
    message: ''
  };
  
  isSubmitting = false;

  constructor(
    private backendService: BackendService,
    private errorHandlerService: ErrorHandlerService,
    private successPopupService: SuccessPopupService
  ) {}

  async submitForm() {
    if (!this.contactForm.name || !this.contactForm.email || !this.contactForm.message) {
      this.errorHandlerService.showError('Please fill in all fields');
      return;
    }

    this.isSubmitting = true;

    try {
      const result = await new Promise<any>((resolve, reject) => {
        const request = this.backendService.submitContactMessage(this.contactForm).subscribe({
          next: (response) => {
            request.unsubscribe();
            resolve(response);
          },
          error: (error) => {
            request.unsubscribe();
            reject(error);
          }
        });
      });

      if (result.success) {
        this.contactForm = { name: '', email: '', message: '' };
        this.successPopupService.show(
          result.message || 'Thank you! Your message has been sent successfully.',
          'Message Sent'
        );
      } else {
        this.errorHandlerService.showError(result.message || 'Failed to send message');
      }
    } catch (error: any) {
      this.errorHandlerService.showError(
        error?.error?.message || 'Network error. Please try again.'
      );
    } finally {
      this.isSubmitting = false;
    }
  }
}
