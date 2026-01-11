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
  @Input() verificationMode = false;
  @Input() passwordRecoveryMode = false;
  @Output() signupSuccess = new EventEmitter<any>();
  @Output() closeModal = new EventEmitter<void>();
  @Output() switchToLoginEvent = new EventEmitter<void>();

  // Validation errors
  validationErrors: any = {};

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
  errorMessage = '';
  identifier = ''; // For verification mode
  showSuccessMessage = false;

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
    this.errorMessage = '';
    this.verificationMode = false;
    this.validationErrors = {};
    // Reset verification states
    this.emailVerified = false;
    this.phoneVerified = false;
    this.allVerified = false;
    this.requiresEmailVerification = false;
    this.requiresPhoneVerification = false;
  }

  switchToRegularSignup() {
    this.verificationMode = false;
    this.errorMessage = '';
  }

  updateOtpCode(event: any) {
    const value = event.target.value;
    if (this.selectedVerificationMethod === 'email') {
      this.emailOtp = value;
    } else {
      this.phoneOtp = value;
    }
  }

  async verifyAccountByIdentifier() {
    if (!this.identifier) {
      this.errorMessage = 'Please enter your email or phone number';
      return;
    }
    
    const otpCode = this.selectedVerificationMethod === 'email' ? this.emailOtp : this.phoneOtp;
    if (!otpCode || otpCode.length !== 6) {
      this.errorMessage = 'Please enter a valid 6-digit OTP';
      return;
    }

    // First, find the user ID by email/phone
    // For now, we'll use a placeholder - this would need an API endpoint to find user by identifier
    // You can use the existing OTP codes with known user IDs for testing
    this.userId = 1; // Temporary - should be fetched from API
    
    // Then verify the OTP
    await this.verifyOtp(this.selectedVerificationMethod);
  }

  async sendPasswordReset() {
    if (!this.identifier) {
      this.errorMessage = 'Please enter your email or phone number';
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';

    try {
      // This would call a password reset API endpoint
      // For now, just show a success message
      this.errorMessage = '';
      
      // Show success state instead of toast
      this.showSuccessMessage = true;
      
      // Auto-redirect to login after 1 second
      setTimeout(() => {
        this.close();
        this.switchToLogin();
      }, 1000);
    } catch (error) {
      this.errorMessage = 'Failed to send reset instructions. Please try again.';
    } finally {
      this.isLoading = false;
    }
  }

  async onSignup() {
    this.validationErrors = {};
    
    // Validate fields
    if (!this.fullName) {
      this.validationErrors.fullName = 'Full name is required';
    }
    
    if (!this.email && !this.phone) {
      this.validationErrors.contact = 'Please provide either email or phone number';
    }
    
    if (!this.password) {
      this.validationErrors.password = 'Password is required';
    } else if (this.password.length < 6) {
      this.validationErrors.password = 'Password must be at least 6 characters';
    }
    
    if (!this.confirmPassword) {
      this.validationErrors.confirmPassword = 'Please confirm your password';
    } else if (this.password !== this.confirmPassword) {
      this.validationErrors.confirmPassword = 'Passwords do not match';
    }
    
    if (!this.agreeTerms) {
      this.validationErrors.terms = 'Please agree to the terms and conditions';
    }
    
    // If there are validation errors, don't proceed
    if (Object.keys(this.validationErrors).length > 0) {
      return;
    }

    this.isLoading = true;
    
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
        
        // Only require verification for one method - prioritize email if both are provided
        if (this.email) {
          this.requiresEmailVerification = true;
          this.requiresPhoneVerification = false;
          this.selectedVerificationMethod = 'email';
        } else if (this.phone) {
          this.requiresEmailVerification = false;
          this.requiresPhoneVerification = true;
          this.selectedVerificationMethod = 'phone';
        }
        
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
      this.errorMessage = 'Please enter a valid 6-digit OTP';
      return;
    }

    this.isVerifying = true;
    this.errorMessage = '';
    
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

      if (response.ok && result.success) {
        if (type === 'email') {
          this.emailVerified = true;
          this.requiresEmailVerification = false;
          this.emailOtp = '';
        } else {
          this.phoneVerified = true;
          this.requiresPhoneVerification = false;
          this.phoneOtp = '';
        }
        
        this.toastService.showSuccess(result.message);
        
        // Redirect to login immediately after ANY verification (email OR phone)
        this.clearForm();
        setTimeout(() => {
          this.close();
          this.switchToLogin();
        }, 1000); // Show success message briefly then redirect
      } else {
        this.errorMessage = result.error || 'Invalid or expired OTP. Please try again.';
      }
    } catch (error) {
      this.errorMessage = 'Network error. Please try again.';
    } finally {
      this.isVerifying = false;
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
      this.toastService.showError('Network error. Please try again.');
    }
  }
}