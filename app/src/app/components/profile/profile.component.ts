import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { AuthService } from '../../services/auth.service';

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

  constructor(private auth: AuthService) {}

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
  }

  onLogout() {
    this.logoutClick.emit();
  }
}
