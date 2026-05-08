import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, map, tap, throwError } from 'rxjs';
import { BackendService } from '../util/backend.service';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private userSubject = new BehaviorSubject<any>(null);
  user$ = this.userSubject.asObservable();

  constructor(private backendService: BackendService) {
    const user = localStorage.getItem('user');
    if (user) this.userSubject.next(JSON.parse(user));
  }

  login(email: string, password: string): Observable<any> {
    return this.backendService.login({ identifier: email, password }).pipe(
      map((response: any) => response?.data ?? response),
      tap((res: any) => {
        if (res?.token) {
          localStorage.setItem('auth_token', res.token);
          localStorage.setItem('token', res.token);
          localStorage.setItem('user', JSON.stringify(res.user));
          this.userSubject.next(res.user);
        }
      })
    );
  }

  register(data: any): Observable<any> {
    return this.backendService.register({
      first_name: data.first_name || data.name?.split(' ')[0] || '',
      last_name: data.last_name || data.name?.split(' ').slice(1).join(' ') || '',
      email: data.email,
      phone: data.phone,
      password: data.password,
      confirm_password: data.repeatPassword || data.confirm_password || data.password,
      country: data.country || ''
    });
  }

  verifyOtp(email: string, otp: string): Observable<any> {
    return throwError(() => new Error('OTP verification is not supported by the current API'));
  }

  logout() {
    this.backendService.logout().subscribe({ error: () => {} });
    this.backendService.clearAuthSession();
    this.userSubject.next(null);
  }

  getToken(): string | null {
    return localStorage.getItem('auth_token') || localStorage.getItem('token');
  }

  isAuthenticated(): boolean {
    return !!this.getToken();
  }

  getProfile(): Observable<any> {
    return this.backendService.getUserProfile().pipe(
      map((response: any) => {
        const data = response?.data ?? {};
        return {
          full_name: data.full_name || `${data.first_name || ''} ${data.last_name || ''}`.trim(),
          email: data.email || '',
          phone: data.phone || '',
          country: data.country || '',
          notification_enabled: 1
        };
      })
    );
  }

  updateProfile(data: any): Observable<any> {
    return this.backendService.updateUserProfile({
      first_name: data.first_name || data.full_name?.split(' ')[0] || '',
      last_name: data.last_name || data.full_name?.split(' ').slice(1).join(' ') || '',
      email: data.email,
      phone: data.phone,
      country: data.country || ''
    }).pipe(tap((response: any) => {
      const user = this.userSubject.value;
      const profile = response?.data;
      if (user && profile) {
        const updatedUser = {
          ...user,
          first_name: profile.first_name,
          last_name: profile.last_name,
          fullName: profile.full_name,
          full_name: profile.full_name,
          email: profile.email,
          phone: profile.phone,
          country: profile.country
        };
        localStorage.setItem('user', JSON.stringify(updatedUser));
        this.userSubject.next(updatedUser);
      }
    }));
  }

  changePassword(currentPassword: string, newPassword: string): Observable<any> {
    return this.backendService.changeCurrentPassword({
      current_password: currentPassword,
      new_password: newPassword,
      confirm_password: newPassword
    });
  }

  updateNotificationPreferences(enabled: boolean): Observable<any> {
    return new Observable(observer => {
      observer.next({ success: true });
      observer.complete();
    });
  }
}
