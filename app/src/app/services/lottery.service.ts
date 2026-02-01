import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { AuthService } from './auth.service';

@Injectable({ providedIn: 'root' })
export class LotteryService {
  constructor(private http: HttpClient, private auth: AuthService) {}

  private getHeaders(): HttpHeaders {
    return new HttpHeaders({ Authorization: `Bearer ${this.auth.getToken()}` });
  }

  getUpcomingDraws(): Observable<any> {
    return this.http.get(`${environment.apiUrl}/api/upcoming-draws`, { headers: this.getHeaders() });
  }

  playLottery(drawId: number, numbers: number[]): Observable<any> {
    return this.http.post(`${environment.apiUrl}/api/play`, { draw_id: drawId, numbers }, { headers: this.getHeaders() });
  }

  getMyEntries(): Observable<any> {
    return this.http.get(`${environment.apiUrl}/api/entries`, { headers: this.getHeaders() });
  }

  getMyWinnings(): Observable<any> {
    return this.http.get(`${environment.apiUrl}/api/my-winnings`, { headers: this.getHeaders() });
  }
}
