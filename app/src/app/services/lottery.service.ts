import { Injectable } from '@angular/core';
import { map, Observable, of } from 'rxjs';
import { BackendService } from '../util/backend.service';

@Injectable({ providedIn: 'root' })
export class LotteryService {
  constructor(private backendService: BackendService) {}

  getUpcomingDraws(): Observable<any> {
    return this.backendService.getUpcomingDraws().pipe(
      map((response: any) => ({
        draws: response?.data ?? []
      }))
    );
  }

  playLottery(entryData: any): Observable<any> {
    return this.backendService.playLottery(entryData);
  }

  getMyEntries(): Observable<any> {
    return this.backendService.getEntryHistory().pipe(
      map((response: any) => response?.data ?? [])
    );
  }

  getMyWinnings(): Observable<any> {
    return this.backendService.getMyWinnings().pipe(
      map((response: any) => {
        const winnings = Array.isArray(response?.data) ? response.data : [];
        const totalWinnings = winnings.reduce((sum: number, item: any) => sum + Number(item.prize_amount || 0), 0);

        return {
          total_winnings: totalWinnings,
          winnings
        };
      })
    );
  }

  getResults(): Observable<any> {
    return this.backendService.getResults().pipe(
      map((response: any) => {
        const results = Array.isArray(response?.data) ? response.data : [];

        return results.map((result: any) => ({
          ...result,
          drawDate: result.draw_date || result.drawDate,
          numbers: result.winning_numbers || result.numbers || [],
          bonusNumbers: result.bonus_numbers || result.bonusNumbers || []
        }));
      })
    );
  }

  getNotifications(): Observable<any> {
    return this.backendService.getNotifications();
  }

  markNotificationRead(id: number): Observable<any> {
    return this.backendService.markNotificationAsRead(id);
  }

  getPastDraws(): Observable<any> {
    return this.getResults();
  }

  getEntryLimit(): Observable<any> {
    return of({ limit: 10, used: 0, remaining: 10 });
  }
}
