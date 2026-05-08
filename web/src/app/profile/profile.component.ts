import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { LayoutComponent } from '../layout/layout.component';
import { SuccessPopupService } from '../util/success-popup.service';
import { BackendService } from '../util/backend.service';
import { ErrorHandlerService } from '../util/error-handler.service';
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

  loadStats() {
    this.backendService.getCurrentUserStats().subscribe({
      next: (response: any) => {
        const statsData = response?.data;
        const date = statsData?.member_since ? new Date(statsData.member_since) : null;
        const formattedDate = date
          ? `${date.getDate()} ${date.toLocaleDateString('en-US', { month: 'short' })} ${date.getFullYear()}`
          : 'N/A';

        this.stats = {
          totalEntries: Number(statsData?.total_entries || 0),
          totalWinnings: statsData?.total_winnings || '0.00',
          memberSince: formattedDate
        };
      },
      error: (error) => {
        console.error('Error loading stats:', error);
        this.stats = {
          totalEntries: 0,
          totalWinnings: '0.00',
          memberSince: 'N/A'
        };
      }
    });
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
        this.errorHandlerService.showError('File size must be less than 5MB');
        return;
      }
      
      if (!file.type.startsWith('image/')) {
        this.errorHandlerService.showError('Please select an image file');
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

  uploadProfilePicture() {
    if (!this.selectedFile) return;
    
    this.isUploadingPicture = true;
    
    const formData = new FormData();
    formData.append('profilePicture', this.selectedFile);

    this.backendService.uploadFile(formData).subscribe({
      next: (response: any) => {
        this.isUploadingPicture = false;

        if (response?.success && response.data?.profile_picture_id) {
          const profilePictureId = response.data.profile_picture_id;
          const imageUrl = this.backendService.getFileUrl(profilePictureId);
          const user = JSON.parse(localStorage.getItem('user') || '{}');

          this.profile.profilePicture = imageUrl;
          this.previewUrl = imageUrl;
          this.selectedFile = null;

          localStorage.setItem('user', JSON.stringify({
            ...user,
            profilePicture: imageUrl,
            profile_picture: imageUrl,
            profile_picture_id: profilePictureId
          }));

          this.successPopupService.show('Profile picture updated successfully!', 'Success');
          return;
        }

        this.errorHandlerService.showError(response?.message || 'Failed to upload profile picture');
      },
      error: (error: any) => {
        this.isUploadingPicture = false;
        console.error('Upload error:', error);
        this.errorHandlerService.showError(error?.error?.message || 'Failed to upload profile picture');
      }
    });
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
      profilePicture: userData.profilePicture
        || userData.profile_picture
        || (userData.profile_picture_id ? this.backendService.getFileUrl(userData.profile_picture_id) : '')
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
        country: this.profile.country,
        profilePicture: this.profile.profilePicture,
        profile_picture: this.profile.profilePicture,
        profile_picture_id: userData.profile_picture_id ?? storedUser.profile_picture_id
      }));
    }
  }
}
