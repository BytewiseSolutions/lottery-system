import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { AlertController, LoadingController } from '@ionic/angular';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
  standalone: false
})
export class LoginPage implements OnInit {
  email = '';
  password = '';
  showPassword = false;
  rememberMe = false;

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

  async showRememberMeInfo() {
    const alert = await this.alertCtrl.create({
      header: 'Remember me',
      message: 'Remember me will enable you to stay logged in forever. In case you don\'t open the app for 90 days, you will need to re enter your username and password.',
      buttons: ['OK']
    });
    await alert.present();
  }

  async login() {
    const loading = await this.loadingCtrl.create({ message: 'Logging in...' });
    await loading.present();

    this.auth.login(this.email, this.password).subscribe({
      next: () => {
        loading.dismiss();
        this.router.navigate(['/home']);
      },
      error: async (err) => {
        loading.dismiss();
        const alert = await this.alertCtrl.create({
          header: 'Error',
          message: err.error?.message || 'Login failed',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }
}
