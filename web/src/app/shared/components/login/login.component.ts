import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { BackendService } from '../../../util/backend.service';
import { SiteSettingsService } from '../../../services/site-settings.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
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

  showLogin = true;
  showSignup = false;
  showPasswordReset = false;

  validationErrors: any = {};

  constructor(
    private router: Router,
    private backendService: BackendService,
    public siteSettingsService: SiteSettingsService
  ) { }

  close(): void {
    this.clearForm();
    this.closeModal.emit();
  }

  switchToSignup(): void {
    this.clearForm();
    this.switchToSignupEvent.emit();
  }

  switchToVerification(): void {
    this.clearForm();
    this.switchToVerificationEvent.emit();
  }

  switchToPasswordRecovery(event?: Event): void {
    event?.preventDefault();
    this.clearForm();
    this.switchToPasswordRecoveryEvent.emit();
  }

  clearForm(): void {
    this.identifier = '';
    this.password = '';
    this.showPassword = false;
    this.errorMessage = '';
    this.isLoading = false;
    this.validationErrors = {};
  }
  openForgotPassword() {
    this.showLogin = false;
    this.showSignup = false;
    this.showPasswordReset = true;
  }
  onLogin(): void {
    this.validationErrors = {};

    if (!this.identifier.trim()) {
      this.validationErrors.identifier =
        'Email or phone number is required';
    }

    if (!this.password.trim()) {
      this.validationErrors.password =
        'Password is required';
    }

    if (Object.keys(this.validationErrors).length > 0) {
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';

    this.backendService.login({
      identifier: this.identifier,
      password: this.password
    }).subscribe({
      next: (response: any) => {
        this.isLoading = false;

        if (response?.success) {
          const loginData = response?.data ?? response;
          const token = loginData?.token;
          const user = loginData?.user;

          if (!token || !user) {
            this.errorMessage =
              response?.message ||
              'Login failed. Please try again.';
            return;
          }

          localStorage.setItem(
            'auth_token',
            token
          );

          localStorage.setItem(
            'token',
            token
          );

          localStorage.setItem(
            'user',
            JSON.stringify(user)
          );

          this.clearForm();

          if (
            user?.role === 'admin' ||
            user?.email === 'admin@totalfreelotto.com'
          ) {
            this.router.navigate(['/admin-dashboard']);
            return;
          }

          this.loginSuccess.emit(user);
          this.close();
        } else {
          this.errorMessage =
            response?.message ||
            'Login failed. Please try again.';
        }
      },

      error: (error) => {
        this.isLoading = false;

        if (error.status === 400) {
          this.errorMessage =
            error?.error?.message ||
            'Invalid credentials.';
        } else if (error.status === 429) {
          this.errorMessage =
            'Too many login attempts. Please try again later.';
        } else if (error.status === 0) {
          this.errorMessage =
            'Network error. Please check your connection.';
        } else {
          this.errorMessage =
            error?.error?.message ||
            'Server error. Please try again later.';
        }
      }
    });
  }
}
