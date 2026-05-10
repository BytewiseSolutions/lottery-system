import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { BackendService } from '../../util/backend.service';
import { AlertController, LoadingController } from '@ionic/angular';
import { COUNTRIES } from '../../shared/countries';

@Component({
  selector: 'app-signup',
  templateUrl: './signup.page.html',
  styleUrls: ['./signup.page.scss'],
  standalone: false
})
export class SignupPage implements OnInit {
  first_name = '';
  last_name = '';
  email = '';
  password = '';
  confirm_password = '';
  phone = '';
  country = '';
  showPassword = false;
  showRepeatPassword = false;
  agreeTerms = false;
  countries = COUNTRIES;

  constructor(
    private backendService: BackendService,
    private router: Router,
    private alertCtrl: AlertController,
    private loadingCtrl: LoadingController
  ) {}

  ngOnInit() {
    // Remove auth check since we're using new backend
  }

  togglePassword() {
    this.showPassword = !this.showPassword;
  }

  toggleRepeatPassword() {
    this.showRepeatPassword = !this.showRepeatPassword;
  }

  async openTerms() {
    this.router.navigate(['/terms']);
  }

  async signup() {
    // Validation
    if (!this.validateForm()) {
      return;
    }

    const loading = await this.loadingCtrl.create({ message: 'Registering...' });
    await loading.present();

    const userData = {
      first_name: this.first_name,
      last_name: this.last_name,
      email: this.email,
      password: this.password,
      confirm_password: this.confirm_password,
      phone: this.phone,
      country: this.country
    };

    this.backendService.register(userData).subscribe({
      next: async (response) => {
        loading.dismiss();
        
        const alert = await this.alertCtrl.create({
          header: 'Success',
          message: response.message || 'Registration successful!',
          buttons: [{
            text: 'OK',
            handler: () => {
              this.router.navigate(['/auth/login']);
            }
          }]
        });
        await alert.present();
      },
      error: async (err) => {
        loading.dismiss();
        
        let errorMessage = 'Registration failed';
        
        if (err.error?.message) {
          errorMessage = err.error.message;
        } else if (err.error?.data?.errors) {
          // Handle validation errors
          const errors = err.error.data.errors;
          errorMessage = Object.values(errors).join('\n');
        }
        
        const alert = await this.alertCtrl.create({
          header: 'Registration Error',
          message: errorMessage,
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  private validateForm(): boolean {
    if (!this.first_name || !this.last_name) {
      this.showAlert('Error', 'Please enter your first and last name');
      return false;
    }

    if (!this.email) {
      this.showAlert('Error', 'Please enter your email');
      return false;
    }

    if (!this.password || this.password.length < 8) {
      this.showAlert('Error', 'Password must be at least 8 characters');
      return false;
    }

    if (this.password !== this.confirm_password) {
      this.showAlert('Error', 'Passwords do not match');
      return false;
    }

    if (!this.agreeTerms) {
      this.showAlert('Error', 'Please agree to the terms and conditions');
      return false;
    }

    return true;
  }

  private async showAlert(header: string, message: string) {
    const alert = await this.alertCtrl.create({
      header,
      message,
      buttons: ['OK']
    });
    await alert.present();
  }
}
