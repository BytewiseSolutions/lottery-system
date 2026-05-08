import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class BackendService {
  private readonly baseUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  get<T>(endpoint: string, params?: any): Observable<T> {
    let httpParams = new HttpParams();
    
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key].toString());
        }
      });
    }

    return this.http.get<T>(`${this.baseUrl}/${endpoint}`, {
      headers: this.getHeaders(),
      params: httpParams
    });
  }

  post<T>(endpoint: string, data: any): Observable<T> {
    return this.http.post<T>(`${this.baseUrl}/${endpoint}`, data, {
      headers: this.getHeaders()
    });
  }

  put<T>(endpoint: string, data: any): Observable<T> {
    return this.http.put<T>(`${this.baseUrl}/${endpoint}`, data, {
      headers: this.getHeaders()
    });
  }

  delete<T>(endpoint: string): Observable<T> {
    return this.http.delete<T>(`${this.baseUrl}/${endpoint}`, {
      headers: this.getHeaders()
    });
  }

  private getHeaders(): HttpHeaders {
    let headers = new HttpHeaders({
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    });

    const token = localStorage.getItem('auth_token') || localStorage.getItem('token');
    if (token) {
      headers = headers.set('Authorization', `Bearer ${token}`);
    }

    return headers;
  }

  register(userData: any): Observable<any> {
    return this.post('user/register', userData);
  }

  login(credentials: any): Observable<any> {
    return this.post('auth/login', credentials);
  }

  logout(): Observable<any> {
    const token = localStorage.getItem('auth_token') || localStorage.getItem('token');
    return this.post('auth/logout', { token });
  }

  clearAuthSession(): void {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }

  getUserProfile(): Observable<any> {
    return this.get('user/profile');
  }

  updateUserProfile(userData: any): Observable<any> {
    return this.put('user/profile', userData);
  }

  submitVote(voteData: any): Observable<any> {
    return this.post('vote/submit', voteData);
  }

  getVoteHistory(): Observable<any> {
    return this.get('vote/history');
  }

  getLeadingNumbers(lottery: string, voteDate: string): Observable<any> {
    return this.get('vote/leading', { lottery, voteDate });
  }

  getCurrentDraw(): Observable<any> {
    return this.get('draw/current');
  }

  getUpcomingDraws(): Observable<any> {
    return this.get('draw/upcoming');
  }

  playLottery(entryData: any): Observable<any> {
    return this.post('entry/submit', entryData);
  }

  getEntryHistory(): Observable<any> {
    return this.get('entry/history');
  }

  getLatestResults(): Observable<any> {
    return this.get('result/latest');
  }

  getResults(): Observable<any> {
    return this.get('result/list');
  }

  getWinners(): Observable<any> {
    return this.get('winner/list');
  }

  getMyWinnings(): Observable<any> {
    return this.get('winner/my');
  }

  getNotifications(): Observable<any> {
    return this.get('notification/list');
  }

  markNotificationAsRead(id: number): Observable<any> {
    return this.post('notification/mark-read', { id });
  }

  getUnreadNotificationCount(): Observable<any> {
    return this.get('notification/unread-count');
  }

  uploadFile(fileData: FormData): Observable<any> {
    let headers = new HttpHeaders();
    const token = localStorage.getItem('auth_token') || localStorage.getItem('token');
    if (token) {
      headers = headers.set('Authorization', `Bearer ${token}`);
    }

    return this.http.post(`${this.baseUrl}/file/upload`, fileData, {
      headers
    });
  }

  getAnalytics(): Observable<any> {
    return this.get('analytics/stats');
  }


  getSettings(): Observable<any> {
    return this.get('settings/get');
  }

  updateSettings(settingsData: any): Observable<any> {
    return this.put('settings/update', settingsData);
  }

  changeCurrentPassword(passwordData: any): Observable<any> {
    return this.post('user/change-password', passwordData);
  }
}
