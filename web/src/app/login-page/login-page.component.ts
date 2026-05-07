import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { LayoutComponent } from '../layout/layout.component';
import { LoginComponent } from '../shared/components/login/login.component';
import { SignupComponent } from '../shared/components/signup/signup.component';

@Component({
  selector: 'app-login-page',
  standalone: true,
  imports: [CommonModule, LayoutComponent, LoginComponent, SignupComponent],
  templateUrl: './login-page.component.html',
  styleUrls: ['./login-page.component.css']
})
export class LoginPageComponent implements OnInit {
  showLoginModal = true;
  showSignupModal = false;
  private returnUrl = '/';

  constructor(
    private route: ActivatedRoute,
    private router: Router
  ) {}

  ngOnInit() {
    if (localStorage.getItem('auth_token') || localStorage.getItem('token')) {
      this.redirectAfterLogin();
      return;
    }

    this.returnUrl = this.route.snapshot.queryParamMap.get('returnUrl') || '/';
  }

  onLoginSuccess() {
    this.redirectAfterLogin();
  }

  onSignupSuccess() {
    this.redirectAfterLogin();
  }

  onCloseLogin() {
    this.router.navigateByUrl('/');
  }

  onCloseSignup() {
    this.router.navigateByUrl('/');
  }

  onSwitchToSignup() {
    this.showLoginModal = false;
    this.showSignupModal = true;
  }

  onSwitchToLogin() {
    this.showSignupModal = false;
    this.showLoginModal = true;
  }

  private redirectAfterLogin() {
    this.router.navigateByUrl(this.returnUrl);
  }
}
