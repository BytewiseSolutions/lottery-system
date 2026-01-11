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

  getResults(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/results`);
  }

  getUpcomingDraws(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/upcoming-draws`);
  }

  getDashboardStats(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/dashboard-stats`);
  }

  // User Management
  getUsers(page: number = 1, limit: number = 10, search: string = ''): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/users?page=${page}&limit=${limit}&search=${search}`);
  }

  createUser(user: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/users`, user);
  }

  updateUser(user: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/users`, user);
  }

  deleteUser(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/users`, { body: { id } });
  }

  // Analytics
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
    return this.http.post<any>(`${this.apiUrl}/admin-upload-result`, result);
  }

  updateResultStatus(id: number, status: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/update-result-status`, { id, status });
  }

  updateResult(id: number, result: any): Observable<any> {
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
}