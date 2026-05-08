import { Injectable } from '@angular/core';
import { map, Observable } from 'rxjs';
import { BackendService } from '../util/backend.service';

@Injectable({ providedIn: 'root' })
export class ResultsService {
  constructor(private backendService: BackendService) {}

  getPastDraws(): Observable<any> {
    return this.backendService.getResults().pipe(
      map((response: any) => response?.data ?? [])
    );
  }

  getResults(drawId: number): Observable<any> {
    return this.backendService.getResults().pipe(
      map((response: any) => {
        const results = Array.isArray(response?.data) ? response.data : [];
        return results.filter((result: any) => Number(result.draw_id) === Number(drawId));
      })
    );
  }

  getWinners(drawId?: number): Observable<any> {
    return this.backendService.getMyWinnings().pipe(
      map((response: any) => {
        const winnings = Array.isArray(response?.data) ? response.data : [];
        return drawId
          ? winnings.filter((winner: any) => Number(winner.draw_id) === Number(drawId))
          : winnings;
      })
    );
  }
}
