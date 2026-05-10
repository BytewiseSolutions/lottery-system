import { Component } from '@angular/core';
import { SidebarComponent } from './sidebar/sidebar.component';
import { OverviewComponent } from './components/overview/overview.component';
import { AnalyticsComponent } from './components/analytics/analytics.component';

@Component({
  selector: 'app-admin-dashboard',
  imports: [SidebarComponent, OverviewComponent, AnalyticsComponent],
  templateUrl: './admin-dashboard.component.html',
  styleUrl: './admin-dashboard.component.css'
})
export class AdminDashboardComponent {

}
