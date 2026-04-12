import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { environment } from '../../environments/environment';

export interface Stats {
  winnersLastMonth: number;
  totalEntries: number;
  totalPayouts: number;
}

export interface Draw {
  id: number;
  lottery: string;
  drawDate: string;
  jackpot: string;
  status: string;
  name?: string;
  nextDraw?: string;
}

@Injectable({
  providedIn: 'root'
})
export class LotteryService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  private getAuthOptions() {
    const token = localStorage.getItem('token');
    return token ? { headers: { Authorization: `Bearer ${token}` } } : {};
  }

  getResults(): Observable<any[]> {
    const token = localStorage.getItem('token');
    if (token) {
      return this.http.get<any[]>(`${this.apiUrl}/results`, {
        headers: { 'Authorization': `Bearer ${token}` }
      });
    }
    return this.http.get<any[]>(`${this.apiUrl}/results`);
  }

  getUpcomingDraws(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/upcoming-draws`);
  }

  getCurrentVotingDraw(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/current-voting-draw`);
  }

  // Get voting countdown from backend
  getVotingCountdown(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/voting-countdown`);
  }

  // Get quick pick numbers from backend
  getQuickPickNumbers(type: 'main' | 'bonus', excludeNumbers: number[] = []): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/quick-pick`, {
      type: type,
      excludeNumbers: excludeNumbers
    });
  }

  getDashboardStats(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/dashboard-stats`);
  }

  getUsers(page: number = 1, limit: number = 10, search: string = ''): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/users?page=${page}&limit=${limit}&search=${search}`,
      this.getAuthOptions()
    );
  }

  createUser(user: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/users`, user, this.getAuthOptions());
  }

  updateUser(user: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/users`, user, this.getAuthOptions());
  }

  deleteUser(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/users`, {
      ...this.getAuthOptions(),
      body: { id }
    });
  }

  getAnalytics(range: string, dateFrom?: string, dateTo?: string): Observable<any> {
    let url = `${this.apiUrl}/analytics?range=${range}`;
    if (range === 'custom' && dateFrom && dateTo) {
      url += `&dateFrom=${dateFrom}&dateTo=${dateTo}`;
    }
    return this.http.get<any>(url);
  }

  getAnalyticsData(range: string = '7d'): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/analytics-data?range=${range}`);
  }

  getDraws(): Observable<Draw[]> {
    return this.http.get<Draw[]>(`${this.apiUrl}/draws`);
  }

  getStats(): Observable<Stats> {
    return this.http.get<Stats>(`${this.apiUrl}/stats`);
  }

  createResult(result: any): Observable<any> {
    const token = localStorage.getItem('token');
    if (token) {
      return this.http.post<any>(`${this.apiUrl}/admin-upload-result`, result, {
        headers: { 'Authorization': `Bearer ${token}` }
      });
    }
    return this.http.post<any>(`${this.apiUrl}/admin-upload-result`, result);
  }

  updateResultStatus(id: number, status: string): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.post<any>(`${this.apiUrl}/update-result-status`, { id, status }, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
  }

  updateResult(id: number, result: any): Observable<any> {
    const token = localStorage.getItem('token');
    if (token) {
      return this.http.put<any>(`${this.apiUrl}/results?id=${id}`, result, {
        headers: { 'Authorization': `Bearer ${token}` }
      });
    }
    return this.http.put<any>(`${this.apiUrl}/results?id=${id}`, result);
  }

  deleteResult(id: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/admin-delete-result`, { id });
  }

  updateDrawTime(id: number, draw: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/update-draw-time`, { id, ...draw });
  }

  deleteDraw(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/delete-draw?id=${id}`);
  }

  announceWinners(drawId: number, winningNumbers: number[], bonusNumbers: number[]): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/announce-winners`, {
      drawId,
      winningNumbers,
      bonusNumbers
    });
  }

  getEntries(): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.get<any>(`${this.apiUrl}/entries`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
  }

  getWinners(): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.get<any>(`${this.apiUrl}/winners`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
  }

  sendNotification(recipientType: string, message: string): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.post<any>(`${this.apiUrl}/send-notification`, 
      { recipient_type: recipientType, message },
      { headers: { 'Authorization': `Bearer ${token}` } }
    );
  }

  markAsPaid(winnerId: number): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.post<any>(`${this.apiUrl}/mark-paid`,
      { winner_id: winnerId },
      { headers: { 'Authorization': `Bearer ${token}` } }
    );
  }

  getNotifications(): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.get<any>(`${this.apiUrl}/notifications`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
  }

  getDrawInfo(lottery: string): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/get-draw-info?lottery=${encodeURIComponent(lottery)}`);
  }

  getActivityLogs(): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.get<any>(`${this.apiUrl}/activity-logs`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
  }

  getContactMessages(): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.get<any>(`${this.apiUrl}/contact-messages`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
  }

  deleteContactMessage(id: number): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.delete<any>(`${this.apiUrl}/contact-messages?id=${id}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
  }

  // Voting methods
  submitVote(voteData: any): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.post<any>(`${this.apiUrl}/vote`, voteData, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
  }

  getLeadingNumbers(lottery: string, voteDate: string): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/leading-numbers?lottery=${encodeURIComponent(lottery)}&voteDate=${voteDate}`);
  }

  getVotingHistory(): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.get<any>(`${this.apiUrl}/voting-history`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
  }

  allocateAdminVotes(voteData: any): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.post<any>(`${this.apiUrl}/admin-vote`, voteData, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
  }

  getAdminVotes(): Observable<any> {
    const token = localStorage.getItem('token');
    return this.http.get<any>(`${this.apiUrl}/admin-vote`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
  }
}
