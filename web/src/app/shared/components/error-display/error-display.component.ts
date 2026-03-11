import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ErrorHandlerService, ErrorMessage } from '../../../services/error-handler.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-error-display',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="error-container">
      @for (error of errors; track $index) {
        <div [class]="'error-message error-' + error.type">
          <div class="error-content">
            <i [class]="getIcon(error.type)"></i>
            <span>{{ error.message }}</span>
            <button class="close-btn" (click)="removeError($index)">&times;</button>
          </div>
        </div>
      }
    </div>
  `,
  styles: [`
    .error-container {
      position: fixed;
      top: 80px;
      right: 20px;
      z-index: 10000;
      max-width: 400px;
    }

    .error-message {
      padding: 15px 20px;
      margin-bottom: 10px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
      from {
        transform: translateX(400px);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }

    .error-content {
      display: flex;
      align-items: center;
      gap: 10px;
      color: white;
    }

    .error-content i {
      font-size: 20px;
    }

    .error-content span {
      flex: 1;
      font-size: 14px;
    }

    .close-btn {
      background: none;
      border: none;
      color: white;
      font-size: 24px;
      cursor: pointer;
      padding: 0;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0.8;
      transition: opacity 0.2s;
    }

    .close-btn:hover {
      opacity: 1;
    }

    .error-error {
      background: linear-gradient(135deg, #f5576c, #f093fb);
    }

    .error-warning {
      background: linear-gradient(135deg, #ffa726, #fb8c00);
    }

    .error-info {
      background: linear-gradient(135deg, #4facfe, #00f2fe);
    }

    .error-success {
      background: linear-gradient(135deg, #43e97b, #38f9d7);
    }

    @media (max-width: 768px) {
      .error-container {
        right: 10px;
        left: 10px;
        max-width: none;
      }
    }
  `]
})
export class ErrorDisplayComponent implements OnInit, OnDestroy {
  errors: ErrorMessage[] = [];
  private subscription?: Subscription;

  constructor(private errorHandler: ErrorHandlerService) {}

  ngOnInit() {
    this.subscription = this.errorHandler.error$.subscribe(error => {
      this.errors.push(error);
      
      if (error.duration) {
        setTimeout(() => {
          this.removeError(0);
        }, error.duration);
      }
    });
  }

  ngOnDestroy() {
    this.subscription?.unsubscribe();
  }

  removeError(index: number) {
    this.errors.splice(index, 1);
  }

  getIcon(type: string): string {
    switch(type) {
      case 'error': return 'fas fa-exclamation-circle';
      case 'warning': return 'fas fa-exclamation-triangle';
      case 'info': return 'fas fa-info-circle';
      case 'success': return 'fas fa-check-circle';
      default: return 'fas fa-info-circle';
    }
  }
}
