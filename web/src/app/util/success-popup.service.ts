import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';

export interface SuccessPopup {
  message: string;
  title?: string;
}

@Injectable({
  providedIn: 'root'
})
export class SuccessPopupService {
  private popupSubject = new Subject<SuccessPopup>();
  public popup$ = this.popupSubject.asObservable();

  show(message: string, title: string = 'Success!') {
    this.popupSubject.next({ message, title });
  }
}
