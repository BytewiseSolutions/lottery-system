import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-login',
  imports: [CommonModule, FormsModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {
  @Input() isVisible = false;
  @Output() loginSuccess = new EventEmitter<any>();
  @Output() closeModal = new EventEmitter<void>();
  @Output() switchToSignupEvent = new EventEmitter<void>();

  identifier = '';
  password = '';
  showPassword = false;
  isLoading = false;
  errorMessage = '';

  close() {
    this.clearForm();
    this.closeModal.emit();
  }

  switchToSignup() {
    this.clearForm();
    this.switchToSignupEvent.emit();
  }

  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }

  clearForm() {
    this.identifier = '';
    this.password = '';
    this.showPassword = false;
    this.errorMessage = '';
  }

  async onLogin() {
    if (!this.identifier || !this.password) {
      this.errorMessage = 'Please enter both email/phone and password';
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';

    try {
      const response = await fetch(`${environment.apiUrl}/login.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ identifier: this.identifier, password: this.password })
      });

      const result = await response.json();

      if (result.success) {
        localStorage.setItem('token', result.token);
        localStorage.setItem('user', JSON.stringify(result.user));
        
        this.clearForm();
        
        // Check if admin and redirect to admin dashboard
        if (result.user.role === 'admin' || result.user.email === 'admin@totalfreelotto.com') {
          window.location.href = '/app/#/dashboard';
          return;
        }
        
        this.loginSuccess.emit(result.user);
        this.close();
      } else {
        this.errorMessage = result.error || 'Login failed. Please try again.';
      }
    } catch (error) {
      this.errorMessage = 'Network error. Please check your connection.';
    } finally {
      this.isLoading = false;
    }
  }
}