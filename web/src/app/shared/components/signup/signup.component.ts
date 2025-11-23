import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-signup',
  imports: [CommonModule, FormsModule],
  templateUrl: './signup.component.html',
  styleUrl: './signup.component.css'
})
export class SignupComponent {
  @Input() isVisible = false;
  @Output() signupSuccess = new EventEmitter<any>();
  @Output() closeModal = new EventEmitter<void>();
  @Output() switchToLoginEvent = new EventEmitter<void>();

  email = '';
  password = '';
  confirmPassword = '';
  agreeTerms = false;

  close() {
    this.closeModal.emit();
  }

  switchToLogin() {
    this.switchToLoginEvent.emit();
  }

  async onSignup() {
    try {
      const response = await fetch(`${environment.apiUrl}/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email: this.email,
          password: this.password,
          confirmPassword: this.confirmPassword
        })
      });

      const result = await response.json();

      if (result.success) {
        localStorage.setItem('token', result.token);
        localStorage.setItem('user', JSON.stringify(result.user));
        this.signupSuccess.emit(result.user);
        this.close();
        alert('Registration successful!');
      } else {
        alert(result.error);
      }
    } catch (error) {
      alert('Registration failed');
    }
  }
}