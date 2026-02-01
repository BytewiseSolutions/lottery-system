import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { AuthService } from './auth.service';

@Injectable({ providedIn: 'root' })
export class ResultsService {
  constructor(private http: HttpClient, private auth: AuthService) {}

  private getHeaders(): HttpHeaders {
    return new HttpHeaders({ Authorization: `Bearer ${this.auth.getToken()}` });
  }

  getPastDraws(): Observable<any> {
    return this.http.get(`${environment.apiUrl}/past-draws.php`, { headers: this.getHeaders() });
  }

  getResults(drawId: number): Observable<any> {
    return this.http.get(`${environment.apiUrl}/results.php?draw_id=${drawId}`, { headers: this.getHeaders() });
  }

  getWinners(drawId?: number): Observable<any> {
    const url = drawId ? `${environment.apiUrl}/winners.php?draw_id=${drawId}` : `${environment.apiUrl}/winners.php`;
    return this.http.get(url, { headers: this.getHeaders() });
  }
}
