import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { SuccessPopupService } from '../../../services/success-popup.service';
import { environment } from '../../../../environments/environment';
import { COUNTRIES } from '../../data/countries';

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
  countries = COUNTRIES;
  filteredCountries = COUNTRIES.slice(0, 5);
  showCountryDropdown = false;

  constructor(private successPopupService: SuccessPopupService, private router: Router) {}

  fullName = '';
  firstName = '';
  lastName = '';
  email = '';
  phone = '';
  country = '';
  password = '';
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
  showOtpInput = false;
  resetOtp = '';
  newPassword = '';
  confirmNewPassword = '';

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
    this.firstName = '';
    this.lastName = '';
    this.email = '';
    this.phone = '';
    this.country = '';
    this.password = '';
    this.agreeTerms = false;
    this.showPassword = false;
    this.showConfirmPassword = false;
    this.showOtpVerification = false;
    this.emailOtp = '';
    this.phoneOtp = '';
    this.errorMessage = '';
    this.verificationMode = false;
    this.validationErrors = {};
    this.showCountryDropdown = false;
    this.filteredCountries = COUNTRIES.slice(0, 5);
    // Reset verification states
    this.emailVerified = false;
    this.phoneVerified = false;
    this.allVerified = false;
    this.requiresEmailVerification = false;
    this.requiresPhoneVerification = false;
    // Reset password recovery states
    this.showOtpInput = false;
    this.showSuccessMessage = false;
    this.resetOtp = '';
    this.newPassword = '';
    this.confirmNewPassword = '';
    this.identifier = '';
  }

  switchToRegularSignup() {
    this.verificationMode = false;
    this.errorMessage = '';
  }



  async sendPasswordReset() {
    if (!this.identifier) {
      this.errorMessage = 'Please enter your email or phone number';
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';

    try {
      const response = await fetch(`${environment.apiUrl}/send-reset-code`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ identifier: this.identifier })
      });

      const result = await response.json();

      if (result.success) {
        this.showOtpInput = true;
        this.errorMessage = '';
      } else {
        this.errorMessage = result.error || 'Failed to send reset code. Please try again.';
      }
    } catch (error) {
      this.errorMessage = 'Network error. Please try again.';
    } finally {
      this.isLoading = false;
    }
  }

  async resetPassword() {
    if (!this.resetOtp || !this.newPassword || !this.confirmNewPassword) {
      this.errorMessage = 'Please fill in all fields';
      return;
    }

    if (this.newPassword !== this.confirmNewPassword) {
      this.errorMessage = 'Passwords do not match';
      return;
    }

    if (this.newPassword.length < 6) {
      this.errorMessage = 'Password must be at least 6 characters';
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';

    try {
      const response = await fetch(`${environment.apiUrl}/reset-password`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          identifier: this.identifier,
          code: this.resetOtp,
          newPassword: this.newPassword
        })
      });

      const result = await response.json();

      if (result.success) {
        this.showSuccessMessage = true;
        this.showOtpInput = false;
        this.errorMessage = '';
        
        setTimeout(() => {
          this.close();
          this.switchToLogin();
        }, 3000);
      } else {
        this.errorMessage = result.error || 'Failed to reset password. Please try again.';
      }
    } catch (error) {
      this.errorMessage = 'Network error. Please try again.';
    } finally {
      this.isLoading = false;
    }
  }

  async resendResetCode() {
    await this.sendPasswordReset();
  }

  async onSignup() {
    this.validationErrors = {};
    
    // Validate terms agreement first
    if (!this.agreeTerms) {
      this.validationErrors.terms = 'You must agree to the terms and conditions';
      return;
    }
    
    // Validate fields
    if (!this.firstName) {
      this.validationErrors.firstName = 'First name is required';
    }
    
    if (!this.lastName) {
      this.validationErrors.lastName = 'Last name is required';
    }
    
    if (!this.email && !this.phone) {
      this.validationErrors.contact = 'Please provide either email or phone number';
    }
    
    if (!this.country) {
      this.validationErrors.country = 'Country is required';
    }
    
    if (!this.password) {
      this.validationErrors.password = 'Password is required';
    } else if (this.password.length < 6) {
      this.validationErrors.password = 'Password must be at least 6 characters';
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
          fullName: `${this.firstName} ${this.lastName}`.trim(),
          email: this.email || null,
          phone: this.phone || null,
          country: this.country,
          password: this.password
        })
      });

      const result = await response.json();

      if (result.success) {
        this.successPopupService.show("Your account has been created. You can now login with your credentials.", "Account Created Successfully!");
        
        setTimeout(() => {
          this.clearForm();
          this.close();
          this.switchToLogin();
        }, 3000);
      } else {
        alert(result.error);
      }
    } catch (error) {
      console.error('Registration error:', error);
      alert('Network error. Please check your connection.');
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
  
  

  dismissSuccessPopup() {
    
    this.clearForm();
    this.close();
    this.switchToLogin();
  }

  filterCountries(event: any) {
    const searchTerm = event.target.value.toLowerCase();
    if (!searchTerm) {
      this.filteredCountries = COUNTRIES.slice(0, 5);
    } else {
      this.filteredCountries = COUNTRIES.filter(c => 
        c.toLowerCase().startsWith(searchTerm)
      );
    }
    this.showCountryDropdown = true;
  }

  selectCountry(country: string) {
    this.country = country;
    this.showCountryDropdown = false;
  }
}
