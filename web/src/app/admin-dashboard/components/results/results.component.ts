import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';

@Component({
  selector: 'app-results',
  imports: [CommonModule, SidebarComponent],
  templateUrl: './results.component.html',
  styleUrl: './results.component.css'
})
export class ResultsComponent {

}
