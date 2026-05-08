import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { BackendService } from '../util/backend.service';

@Injectable({ providedIn: 'root' })
export class NotificationService {
  private unreadCountSubject = new BehaviorSubject<number>(0);
  unreadCount$ = this.unreadCountSubject.asObservable();

  constructor(private backendService: BackendService) {
    this.loadUnreadCount();
  }

  getNotifications(): Observable<any> {
    return this.backendService.getNotifications()
      .pipe(tap((response: any) => {
        if (response.success && response.data) {
          this.loadUnreadCount();
        }
      }));
  }

  markAsRead(id: number): Observable<any> {
    return this.backendService.markNotificationAsRead(id)
      .pipe(tap(() => this.loadUnreadCount()));
  }

  private loadUnreadCount() {
    this.backendService.getUnreadNotificationCount()
      .subscribe((response: any) => {
        if (response.success && response.data) {
          this.unreadCountSubject.next(response.data.count || 0);
        }
      });
  }
}
