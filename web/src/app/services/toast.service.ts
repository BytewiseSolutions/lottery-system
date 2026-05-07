import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';

export interface Toast {
  message: string;
  type: 'success' | 'error' | 'info';
  duration?: number;
}

@Injectable({
  providedIn: 'root'
})
export class ToastService {
  private toastSubject = new Subject<Toast>();
  public toast$ = this.toastSubject.asObservable();

  showSuccess(message: string, duration = 8000) {
    this.toastSubject.next({ message, type: 'success', duration });
  }

  showError(message: string, duration = 10000) {
    this.toastSubject.next({ message, type: 'error', duration });
  }

  showInfo(message: string, duration = 8000) {
    this.toastSubject.next({ message, type: 'info', duration });
  }
}
