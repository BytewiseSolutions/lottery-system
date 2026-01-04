import { inject } from '@angular/core';
import { Router, CanActivateFn } from '@angular/router';

export const adminGuard: CanActivateFn = () => {
  const router = inject(Router);
  const token = localStorage.getItem('token');
  const userStr = localStorage.getItem('user');
  
  if (!token || !userStr) {
    router.navigate(['/']);
    return false;
  }
  
  try {
    const user = JSON.parse(userStr);
    if (user.role === 'admin' || user.email === 'admin@totalfreelotto.com') {
      return true;
    }
  } catch (error) {
    console.error('Auth guard error:', error);
  }
  
  router.navigate(['/']);
  return false;
};
