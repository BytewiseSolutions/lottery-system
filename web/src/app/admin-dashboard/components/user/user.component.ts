import { Component } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { CommonModule } from '@angular/common';
import { Payload } from './payload';

@Component({
  selector: 'app-user',
  imports: [CommonModule, SidebarComponent],
  templateUrl: './user.component.html',
  styleUrl: './user.component.css'
})
export class UserComponent {
  isEditMode = false;
  currentUser: User | null = null;
  formError = '';
  userForm: Payload = {};
  showUserModal = false;

  
  openAddUserModal() {
    this.isEditMode = false;
    this.currentUser = null;
    this.formError = '';
    this.userForm = {
      is_active: true,
      email_verified: false,
      phone_verified: false,
      role: 'user'
    };
    this.showUserModal = true;
  }
}
