import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';

@Component({
  selector: 'app-payment',
  imports: [CommonModule, SidebarComponent],
  templateUrl: './payment.component.html',
  styleUrl: './payment.component.css'
})
export class PaymentComponent {

}
