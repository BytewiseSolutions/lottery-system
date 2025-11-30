import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, interval, of } from 'rxjs';
import { map, startWith, switchMap, catchError } from 'rxjs/operators';
import { environment } from '../../environments/environment';

export interface Draw {
  id: number;
  name: string;
  jackpot: string;
  nextDraw: string;
}

export interface PoolData {
  'Mon Lotto': number;
  'Wed Lotto': number;
  'Fri Lotto': number;
}

export interface Stats {
  winnersLastMonth: number;
  totalEntries: number;
  totalPayouts: number;
}

@Injectable({
  providedIn: 'root'
})
export class LotteryService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  getDraws(): Observable<Draw[]> {
    return interval(30000).pipe(
      startWith(0),
      switchMap(() => this.http.get<Draw[]>(`${this.apiUrl}/draws`).pipe(
        catchError(error => {
          console.error('API Error:', error);
          return of([
            { id: 1, name: 'Mon Lotto', jackpot: '$0.000', nextDraw: new Date(Date.now() + 2*24*60*60*1000).toISOString() },
            { id: 2, name: 'Wed Lotto', jackpot: '$0.000', nextDraw: new Date(Date.now() + 4*24*60*60*1000).toISOString() },
            { id: 3, name: 'Fri Lotto', jackpot: '$0.000', nextDraw: new Date(Date.now() + 6*24*60*60*1000).toISOString() }
          ]);
        })
      ))
    );
  }

  getPoolMoney(): Observable<PoolData> {
    return interval(30000).pipe(
      startWith(0),
      switchMap(() => this.http.get<PoolData>(`${this.apiUrl}/pool`).pipe(
        catchError(error => {
          console.error('Pool API Error:', error);
          return of({ 'Mon Lotto': 0, 'Wed Lotto': 0, 'Fri Lotto': 0 });
        })
      ))
    );
  }

  getTotalPoolMoney(): Observable<number> {
    return this.getPoolMoney().pipe(
      map(pools => Object.values(pools).reduce((sum, amount) => sum + amount, 0))
    );
  }

  getCountdown(targetDate: string): Observable<string> {
    return interval(1000).pipe(
      startWith(0),
      map(() => {
        const now = new Date().getTime();
        const target = new Date(targetDate).getTime();
        const distance = target - now;
        
        if (distance > 0) {
          const days = Math.floor(distance / (1000 * 60 * 60 * 24));
          const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          const seconds = Math.floor((distance % (1000 * 60)) / 1000);
          
          return `${days.toString().padStart(2, '0')} Days ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        return '00 Days 00:00:00';
      })
    );
  }

  getStats(): Observable<Stats> {
    return this.http.get<Stats>(`${this.apiUrl}/stats`).pipe(
      catchError(error => {
        console.error('Stats API Error:', error);
        return of({
          winnersLastMonth: 0,
          totalEntries: 0,
          totalPayouts: 0
        });
      })
    );
  }
}