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
  isLoading = false;
  
  // Password visibility
  showPassword = false;
  showConfirmPassword = false;
  
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
  isVerifying = false;

  close() {
    this.clearForm();
    this.closeModal.emit();
  }

  switchToLogin() {
    this.clearForm();
    this.switchToLoginEvent.emit();
  }

  clearForm() {
    this.fullName = '';
    this.email = '';
    this.phone = '';
    this.password = '';
    this.confirmPassword = '';
    this.agreeTerms = false;
    this.showPassword = false;
    this.showConfirmPassword = false;
    this.showOtpVerification = false;
    this.emailOtp = '';
    this.phoneOtp = '';
  }

  async onSignup() {
    if (!this.fullName || !this.password || !this.confirmPassword) {
      this.toastService.showError('Please fill in all required fields');
      return;
    }
    
    if (!this.email && !this.phone) {
      this.toastService.showError('Please provide either email or phone number');
      return;
    }
    
    if (!this.agreeTerms) {
      this.toastService.showError('Please agree to the terms and conditions');
      return;
    }

    this.isLoading = true;
    
    try {
      const response = await fetch(`${environment.apiUrl}/register.php`, {
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
        
        // Clear sensitive data
        this.password = '';
        this.confirmPassword = '';
        
        this.toastService.showSuccess(result.message);
      } else {
        this.toastService.showError(result.error);
      }
    } catch (error) {
      console.error('Registration error:', error);
      this.toastService.showError('Network error. Please check your connection.');
    } finally {
      this.isLoading = false;
    }
  }
  
  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }
  
  toggleConfirmPasswordVisibility() {
    this.showConfirmPassword = !this.showConfirmPassword;
  }
  
  async verifyOtp(type: 'email' | 'phone') {
    const otpCode = type === 'email' ? this.emailOtp : this.phoneOtp;
    
    if (!otpCode || otpCode.length !== 6) {
      this.toastService.showError('Please enter a valid 6-digit OTP');
      return;
    }

    this.isVerifying = true;
    
    try {
      const response = await fetch(`${environment.apiUrl}/verify-otp.php`, {
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
          this.emailOtp = '';
        } else {
          this.phoneVerified = true;
          this.requiresPhoneVerification = false;
          this.phoneOtp = '';
        }
        
        this.allVerified = result.fullyVerified;
        this.toastService.showSuccess(result.message);
        
        // Auto-redirect to login when fully verified
        if (this.allVerified) {
          setTimeout(() => {
            this.close();
            this.switchToLogin();
          }, 1500);
        }
      } else {
        this.toastService.showError(result.error);
      }
    } catch (error) {
      this.toastService.showError('Network error. Please try again.');
    } finally {
      this.isVerifying = false;
    }
  }
  
  async resendOtp(type: 'email' | 'phone') {
    try {
      const response = await fetch(`${environment.apiUrl}/resend-otp.php`, {
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
      this.toastService.showError('Network error. Please try again.');
    }
  }
}