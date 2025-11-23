import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-login',
  imports: [CommonModule, FormsModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {
  @Input() isVisible = false;
  @Output() loginSuccess = new EventEmitter<any>();
  @Output() closeModal = new EventEmitter<void>();
  @Output() switchToSignupEvent = new EventEmitter<void>();

  email = '';
  password = '';

  close() {
    this.closeModal.emit();
  }

  switchToSignup() {
    this.switchToSignupEvent.emit();
  }

  async onLogin() {
    if (!this.email || !this.password) {
      alert('Please enter both email and password');
      return;
    }

    try {
      const response = await fetch(`${environment.apiUrl}/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: this.email, password: this.password })
      });

      const result = await response.json();

      if (result.success) {
        localStorage.setItem('token', result.token);
        localStorage.setItem('user', JSON.stringify(result.user));
        this.loginSuccess.emit(result.user);
        this.close();
        alert('Login successful!');
      } else {
        alert(result.error);
      }
    } catch (error) {
      alert('Login failed');
    }
  }
}