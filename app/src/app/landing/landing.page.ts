import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Component({
  selector: 'app-landing',
  templateUrl: './landing.page.html',
  styleUrls: ['./landing.page.scss'],
  standalone: false
})
export class LandingPage implements OnInit {
  constructor(private router: Router, private auth: AuthService) {}

  ngOnInit() {
    if (this.auth.isAuthenticated()) {
      this.router.navigate(['/home']);
    }
  }

  startMakingMoney() {
    if (this.auth.isAuthenticated()) {
      this.router.navigate(['/home']);
    } else {
      this.router.navigate(['/login']);
    }
  }

  openLogin() {
    this.router.navigate(['/login']);
  }
}
