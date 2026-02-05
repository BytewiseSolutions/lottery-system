import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { environment } from '../../environments/environment';
import { AuthService } from './auth.service';

@Injectable({ providedIn: 'root' })
export class NotificationService {
  private unreadCountSubject = new BehaviorSubject<number>(0);
  unreadCount$ = this.unreadCountSubject.asObservable();

  constructor(private http: HttpClient, private auth: AuthService) {
    this.loadUnreadCount();
  }

  private getHeaders(): HttpHeaders {
    return new HttpHeaders({ Authorization: `Bearer ${this.auth.getToken()}` });
  }

  getNotifications(): Observable<any> {
    return this.http.get(`${environment.apiUrl}/api/user-notifications`, { headers: this.getHeaders() })
      .pipe(tap((data: any) => this.unreadCountSubject.next(data.unread_count || 0)));
  }

  markAsRead(id: number): Observable<any> {
    return this.http.post(`${environment.apiUrl}/api/mark-notification-read`, { id }, { headers: this.getHeaders() })
      .pipe(tap(() => this.loadUnreadCount()));
  }

  private loadUnreadCount() {
    this.http.get(`${environment.apiUrl}/api/user-notifications`, { headers: this.getHeaders() })
      .subscribe((data: any) => this.unreadCountSubject.next(data.unread_count || 0));
  }
}
