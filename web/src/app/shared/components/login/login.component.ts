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
  @Output() switchToVerificationEvent = new EventEmitter<void>();
  @Output() switchToPasswordRecoveryEvent = new EventEmitter<void>();

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

  switchToVerification() {
    this.clearForm();
    this.switchToVerificationEvent.emit();
  }

  switchToPasswordRecovery() {
    this.clearForm();
    this.switchToPasswordRecoveryEvent.emit();
  }

  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }

  clearForm() {
    this.identifier = '';
    this.password = '';
    this.showPassword = false;
    this.errorMessage = '';
    this.isLoading = false;
  }

  async onLogin() {
    if (!this.identifier || !this.password) {
      this.errorMessage = 'Please enter both email/phone and password';
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';

    try {
      const controller = new AbortController();
      const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 second timeout
      
      const response = await fetch(`${environment.apiUrl}/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ identifier: this.identifier, password: this.password }),
        signal: controller.signal
      });
      
      clearTimeout(timeoutId);
      
      let result;
      try {
        result = await response.json();
      } catch (parseError) {
        this.errorMessage = 'Server error. Please try again later.';
        this.isLoading = false;
        return;
      }

      if (response.ok && result.success) {
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
        // Handle different HTTP status codes
        if (response.status === 400) {
          this.errorMessage = result?.error || 'Invalid credentials. Please check your email/phone and password.';
        } else if (response.status === 429) {
          this.errorMessage = 'Too many login attempts. Please try again later.';
        } else if (response.status === 500) {
          this.errorMessage = 'Server error. Please try again later.';
        } else {
          this.errorMessage = result?.error || 'Login failed. Please try again.';
        }
      }
    } catch (error: any) {
      if (error.name === 'AbortError') {
        this.errorMessage = 'Request timeout. Please try again.';
      } else {
        this.errorMessage = 'Network error. Please check your connection and try again.';
      }
    } finally {
      this.isLoading = false;
    }
  }
}