import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';

@Component({
  selector: 'app-voting',
  imports: [CommonModule, SidebarComponent],
  templateUrl: './voting.component.html',
  styleUrl: './voting.component.css'
})
export class VotingComponent {

}
