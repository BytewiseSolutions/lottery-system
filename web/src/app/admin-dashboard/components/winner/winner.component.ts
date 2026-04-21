import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';

@Component({
  selector: 'app-winner',
  imports: [CommonModule, SidebarComponent],
  templateUrl: './winner.component.html',
  styleUrl: './winner.component.css'
})
export class WinnerComponent {

}
