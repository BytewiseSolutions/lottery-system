import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-admin-sidebar',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './admin-sidebar.component.html',
  styleUrls: ['./admin-sidebar.component.scss']
})
export class AdminSidebarComponent {
  @Input() activeSection = 'dashboard';
  @Input() totalResults = 0;
  @Input() isConnected = true;
  @Input() storageUsed = '1.2GB';
  @Input() storageTotal = '10GB';
  @Input() storagePercentage = 12;
  @Input() memoryUsage = 45;
  @Input() lastLogin = 'Today, 09:30';

  @Output() sectionChange = new EventEmitter<string>();
  @Output() logoutClick = new EventEmitter<void>();

  onSectionClick(section: string) {
    this.sectionChange.emit(section);
  }

  onLogout() {
    this.logoutClick.emit();
  }
}