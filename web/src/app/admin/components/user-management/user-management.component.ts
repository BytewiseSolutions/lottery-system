import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-user-management',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './user-management.component.html',
  styleUrls: ['./user-management.component.scss']
})
export class UserManagementComponent {
  @Input() users: any[] = [];
  @Input() totalUsersCount = 0;
  @Input() usersCurrentPage = 1;
  @Input() usersTotalPages = 1;
  @Input() usersSearchQuery = '';

  @Output() searchUsers = new EventEmitter<string>();
  @Output() addUser = new EventEmitter<void>();
  @Output() editUser = new EventEmitter<any>();
  @Output() deleteUser = new EventEmitter<number>();
  @Output() pageChange = new EventEmitter<number>();

  onSearchUsers() {
    this.searchUsers.emit(this.usersSearchQuery);
  }

  onAddUser() {
    this.addUser.emit();
  }

  onEditUser(user: any) {
    this.editUser.emit(user);
  }

  onDeleteUser(id: number) {
    this.deleteUser.emit(id);
  }

  onPrevPage() {
    if (this.usersCurrentPage > 1) {
      this.pageChange.emit(this.usersCurrentPage - 1);
    }
  }

  onNextPage() {
    if (this.usersCurrentPage < this.usersTotalPages) {
      this.pageChange.emit(this.usersCurrentPage + 1);
    }
  }
}