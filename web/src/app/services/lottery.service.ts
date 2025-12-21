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
    return this.http.get<any[]>(`${this.apiUrl}/results.php`);
  }

  getUpcomingDraws(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/upcoming-draws.php`);
  }

  getDashboardStats(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/stats.php`);
  }

  getDraws(): Observable<Draw[]> {
    return this.http.get<Draw[]>(`${this.apiUrl}/draws.php`);
  }

  getStats(): Observable<Stats> {
    return this.http.get<Stats>(`${this.apiUrl}/stats.php`);
  }

  createResult(result: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/admin-upload-result.php`, result);
  }

  updateResult(id: number, result: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/results.php?id=${id}`, result);
  }

  deleteResult(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/admin-delete-result.php?id=${id}`);
  }

  updateDrawTime(id: number, draw: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/update-draw-time.php`, { id, ...draw });
  }

  deleteDraw(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/delete-draw.php?id=${id}`);
  }
}