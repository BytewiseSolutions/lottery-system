import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';

export interface ErrorMessage {
  message: string;
  type: 'error' | 'warning' | 'info' | 'success';
  duration?: number;
}

@Injectable({
  providedIn: 'root'
})
export class ErrorHandlerService {
  private errorSubject = new Subject<ErrorMessage>();
  public error$ = this.errorSubject.asObservable();

  showError(message: string, duration: number = 5000) {
    this.errorSubject.next({ message, type: 'error', duration });
  }

  showWarning(message: string, duration: number = 5000) {
    this.errorSubject.next({ message, type: 'warning', duration });
  }

  showInfo(message: string, duration: number = 5000) {
    this.errorSubject.next({ message, type: 'info', duration });
  }

  showSuccess(message: string, duration: number = 5000) {
    this.errorSubject.next({ message, type: 'success', duration });
  }

  handleHttpError(error: any): string {
    if (error.status === 0) {
      return 'Network error. Please check your internet connection.';
    } else if (error.status === 400) {
      return error.error?.error || 'Invalid request. Please check your input.';
    } else if (error.status === 401) {
      return 'Unauthorized. Please login again.';
    } else if (error.status === 403) {
      return 'Access denied. You do not have permission.';
    } else if (error.status === 404) {
      return 'Resource not found.';
    } else if (error.status === 429) {
      return 'Too many requests. Please try again later.';
    } else if (error.status === 500) {
      return 'Server error. Please try again later.';
    } else if (error.status === 503) {
      return 'Service unavailable. Please try again later.';
    } else {
      return error.error?.error || 'An unexpected error occurred.';
    }
  }
}
