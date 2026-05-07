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
    return this.http.get(`${environment.apiUrl}/notification/list`, { headers: this.getHeaders() })
      .pipe(tap((response: any) => {
        if (response.success && response.data) {
          // Load unread count separately
          this.loadUnreadCount();
        }
      }));
  }

  markAsRead(id: number): Observable<any> {
    // Note: Mark as read endpoint needs to be implemented in api-refactor
    return this.http.post(`${environment.apiUrl}/notification/mark-read`, { id }, { headers: this.getHeaders() })
      .pipe(tap(() => this.loadUnreadCount()));
  }

  private loadUnreadCount() {
    this.http.get(`${environment.apiUrl}/notification/unread-count`, { headers: this.getHeaders() })
      .subscribe((response: any) => {
        if (response.success && response.data) {
          this.unreadCountSubject.next(response.data.count || 0);
        }
      });
  }
}
