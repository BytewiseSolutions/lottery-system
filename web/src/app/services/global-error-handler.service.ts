import { ErrorHandler, Injectable } from '@angular/core';
import { ToastService } from './toast.service';

@Injectable()
export class GlobalErrorHandler implements ErrorHandler {
  constructor(private toastService: ToastService) {}

  handleError(error: Error): void {
    console.error('Global error:', error);
    
    // Handle different error types
    if (error.message.includes('Network') || error.message.includes('fetch')) {
      this.toastService.showError('Network error. Please check your connection.');
    } else if (error.message.includes('401') || error.message.includes('Unauthorized')) {
      this.toastService.showError('Session expired. Please login again.');
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = '/';
    } else {
      this.toastService.showError('An unexpected error occurred.');
    }
  }
}
