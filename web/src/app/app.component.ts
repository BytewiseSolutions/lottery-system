import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { GlobalModalComponent } from './shared/components/global-modal/global-modal.component';
import { SuccessPopupComponent } from './shared/components/success-popup/success-popup.component';
import { ErrorDisplayComponent } from './shared/components/error-display/error-display.component';
import { ToastComponent } from './shared/components/toast/toast.component';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, GlobalModalComponent, SuccessPopupComponent, ErrorDisplayComponent, ToastComponent],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
}
