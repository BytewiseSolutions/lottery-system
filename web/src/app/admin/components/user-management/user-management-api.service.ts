import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../../environments/environment';
import {
  UserFormPayload,
  UserListResponse,
  UserResponse,
  UserStatsResponse
} from './user-management.models';

export interface UserListQuery {
  page: number;
  limit: number;
  search: string;
  status: string;
  role: string;
  country: string;
  date_from: string;
  date_to: string;
  sort_by: string;
  sort_order: string;
}

@Injectable({
  providedIn: 'root'
})
export class UserManagementApiService {
  private readonly apiUrl = `${environment.apiUrl}/users`;

  constructor(private http: HttpClient) {}

  getUsers(query: UserListQuery): Observable<UserListResponse> {
    const params = new HttpParams({
      fromObject: {
        page: String(query.page),
        limit: String(query.limit),
        search: query.search,
        status: query.status,
        role: query.role,
        country: query.country,
        date_from: query.date_from,
        date_to: query.date_to,
        sort_by: query.sort_by,
        sort_order: query.sort_order
      }
    });

    return this.http.get<UserListResponse>(this.apiUrl, {
      headers: this.getAuthHeaders(),
      params
    });
  }

  getUserStats(): Observable<UserStatsResponse> {
    return this.http.get<UserStatsResponse>(`${environment.apiUrl}/user-stats`, {
      headers: this.getAuthHeaders()
    });
  }

  getUser(userId: number): Observable<UserResponse> {
    return this.http.get<UserResponse>(`${this.apiUrl}/${userId}`, {
      headers: this.getAuthHeaders()
    });
  }

  createUser(user: UserFormPayload): Observable<unknown> {
    return this.http.post(this.apiUrl, user, {
      headers: this.getAuthHeaders()
    });
  }

  updateUser(userId: number, user: UserFormPayload): Observable<unknown> {
    return this.http.put(`${this.apiUrl}/${userId}`, user, {
      headers: this.getAuthHeaders()
    });
  }

  deleteUser(userId: number): Observable<unknown> {
    return this.http.delete(`${this.apiUrl}/${userId}`, {
      headers: this.getAuthHeaders()
    });
  }

  bulkAction(action: string, userIds: number[]): Observable<unknown> {
    return this.http.post(`${this.apiUrl}/bulk-action`, {
      action,
      user_ids: userIds
    }, {
      headers: this.getAuthHeaders()
    });
  }

  toggleUserStatus(userId: number): Observable<unknown> {
    return this.http.patch(`${this.apiUrl}/${userId}/toggle-status`, {}, {
      headers: this.getAuthHeaders()
    });
  }

  sendVerificationEmail(userId: number): Observable<unknown> {
    return this.http.post(`${this.apiUrl}/${userId}/send-verification`, {}, {
      headers: this.getAuthHeaders()
    });
  }

  resetUserPassword(userId: number): Observable<{ message?: string }> {
    return this.http.post<{ message?: string }>(`${this.apiUrl}/${userId}/reset-password`, {}, {
      headers: this.getAuthHeaders()
    });
  }

  exportUsers(): Observable<Blob> {
    return this.http.get(`${this.apiUrl}/export`, {
      headers: this.getAuthHeaders(),
      responseType: 'blob'
    });
  }

  private getAuthHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return token
      ? new HttpHeaders().set('Authorization', `Bearer ${token}`)
      : new HttpHeaders();
  }
}
