import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { ToastService } from '../services/toast.service';

@Component({
  selector: 'app-edit-profile',
  templateUrl: './edit-profile.page.html',
  styleUrls: ['./edit-profile.page.scss'],
  standalone: false,
})
export class EditProfilePage implements OnInit {
  profile: any = { full_name: '', email: '', phone: '' };
  loading = false;

  constructor(
    private auth: AuthService,
    private toast: ToastService,
    private router: Router
  ) {}

  ngOnInit() {
    this.auth.getProfile().subscribe({
      next: (data) => this.profile = data,
      error: () => this.toast.showError('Failed to load profile')
    });
  }

  save() {
    this.loading = true;
    this.auth.updateProfile(this.profile).subscribe({
      next: () => {
        this.toast.showSuccess('Profile updated');
        this.router.navigate(['/settings']);
      },
      error: () => {
        this.toast.showError('Update failed');
        this.loading = false;
      }
    });
  }
}
