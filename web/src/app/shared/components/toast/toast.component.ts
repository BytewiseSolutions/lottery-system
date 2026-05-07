import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Subscription } from 'rxjs';
import { ToastService, Toast } from '../../../services/toast.service';

@Component({
  selector: 'app-toast',
  imports: [CommonModule],
  template: `
    <div class="toast-container">
      <div *ngFor="let toast of toasts"
           class="toast" 
           [ngClass]="'toast-' + toast.type">
        <span class="toast-message">{{ toast.message }}</span>
        <button type="button" class="toast-close" (click)="removeToast(toast)" aria-label="Close notification">×</button>
      </div>
    </div>
  `,
  styles: [`
    .toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
    }
    .toast {
      padding: 12px 16px;
      margin-bottom: 8px;
      border-radius: 4px;
      color: white;
      font-weight: 500;
      animation: slideIn 0.3s ease-out;
      cursor: pointer;
      position: relative;
      min-width: 300px;
      display: flex;
      align-items: flex-start;
      gap: 12px;
      box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18);
    }
    .toast-message {
      flex: 1;
      line-height: 1.4;
    }
    .toast-close {
      border: 0;
      background: transparent;
      color: inherit;
      font-size: 18px;
      line-height: 1;
      opacity: 0.8;
      cursor: pointer;
      padding: 0;
    }
    .toast-success { background-color: #10b981; }
    .toast-error { background-color: #ef4444; }
    .toast-info { background-color: #3b82f6; }
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
  `]
})
export class ToastComponent implements OnInit, OnDestroy {
  toasts: Toast[] = [];
  private subscription?: Subscription;

  constructor(private toastService: ToastService) {}

  ngOnInit() {
    this.subscription = this.toastService.toast$.subscribe(toast => {
      this.toasts.push(toast);
      setTimeout(() => {
        this.removeToast(toast);
      }, toast.duration || 6000);
    });
  }

  ngOnDestroy() {
    this.subscription?.unsubscribe();
  }

  removeToast(toast: Toast) {
    const index = this.toasts.indexOf(toast);
    if (index > -1) {
      this.toasts.splice(index, 1);
    }
  }
}
