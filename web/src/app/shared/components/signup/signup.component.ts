import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ToastService } from '../../../services/toast.service';
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

  constructor(private toastService: ToastService) {}

  fullName = '';
  email = '';
  phone = '';
  password = '';
  confirmPassword = '';
  agreeTerms = false;
  
  // OTP verification
  showOtpVerification = false;
  userId: number | null = null;
  requiresEmailVerification = false;
  requiresPhoneVerification = false;
  selectedVerificationMethod: 'email' | 'phone' = 'email';
  emailOtp = '';
  phoneOtp = '';
  emailVerified = false;
  phoneVerified = false;
  allVerified = false;

  close() {
    this.closeModal.emit();
  }

  switchToLogin() {
    this.switchToLoginEvent.emit();
  }

  async onSignup() {
    if (!this.email && !this.phone) {
      this.toastService.showError('Please provide either email or phone number');
      return;
    }
    
    try {
      const response = await fetch(`${environment.apiUrl}/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          fullName: this.fullName,
          email: this.email || null,
          phone: this.phone || null,
          password: this.password,
          confirmPassword: this.confirmPassword
        })
      });

      const result = await response.json();

      if (result.success) {
        this.userId = result.userId;
        this.requiresEmailVerification = result.requiresVerification.includes('email');
        this.requiresPhoneVerification = result.requiresVerification.includes('phone');
        this.selectedVerificationMethod = this.requiresEmailVerification ? 'email' : 'phone';
        this.showOtpVerification = true;
        this.toastService.showSuccess(result.message);
      } else {
        this.toastService.showError(result.error);
      }
    } catch (error) {
      this.toastService.showError('Registration failed');
    }
  }
  
  async verifyOtp(type: 'email' | 'phone') {
    const otpCode = type === 'email' ? this.emailOtp : this.phoneOtp;
    
    if (!otpCode || otpCode.length !== 6) {
      this.toastService.showError('Please enter a valid 6-digit OTP');
      return;
    }
    
    try {
      const response = await fetch(`${environment.apiUrl}/verify-otp`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          userId: this.userId,
          otpCode: otpCode,
          otpType: type
        })
      });

      const result = await response.json();

      if (result.success) {
        if (type === 'email') {
          this.emailVerified = true;
          this.requiresEmailVerification = false;
        } else {
          this.phoneVerified = true;
          this.requiresPhoneVerification = false;
        }
        
        this.allVerified = result.fullyVerified;
        this.toastService.showSuccess(result.message);
        
        // Auto-redirect to login when fully verified
        if (this.allVerified) {
          setTimeout(() => {
            this.switchToLogin();
          }, 2000);
        }
      } else {
        this.toastService.showError(result.error);
      }
    } catch (error) {
      this.toastService.showError('Verification failed');
    }
  }
  
  async resendOtp(type: 'email' | 'phone') {
    try {
      const response = await fetch(`${environment.apiUrl}/resend-otp`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          userId: this.userId,
          otpType: type
        })
      });

      const result = await response.json();
      if (result.success) {
        this.toastService.showSuccess(result.message);
      } else {
        this.toastService.showError(result.error);
      }
    } catch (error) {
      this.toastService.showError('Failed to resend OTP');
    }
  }
}