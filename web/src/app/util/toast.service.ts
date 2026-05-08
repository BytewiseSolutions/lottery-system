import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

export interface Toast {
  id: number;
  message: string;
  type: 'success' | 'error' | 'info';
  duration?: number;
}

@Injectable({
  providedIn: 'root'
})
export class ToastService {
  private readonly toastsSubject = new BehaviorSubject<Toast[]>([]);
  public readonly toasts$ = this.toastsSubject.asObservable();
  private nextId = 1;

  showSuccess(message: string, duration = 8000) {
    this.addToast(message, 'success', duration);
  }

  showError(message: string, duration = 10000) {
    this.addToast(message, 'error', duration);
  }

  showInfo(message: string, duration = 8000) {
    this.addToast(message, 'info', duration);
  }

  removeToast(toastId: number) {
    this.toastsSubject.next(
      this.toastsSubject.value.filter((toast) => toast.id !== toastId)
    );
  }

  private addToast(message: string, type: Toast['type'], duration: number) {
    const toast: Toast = {
      id: this.nextId++,
      message,
      type,
      duration
    };

    this.toastsSubject.next([...this.toastsSubject.value, toast]);

    setTimeout(() => {
      this.removeToast(toast.id);
    }, duration);
  }
}
