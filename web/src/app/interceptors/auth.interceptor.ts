import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { catchError, throwError } from 'rxjs';
import { ToastService } from '../services/toast.service';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const toastService = inject(ToastService);
  const token = localStorage.getItem('token');

  // Clone request and add authorization header if token exists
  const authReq = token
    ? req.clone({
        setHeaders: {
          Authorization: `Bearer ${token}`
        }
      })
    : req;

  return next(authReq).pipe(
    catchError((error) => {
      if (error.status === 401) {
        toastService.showError('Session expired. Please login again.');
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/';
      } else if (error.status === 429) {
        toastService.showError('Too many requests. Please try again later.');
      } else if (error.status === 0) {
        toastService.showError('Network error. Please check your connection.');
      }
      return throwError(() => error);
    })
  );
};
