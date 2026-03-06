import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class VotingService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) { }

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return new HttpHeaders({
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    });
  }

  submitVote(lottery: string, numbers: number[], bonusNumbers: number[], voteDate: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/vote.php`, {
      lottery,
      numbers,
      bonusNumbers,
      voteDate
    }, { headers: this.getHeaders() });
  }

  getVotingHistory(): Observable<any> {
    return this.http.get(`${this.apiUrl}/voting-history.php`, { headers: this.getHeaders() });
  }

  getLeadingNumbers(lottery: string, voteDate: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/leading-numbers.php?lottery=${lottery}&voteDate=${voteDate}`);
  }

  isVotingTime(): boolean {
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const currentTime = hours * 60 + minutes;
    const votingStart = 19 * 60; // 19:00
    const votingEnd = 20 * 60; // 20:00
    return currentTime >= votingStart && currentTime < votingEnd;
  }

  getVotingCountdown(): string {
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const seconds = now.getSeconds();
    
    if (hours < 19) {
      const timeUntilVoting = (19 - hours - 1) * 3600 + (60 - minutes) * 60 + (60 - seconds);
      return this.formatTime(timeUntilVoting);
    } else if (hours === 19) {
      const timeRemaining = (59 - minutes) * 60 + (60 - seconds);
      return this.formatTime(timeRemaining);
    } else {
      return '00:00:00';
    }
  }

  private formatTime(seconds: number): string {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
  }
}
