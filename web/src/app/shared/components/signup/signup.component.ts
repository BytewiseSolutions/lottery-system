import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { SuccessPopupService } from '../../../services/success-popup.service';
import { BackendService } from '../../../util/backend.service';
import { CountrySelectorComponent } from '../country-selector/country-selector.component';

@Component({
  selector: 'app-signup',
  standalone: true,
  imports: [CommonModule, FormsModule, CountrySelectorComponent],
  templateUrl: './signup.component.html',
  styleUrl: './signup.component.css'
})
export class SignupComponent {
  @Input() isVisible = false;
  @Output() signupSuccess = new EventEmitter<any>();
  @Output() closeModal = new EventEmitter<void>();
  @Output() switchToLoginEvent = new EventEmitter<void>();
  @Output() countrySelected = new EventEmitter<any>();

  validationErrors: any = {};
  selectedCountry: any = null;

  constructor(
    private successPopupService: SuccessPopupService,
    private backendService: BackendService
  ) {}

  firstName = '';
  lastName = '';
  email = '';
  phone = '';
  country = '';
  password = '';
  agreeTerms = false;
  isLoading = false;
  showPassword = false;
  errorMessage = '';

  close() {
    this.clearForm();
    this.closeModal.emit();
  }

  switchToLogin() {
    this.clearForm();
    this.switchToLoginEvent.emit();
  }

  clearForm() {
    this.firstName = '';
    this.lastName = '';
    this.email = '';
    this.phone = '';
    this.country = '';
    this.password = '';
    this.agreeTerms = false;
    this.showPassword = false;
    this.errorMessage = '';
    this.validationErrors = {};
    this.selectedCountry = null;
  }



  async onSignup() {
    this.validationErrors = {};
    
    if (!this.agreeTerms) {
      this.validationErrors.terms = 'You must agree to the terms and conditions';
      return;
    }
    
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
    } else if (this.password.length < 8) {
      this.validationErrors.password = 'Password must be at least 8 characters';
    }
    
    if (!this.agreeTerms) {
      this.validationErrors.terms = 'Please agree to the terms and conditions';
    }
    
    if (Object.keys(this.validationErrors).length > 0) {
      return;
    }

    this.isLoading = true;
    
    const userData = {
      first_name: this.firstName,
      last_name: this.lastName,
      email: this.email || null,
      phone: this.phone || null,
      country: this.country,
      password: this.password,
      confirm_password: this.password // Using same password for confirm
    };

    this.backendService.register(userData).subscribe({
      next: (response: any) => {
        this.isLoading = false;
        
        if (response.success) {
          this.successPopupService.show(
            response.message || "Your account has been created. You can now login with your credentials.", 
            "Account Created Successfully!"
          );
          
          setTimeout(() => {
            this.clearForm();
            this.close();
            this.switchToLogin();
          }, 3000);
        } else {
          this.errorMessage = response.message || 'Registration failed';
        }
      },
      error: (error: any) => {
        this.isLoading = false;
        console.error('Registration error:', error);
        
        if (error.error?.data?.errors) {
          const apiErrors = error.error.data.errors;
          this.validationErrors = {
            firstName: apiErrors.first_name?.[0],
            lastName: apiErrors.last_name?.[0],
            email: apiErrors.email?.[0],
            phone: apiErrors.phone?.[0],
            password: apiErrors.password?.[0],
            country: apiErrors.country?.[0]
          };
          Object.keys(this.validationErrors).forEach(key => {
            if (!this.validationErrors[key]) {
              delete this.validationErrors[key];
            }
          });
        } else {
          this.errorMessage = error.error?.message || 'Network error. Please check your connection.';
        }
      }
    });
  }
  
  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }
  
  dismissSuccessPopup() {
    this.clearForm();
    this.close();
    this.switchToLogin();
  }

  onCountrySelected(country: any) {
    this.selectedCountry = country;
    this.country = country.name;
    this.countrySelected.emit(country);
  }
}
