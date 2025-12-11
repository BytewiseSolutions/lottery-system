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
           [ngClass]="'toast-' + toast.type"
           (click)="removeToast(toast)">
        {{ toast.message }}
        <span class="toast-close">×</span>
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
    }
    .toast-close {
      position: absolute;
      right: 8px;
      top: 8px;
      font-size: 18px;
      opacity: 0.7;
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