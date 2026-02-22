import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { LayoutComponent } from '../layout/layout.component';
import { environment } from '../../environments/environment';

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
  showSuccess = false;
  errorMessage = '';

  async submitForm() {
    if (!this.contactForm.name || !this.contactForm.email || !this.contactForm.message) {
      this.errorMessage = 'Please fill in all fields';
      return;
    }

    this.isSubmitting = true;
    this.errorMessage = '';

    try {
      const response = await fetch(`${environment.apiUrl}/contact`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(this.contactForm)
      });

      const result = await response.json();

      if (result.success) {
        this.showSuccess = true;
        this.contactForm = { name: '', email: '', message: '' };
        setTimeout(() => this.showSuccess = false, 5000);
      } else {
        this.errorMessage = result.error || 'Failed to send message';
      }
    } catch (error) {
      this.errorMessage = 'Network error. Please try again.';
    } finally {
      this.isSubmitting = false;
    }
  }
}
