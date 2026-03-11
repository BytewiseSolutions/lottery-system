import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { LayoutComponent } from '../layout/layout.component';
import { SuccessPopupService } from '../services/success-popup.service';
import { environment } from '../../environments/environment';
import { COUNTRIES } from '../shared/data/countries';

@Component({
  selector: 'app-profile',
  imports: [CommonModule, FormsModule, LayoutComponent],
  templateUrl: './profile.component.html'
})
export class ProfileComponent implements OnInit {
  profile = {
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    country: '',
    profilePicture: ''
  };

  originalProfile = { ...this.profile };

  stats = {
    totalEntries: 0,
    totalWinnings: '0.00',
    memberSince: ''
  };

  isEditing = false;
  isSaving = false;
  countries = COUNTRIES;
  filteredCountries = COUNTRIES.slice(0, 5);
  showCountryDropdown = false;
  selectedFile: File | null = null;
  previewUrl: string | null = null;
  isUploadingPicture = false;

  constructor(private successPopupService: SuccessPopupService) {}

  ngOnInit() {
    this.loadProfile();
    this.loadStats();
  }

  loadProfile() {
    const user = localStorage.getItem('user');
    if (user) {
      const userData = JSON.parse(user);
      const names = userData.fullName?.split(' ') || ['', ''];
      this.profile = {
        firstName: names[0] || '',
        lastName: names.slice(1).join(' ') || '',
        email: userData.email || '',
        phone: userData.phone || '',
        country: userData.country || '',
        profilePicture: userData.profilePicture || ''
      };
      this.originalProfile = { ...this.profile };
      this.previewUrl = this.profile.profilePicture || null;
    }
  }

  async loadStats() {
    const user = localStorage.getItem('user');
    if (!user) return;

    const userData = JSON.parse(user);
    const token = localStorage.getItem('token');

    try {
      const response = await fetch(`${environment.apiUrl}/user-stats?userId=${userData.id}`, {
        headers: { 'Authorization': `Bearer ${token}` }
      });
      const data = await response.json();
      console.log('Stats API response:', data);
      
      if (data.success) {
        const date = data.memberSince ? new Date(data.memberSince) : null;
        const formattedDate = date ? `${date.getDate()} ${date.toLocaleDateString('en-US', { month: 'short' })} ${date.getFullYear()}` : 'N/A';
        
        this.stats = {
          totalEntries: data.totalEntries || 0,
          totalWinnings: data.totalWinnings || '0.00',
          memberSince: formattedDate
        };
        console.log('Processed stats:', this.stats);
      }
    } catch (error) {
      console.error('Error loading stats:', error);
      this.stats = {
        totalEntries: 0,
        totalWinnings: '0.00',
        memberSince: 'N/A'
      };
    }
  }

  toggleEdit() {
    this.isEditing = true;
  }

  cancelEdit() {
    this.profile = { ...this.originalProfile };
    this.isEditing = false;
    this.showCountryDropdown = false;
  }

  filterCountries(event: any) {
    const searchTerm = event.target.value.toLowerCase();
    if (!searchTerm) {
      this.filteredCountries = COUNTRIES.slice(0, 5);
    } else {
      this.filteredCountries = COUNTRIES.filter(c => 
        c.toLowerCase().startsWith(searchTerm)
      );
    }
    this.showCountryDropdown = true;
  }

  selectCountry(country: string) {
    this.profile.country = country;
    this.showCountryDropdown = false;
  }

  onFileSelected(event: any) {
    const file = event.target.files[0];
    if (file) {
      if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        return;
      }
      
      if (!file.type.startsWith('image/')) {
        alert('Please select an image file');
        return;
      }
      
      this.selectedFile = file;
      
      const reader = new FileReader();
      reader.onload = (e: any) => {
        this.previewUrl = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  }

  async uploadProfilePicture() {
    if (!this.selectedFile) return;
    
    this.isUploadingPicture = true;
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    
    const formData = new FormData();
    formData.append('profilePicture', this.selectedFile);
    formData.append('userId', user.id);
    
    try {
      const response = await fetch(`${environment.apiUrl}/upload-profile-picture.php`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`
        },
        body: formData
      });
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const result = await response.json();
      
      if (result.success) {
        // Build the URL to get the image
        const imageUrl = `${environment.apiUrl}/get-file.php?id=${result.profilePictureId}`;
        this.profile.profilePicture = imageUrl;
        this.previewUrl = imageUrl;
        user.profilePicture = imageUrl;
        localStorage.setItem('user', JSON.stringify(user));
        this.successPopupService.show('Profile picture updated successfully!', 'Success');
        this.selectedFile = null;
      } else {
        alert(result.error || 'Failed to upload profile picture');
      }
    } catch (error: any) {
      console.error('Upload error:', error);
      alert('Network error. Please make sure the API server is running on port 8000.');
    } finally {
      this.isUploadingPicture = false;
    }
  }

  removeProfilePicture() {
    this.previewUrl = null;
    this.selectedFile = null;
    this.profile.profilePicture = '';
  }

  async updateProfile() {
    this.isSaving = true;
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    try {
      const response = await fetch(`${environment.apiUrl}/update-profile`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          userId: user.id,
          fullName: `${this.profile.firstName} ${this.profile.lastName}`.trim(),
          email: this.profile.email,
          phone: this.profile.phone,
          country: this.profile.country
        })
      });

      const result = await response.json();
      console.log('Update profile response:', result);

      if (result.success) {
        this.successPopupService.show('Your profile information has been updated successfully!', 'Profile Updated');
        this.originalProfile = { ...this.profile };
        this.isEditing = false;
        
        user.fullName = `${this.profile.firstName} ${this.profile.lastName}`.trim();
        user.email = this.profile.email;
        user.phone = this.profile.phone;
        user.country = this.profile.country;
        localStorage.setItem('user', JSON.stringify(user));
      } else {
        alert(result.error || 'Failed to update profile');
      }
    } catch (error) {
      alert('Network error. Please try again.');
    } finally {
      this.isSaving = false;
    }
  }
}
