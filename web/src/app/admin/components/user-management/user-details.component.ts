import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { UserRecord } from './user-management.models';

export type UserDetailsData = UserRecord;

@Component({
  selector: 'app-user-details',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './user-details.component.html',
  styleUrls: ['./user-details.component.scss']
})
export class UserDetailsComponent {
  @Input({ required: true }) user!: UserDetailsData;
  @Output() closeDetails = new EventEmitter<void>();
  @Output() editUser = new EventEmitter<UserDetailsData>();

  getStatusClass(): string {
    return this.user.is_active ? 'active' : 'inactive';
  }

  formatDateTime(date: string | undefined): string {
    if (!date) {
      return 'Never';
    }

    return new Date(date).toLocaleString();
  }
}
