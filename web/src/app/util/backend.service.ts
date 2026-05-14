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

  getCurrentUserStats(): Observable<any> {
    return this.get('user/stats');
  }

  deleteCurrentAccount(): Observable<any> {
    return this.post('user/delete', {});
  }

  submitVote(voteData: any): Observable<any> {
    return this.post('vote/submit', voteData);
  }

  playLottery(voteData: any): Observable<any> {
    return this.post('entry/submit', voteData);
  }

  getQuickPickNumbers(type: 'main' | 'bonus', excludeNumbers: number[] = []): Observable<any> {
    return this.post('vote/quick-pick', {
      type,
      excludeNumbers
    });
  }

  getLeadingNumbers(lottery: string, voteDate: string): Observable<any> {
    return this.get('vote/leading', {
      lottery,
      voteDate
    });
  }

  getHighestVoteForDraw(drawId: number | string): Observable<any> {
    return this.get('vote/highest-vote', {
      draw_id: drawId
    });
  }

  getVoteHistory(): Observable<any> {
    return this.get('vote/history');
  }

  getAdminVotes(params?: any): Observable<any> {
    return this.get('vote/list', params);
  }

  getAdminVoteById(voteId: number | string): Observable<any> {
    return this.get('vote/details', { id: voteId });
  }

  createAdminVote(voteData: any): Observable<any> {
    return this.post('vote/create', voteData);
  }

  updateAdminVote(voteData: any): Observable<any> {
    return this.put('vote/update', voteData);
  }

  deleteAdminVote(voteId: number | string): Observable<any> {
    return this.delete(`vote/delete?id=${voteId}`);
  }

  getCurrentDraw(): Observable<any> {
    return this.get('draw/current');
  }

  getUpcomingDraws(): Observable<any> {
    return this.get('draw/upcoming');
  }

  getPastDraws(): Observable<any> {
    return this.get('draw/past');
  }

  getLatestResults(): Observable<any> {
    return this.get('result/latest');
  }

  getResults(): Observable<any> {
    return this.get('result/list');
  }

  getResultById(resultId: number | string): Observable<any> {
    return this.get('result/details', { id: resultId });
  }

  autoPublishResults(): Observable<any> {
    return this.post('result/auto-publish', {});
  }

  createResult(resultData: any): Observable<any> {
    return this.post('result/create', resultData);
  }

  updateResult(resultData: any): Observable<any> {
    return this.put('result/update', resultData);
  }

  getEntryHistory(): Observable<any> {
    return this.get('entry/history');
  }

  getEntriesByDraw(drawId: number | string): Observable<any> {
    return this.get('entry/draw', { draw_id: drawId });
  }

  getAllEntries(params?: any): Observable<any> {
    return this.get('entry/list', params);
  }

  getEntryById(entryId: number | string): Observable<any> {
    return this.get('entry/details', { id: entryId });
  }

  getWinners(resultId?: number | string): Observable<any> {
    return this.get('winner/list', resultId ? { result_id: resultId } : undefined);
  }

  getMyWinnings(): Observable<any> {
    return this.get('winner/my');
  }

  getWinnersPage(params?: any): Observable<any> {
    return this.get('winner/list', params);
  }

  markWinnerClaimed(winnerId: number | string): Observable<any> {
    return this.post('winner/claim', { winner_id: winnerId });
  }

  processWinnerPayment(paymentData: any): Observable<any> {
    return this.post('payment/process', paymentData);
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

  getFileUrl(fileId: number | string): string {
    return `${this.baseUrl}/file/get?id=${fileId}`;
  }

  getUsers(params?: any): Observable<any> {
    return this.get('user/list', params);
  }

  getUserById(userId: number | string): Observable<any> {
    return this.get('user/details', { id: userId });
  }

  createUser(userData: any): Observable<any> {
    return this.post('user/create', userData);
  }

  updateUser(userData: any): Observable<any> {
    return this.put('user/update', userData);
  }

  updateUserStatus(userId: number | string, isActive: boolean): Observable<any> {
    return this.post('user/status', { id: userId, is_active: isActive });
  }

  resetUserPassword(userId: number | string, password: string, confirmPassword: string): Observable<any> {
    return this.post('user/password', {
      id: userId,
      password,
      confirm_password: confirmPassword
    });
  }

  changeCurrentPassword(passwordData: any): Observable<any> {
    return this.post('user/change-password', passwordData);
  }

  getAnalytics(params?: any): Observable<any> {
    return this.get('analytics/stats', params);
  }

  getAnalyticsData(period?: string): Observable<any> {
    return this.get('analytics/detailed', { period });
  }

  getEntryTrends(period?: string): Observable<any> {
    return this.get('analytics/entry-trends', { period });
  }

  getRevenueDistribution(period?: string): Observable<any> {
    return this.get('analytics/revenue-distribution', { period });
  }

  getPerformanceMetrics(period?: string): Observable<any> {
    return this.get('analytics/performance-metrics', { period });
  }

  getSettings(): Observable<any> {
    return this.get('settings/get');
  }

  updateSettings(settingsData: any): Observable<any> {
    return this.put('settings/update', settingsData);
  }

  getActivityLogs(limit?: number): Observable<any> {
    return this.get('audit/logs', { limit });
  }

  forgotPassword(data: any): Observable<any> {
    return this.post('auth/forgot-password', data);
  }

  resetPassword(data: any): Observable<any> {
    return this.post('auth/reset-password', data);
  }

  getCurrentUser(): Observable<any> {
    return this.get('user/profile');
  }

  getUnreadNotificationCount(): Observable<any> {
    return this.get('notification/unread-count');
  }

  markNotificationAsRead(notificationId: number): Observable<any> {
    return this.post('notification/mark-read', { id: notificationId });
  }

  createNotification(notificationData: any): Observable<any> {
    return this.post('notification/create', notificationData);
  }

  submitContactMessage(contactData: any): Observable<any> {
    return this.post('contact', contactData);
  }

}
