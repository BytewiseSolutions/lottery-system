import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { LayoutComponent } from '../layout/layout.component';
import { SuccessPopupService } from '../services/success-popup.service';
import { BackendService } from '../util/backend.service';
import { ErrorHandlerService } from '../services/error-handler.service';
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

  constructor(
    private successPopupService: SuccessPopupService,
    private backendService: BackendService,
    private errorHandlerService: ErrorHandlerService
  ) {}

  ngOnInit() {
    this.loadProfile();
    this.loadStats();
  }

  loadProfile() {
    this.loadProfileFromStorage();

    this.backendService.getUserProfile().subscribe({
      next: (response: any) => {
        if (response?.success && response.data) {
          this.applyProfileData(response.data);
        }
      },
      error: (error) => {
        console.error('Failed to load profile from api-refactor:', error);
      }
    });
  }

  private loadProfileFromStorage() {
    const user = localStorage.getItem('user');
    if (user) {
      const userData = JSON.parse(user);
      this.applyProfileData(userData, false);
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
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    this.backendService.updateUserProfile({
      first_name: this.profile.firstName,
      last_name: this.profile.lastName,
      email: this.profile.email,
      phone: this.profile.phone,
      country: this.profile.country
    }).subscribe({
      next: (response: any) => {
        this.isSaving = false;

        if (response?.success && response.data) {
          this.successPopupService.show('Your profile information has been updated successfully!', 'Profile Updated');
          this.applyProfileData(response.data);
          this.isEditing = false;

          const updatedUser = {
            ...user,
            first_name: response.data.first_name,
            last_name: response.data.last_name,
            full_name: response.data.full_name,
            fullName: response.data.full_name,
            email: response.data.email,
            phone: response.data.phone,
            country: response.data.country
          };

          localStorage.setItem('user', JSON.stringify(updatedUser));
          return;
        }

        this.errorHandlerService.showError(response?.message || 'Failed to update profile');
      },
      error: (error) => {
        this.isSaving = false;
        console.error('Update profile failed:', error);
        this.errorHandlerService.showError(error?.error?.message || 'Failed to update profile');
      }
    });
  }

  private applyProfileData(userData: any, updateStorage: boolean = true) {
    const firstName = userData.first_name || userData.firstName || '';
    const lastName = userData.last_name || userData.lastName || '';
    const fullName = userData.full_name || userData.fullName || `${firstName} ${lastName}`.trim();

    this.profile = {
      firstName: firstName || fullName.split(' ')[0] || '',
      lastName: lastName || fullName.split(' ').slice(1).join(' ') || '',
      email: userData.email || '',
      phone: userData.phone || '',
      country: userData.country || '',
      profilePicture: userData.profilePicture || userData.profile_picture || ''
    };
    this.originalProfile = { ...this.profile };
    this.previewUrl = this.profile.profilePicture || null;

    if (updateStorage) {
      const storedUser = JSON.parse(localStorage.getItem('user') || '{}');
      localStorage.setItem('user', JSON.stringify({
        ...storedUser,
        first_name: this.profile.firstName,
        last_name: this.profile.lastName,
        full_name: `${this.profile.firstName} ${this.profile.lastName}`.trim(),
        fullName: `${this.profile.firstName} ${this.profile.lastName}`.trim(),
        email: this.profile.email,
        phone: this.profile.phone,
        country: this.profile.country
      }));
    }
  }
}
