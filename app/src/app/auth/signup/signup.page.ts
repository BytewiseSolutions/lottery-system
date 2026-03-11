import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { AlertController, LoadingController } from '@ionic/angular';
import { COUNTRIES } from '../../shared/countries';

@Component({
  selector: 'app-signup',
  templateUrl: './signup.page.html',
  styleUrls: ['./signup.page.scss'],
  standalone: false
})
export class SignupPage implements OnInit {
  name = '';
  email = '';
  password = '';
  repeatPassword = '';
  phone = '';
  country = '';
  otp = '';
  showOtp = false;
  showPassword = false;
  showRepeatPassword = false;
  agreeTerms = false;
  countries = COUNTRIES;

  constructor(
    private auth: AuthService,
    private router: Router,
    private alertCtrl: AlertController,
    private loadingCtrl: LoadingController
  ) {}

  ngOnInit() {
    if (this.auth.isAuthenticated()) {
      this.router.navigate(['/home']);
    }
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
    const loading = await this.loadingCtrl.create({ message: 'Registering...' });
    await loading.present();

    this.auth.register({ name: this.name, email: this.email, password: this.password, repeatPassword: this.repeatPassword, phone: this.phone, country: this.country }).subscribe({
      next: () => {
        loading.dismiss();
        this.showOtp = true;
      },
      error: async (err) => {
        loading.dismiss();
        const alert = await this.alertCtrl.create({
          header: 'Error',
          message: err.error?.message || 'Registration failed',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  async verifyOtp() {
    const loading = await this.loadingCtrl.create({ message: 'Verifying...' });
    await loading.present();

    this.auth.verifyOtp(this.email, this.otp).subscribe({
      next: () => {
        loading.dismiss();
        this.router.navigate(['/home']);
      },
      error: async (err) => {
        loading.dismiss();
        const alert = await this.alertCtrl.create({
          header: 'Error',
          message: err.error?.message || 'Verification failed',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }
}
