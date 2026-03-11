import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-loading-spinner',
  standalone: true,
  imports: [CommonModule],
  template: `
    @if (show) {
      <div class="spinner-overlay">
        <div class="spinner-container">
          <div class="spinner"></div>
          @if (message) {
            <p class="spinner-message">{{ message }}</p>
          }
        </div>
      </div>
    }
  `,
  styles: [`
    .spinner-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .spinner-container {
      text-align: center;
    }

    .spinner {
      border: 4px solid #f3f3f3;
      border-top: 4px solid rgb(108, 0, 146);
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
      margin: 0 auto;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .spinner-message {
      color: white;
      margin-top: 15px;
      font-size: 16px;
      font-weight: 500;
    }
  `]
})
export class LoadingSpinnerComponent {
  @Input() show = false;
  @Input() message = '';
}
