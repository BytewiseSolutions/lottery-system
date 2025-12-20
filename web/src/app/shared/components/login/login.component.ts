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

  identifier = '';
  password = '';
  showPassword = false;

  close() {
    this.closeModal.emit();
  }

  switchToSignup() {
    this.switchToSignupEvent.emit();
  }

  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }

  async onLogin() {
    if (!this.identifier || !this.password) {
      return;
    }

    try {
      const response = await fetch(`${environment.apiUrl}/login.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ identifier: this.identifier, password: this.password })
      });

      const result = await response.json();

      if (result.success) {
        localStorage.setItem('token', result.token);
        localStorage.setItem('user', JSON.stringify(result.user));
        this.loginSuccess.emit(result.user);
        this.close();
      } else {
        // Error handled by toast service in parent
      }
    } catch (error) {
      // Error handled by toast service in parent
    }
  }
}