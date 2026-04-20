import { Component } from '@angular/core';
import { SidebarComponent } from './sidebar/sidebar.component';
import { OverviewComponent } from './components/overview/overview.component';

@Component({
  selector: 'app-admin-dashboard',
  imports: [SidebarComponent, OverviewComponent],
  templateUrl: './admin-dashboard.component.html',
  styleUrl: './admin-dashboard.component.css'
})
export class AdminDashboardComponent {

}
