import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class BackendService {
  private readonly baseUrl = environment.apiUrl;

  constructor(private http: HttpClient) { }

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

    const token = localStorage.getItem('auth_token');
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
    return this.post('auth/logout', {});
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

  getCurrentDraw(): Observable<any> {
    return this.get('draw/current');
  }

  getUpcomingDraws(): Observable<any> {
    return this.get('draw/upcoming');
  }

  getLatestResults(): Observable<any> {
    return this.get('result/latest');
  }

  getWinners(): Observable<any> {
    return this.get('winner/list');
  }

  getNotifications(): Observable<any> {
    return this.get('notification/list');
  }

uploadFile(fileData: FormData): Observable<any> {
  const headers = this.getHeaders().delete('Content-Type');

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

  forgotPassword(data: any): Observable<any> {
    return this.post('auth/forgot-password', data);
  }

  resetPassword(data: any): Observable<any> {
    return this.post('auth/reset-password', data);
  }

}