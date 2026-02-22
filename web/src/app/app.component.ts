import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { ToastComponent } from './shared/components/toast/toast.component';
import { SuccessPopupComponent } from './shared/components/success-popup/success-popup.component';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, ToastComponent, SuccessPopupComponent],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
}
