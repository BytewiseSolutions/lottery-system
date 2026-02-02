import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private userSubject = new BehaviorSubject<any>(null);
  user$ = this.userSubject.asObservable();

  constructor(private http: HttpClient) {
    const user = localStorage.getItem('user');
    if (user) this.userSubject.next(JSON.parse(user));
  }

  login(email: string, password: string): Observable<any> {
    return this.http.post(`${environment.apiUrl}/api/login`, { identifier: email, password }).pipe(
      tap((res: any) => {
        if (res.token) {
          localStorage.setItem('token', res.token);
          localStorage.setItem('user', JSON.stringify(res.user));
          this.userSubject.next(res.user);
        }
      })
    );
  }

  register(data: any): Observable<any> {
    return this.http.post(`${environment.apiUrl}/api/register`, {
      fullName: data.name,
      email: data.email,
      phone: data.phone,
      password: data.password,
      confirmPassword: data.repeatPassword || data.password
    });
  }

  verifyOtp(email: string, otp: string): Observable<any> {
    return this.http.post(`${environment.apiUrl}/api/verify-otp`, { email, otp }).pipe(
      tap((res: any) => {
        if (res.token) {
          localStorage.setItem('token', res.token);
          localStorage.setItem('user', JSON.stringify(res.user));
          this.userSubject.next(res.user);
        }
      })
    );
  }

  logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.userSubject.next(null);
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }

  isAuthenticated(): boolean {
    return !!this.getToken();
  }

  getProfile(): Observable<any> {
    const user = this.userSubject.value;
    return new Observable(observer => {
      observer.next({
        full_name: user?.fullName || user?.name || '',
        email: user?.email || '',
        phone: user?.phone || '',
        notification_enabled: 1
      });
      observer.complete();
    });
  }

  updateProfile(data: any): Observable<any> {
    return this.http.put(`${environment.apiUrl}/api/profile`, data, {
      headers: { Authorization: `Bearer ${this.getToken()}` }
    }).pipe(tap(() => {
      const user = this.userSubject.value;
      if (user) {
        user.fullName = data.full_name;
        user.phone = data.phone;
        localStorage.setItem('user', JSON.stringify(user));
        this.userSubject.next(user);
      }
    }));
  }

  changePassword(currentPassword: string, newPassword: string): Observable<any> {
    return this.http.post(`${environment.apiUrl}/api/change-password`, 
      { currentPassword, newPassword },
      { headers: { Authorization: `Bearer ${this.getToken()}` } }
    );
  }

  updateNotificationPreferences(enabled: boolean): Observable<any> {
    return new Observable(observer => {
      observer.next({ success: true });
      observer.complete();
    });
  }
}
