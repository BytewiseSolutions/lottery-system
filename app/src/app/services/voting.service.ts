import { Injectable } from '@angular/core';
import { map, Observable } from 'rxjs';
import { BackendService } from '../util/backend.service';

@Injectable({
  providedIn: 'root'
})
export class VotingService {
  constructor(private backendService: BackendService) { }

  submitVote(lottery: string, numbers: number[], bonusNumbers: number[], voteDate: string): Observable<any> {
    return this.backendService.submitVote({
      lottery,
      numbers,
      bonusNumbers,
      voteDate
    });
  }

  getVotingHistory(): Observable<any> {
    return this.backendService.getVoteHistory().pipe(
      map((response: any) => ({
        votes: response?.data?.votes || response?.data || []
      }))
    );
  }

  getLeadingNumbers(lottery: string, voteDate: string): Observable<any> {
    return this.backendService.getLeadingNumbers(lottery, voteDate).pipe(
      map((response: any) => response?.data || { section1: [], section2: [] })
    );
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
