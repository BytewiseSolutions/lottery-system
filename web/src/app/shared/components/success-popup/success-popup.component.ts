import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Subscription } from 'rxjs';
import { SuccessPopupService, SuccessPopup } from '../../../services/success-popup.service';

@Component({
  selector: 'app-success-popup',
  imports: [CommonModule],
  templateUrl: './success-popup.component.html',
  styleUrl: './success-popup.component.css'
})
export class SuccessPopupComponent implements OnInit, OnDestroy {
  showPopup = false;
  message = '';
  title = 'Success!';
  private subscription?: Subscription;

  constructor(private successPopupService: SuccessPopupService) {}

  ngOnInit() {
    this.subscription = this.successPopupService.popup$.subscribe(popup => {
      this.title = popup.title || 'Success!';
      this.message = popup.message;
      this.showPopup = true;
      
      setTimeout(() => {
        const popupEl = document.querySelector('.success-popup');
        if (popupEl) {
          popupEl.classList.add('show');
        }
      }, 50);

      // Auto-close after 3 seconds
      setTimeout(() => {
        this.dismiss();
      }, 3000);
    });
  }

  ngOnDestroy() {
    this.subscription?.unsubscribe();
  }

  dismiss() {
    this.showPopup = false;
  }
}
