import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { NotificationService } from '../../services/notification.service';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.scss'],
  standalone: false,
})
export class ProfileComponent  implements OnInit {
  @Input() userName: string = '';
  @Output() logoutClick = new EventEmitter<void>();
  userDetails: any = {};
  unreadCount = 0;

  constructor(
    private auth: AuthService, 
    private router: Router,
    private notificationService: NotificationService
  ) {}

  ngOnInit() {
    this.auth.user$.subscribe(user => {
      if (user) {
        this.userDetails = {
          name: user.fullName || user.name,
          email: user.email,
          phone: user.phone,
          role: user.role
        };
      }
    });
    
    this.notificationService.unreadCount$.subscribe(count => {
      this.unreadCount = count;
    });
  }

  viewWinnings() {
    this.router.navigate(['/winnings']);
  }

  onLogout() {
    this.logoutClick.emit();
  }
}
