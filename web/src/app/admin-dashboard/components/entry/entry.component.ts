import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';

@Component({
  selector: 'app-entry',
  imports: [CommonModule, SidebarComponent],
  templateUrl: './entry.component.html',
  styleUrl: './entry.component.css'
})
export class EntryComponent {

}
