import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/environment';
import { VoteAllocationDetailsComponent } from '../vote-allocation-details/vote-allocation-details.component';

@Component({
  selector: 'app-voting-management',
  imports: [CommonModule, FormsModule, VoteAllocationDetailsComponent],
  templateUrl: './voting-management.component.html',
  styleUrls: ['./voting-management.component.css']
})
export class VotingManagementComponent implements OnInit {
  lottery = 'Monday Lotto';
  drawDate = '';
  selectedNumbers: number[] = [];
  selectedBonus: number[] = [];
  allocatedVotes = 1000;
  numbers = Array.from({length: 75}, (_, i) => i + 1);
  adminVotes: any[] = [];
  currentStep: 'section1' | 'section2' = 'section1';
  showSuccessPopup = false;
  
  showVotePopup = false;
  selectedNumberForVoting: number | null = null;
  voteAmount = 0;
  currentSection: 'main' | 'bonus' = 'main';
  
  mainNumberVotes: { [key: number]: number } = {};
  bonusNumberVotes: { [key: number]: number } = {};
  
  Object = Object;
  Number = Number;
  

  isLoading = false;
  isSaving = false;
  isDeleting = false;
  loadingMessage = '';
  
  showDeleteDialog = false;
  voteToDelete: any = null;
  deleteSuccessMessage = '';
  
  showInfoDialog = false;
  infoMessage = '';
  
  selectedVoteDetails: any = null;
  
  currentView: 'management' | 'details' | 'edit' = 'management';
  
  // Edit mode state
  editingVote: any = null;
  editedMainVotes: { [key: number]: number } = {};
  editedBonusVotes: { [key: number]: number } = {};
  
  // Edit popup states
  showEditVotePopup = false;
  selectedEditNumber: number | null = null;
  selectedEditType: 'main' | 'bonus' = 'main';
  editVoteAmount = 0;
  
  // Search and filtering
  selectedLotteryFilter = '';
  dateFromFilter = '';
  dateToFilter = '';
  minVotesFilter = '';
  maxVotesFilter = '';
  sortBy = 'date';
  sortOrder = 'desc';
  filteredAdminVotes: any[] = [];
  
  constructor(private http: HttpClient) {}
  
  ngOnInit() {
    this.loadAdminVotes();
    this.drawDate = this.getTodayDate();
    this.setDefaultDateRange();
    this.filteredAdminVotes = this.adminVotes;
  }
  
  getTodayDate(): string {
    const today = new Date();
    return today.toISOString().split('T')[0];
  }
  
  setDefaultDateRange() {
    const today = new Date();
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(today.getDate() - 30);
    
    this.dateToFilter = today.toISOString().split('T')[0];
    this.dateFromFilter = thirtyDaysAgo.toISOString().split('T')[0];
  }
  
  selectMainNumber(num: number) {
    if (this.currentStep !== 'section1') return;
    if (Object.keys(this.mainNumberVotes).length >= 5 && !this.mainNumberVotes[num]) {
      this.showInfoMessage('You can only allocate votes to 5 numbers in Section 1');
      return;
    }
    
    this.selectedNumberForVoting = num;
    this.voteAmount = this.mainNumberVotes[num] || 0;
    this.currentSection = 'main';
    this.showVotePopup = true;
  }
  
  selectBonusNumber(num: number) {
    if (this.currentStep !== 'section2') return;
    if (this.mainNumberVotes[num] > 0) return; // Can't select main numbers as bonus
    if (Object.keys(this.bonusNumberVotes).length >= 2 && !this.bonusNumberVotes[num]) {
      this.showInfoMessage('You can only allocate votes to 2 numbers in Section 2');
      return;
    }
    
    this.selectedNumberForVoting = num;
    this.voteAmount = this.bonusNumberVotes[num] || 0;
    this.currentSection = 'bonus';
    this.showVotePopup = true;
  }
  
  allocateVotesToNumber() {
    if (!this.selectedNumberForVoting || this.voteAmount < 0) return;
    
    if (this.currentSection === 'main') {
      if (this.voteAmount === 0) {
        delete this.mainNumberVotes[this.selectedNumberForVoting];
      } else {
        this.mainNumberVotes[this.selectedNumberForVoting] = this.voteAmount;
      }
    } else if (this.currentSection === 'bonus') {
      if (this.voteAmount === 0) {
        delete this.bonusNumberVotes[this.selectedNumberForVoting];
      } else {
        this.bonusNumberVotes[this.selectedNumberForVoting] = this.voteAmount;
      }
    }
    
    this.closeVotePopup();
  }
  
  closeVotePopup() {
    this.showVotePopup = false;
    this.selectedNumberForVoting = null;
    this.voteAmount = 0;
  }
  
  removeMainVotes(num: number) {
    delete this.mainNumberVotes[num];
  }
  
  removeBonusVotes(num: number) {
    delete this.bonusNumberVotes[num];
  }
  
  getTotalMainVotes(): number {
    return Object.values(this.mainNumberVotes).reduce((sum, votes) => sum + votes, 0);
  }
  
  getTotalBonusVotes(): number {
    return Object.values(this.bonusNumberVotes).reduce((sum, votes) => sum + votes, 0);
  }
  
  nextToSection2() {
    if (Object.keys(this.mainNumberVotes).length === 5) {
      this.currentStep = 'section2';
    }
  }
  
  backToSection1() {
    this.currentStep = 'section1';
  }
  
  nextStep() {
    // This method is no longer used in the new workflow
    // Navigation is handled by nextToSection2() and backToSection1()
  }
  
  editNumbers() {
    this.currentStep = 'section1';
  }
  
  allocateAllVotes() {
    const token = localStorage.getItem('token');
    if (!token) return;
    
    if (Object.keys(this.mainNumberVotes).length !== 5 || Object.keys(this.bonusNumberVotes).length !== 2) {
      this.showInfoMessage('Please allocate votes to exactly 5 main numbers and 2 bonus numbers');
      return;
    }
    
    if (!this.drawDate) {
      this.showInfoMessage('Please select a draw date');
      return;
    }
    
    // Check if allocation already exists for this lottery and date
    const existingAllocation = this.adminVotes.find(vote => 
      vote.lottery === this.lottery && vote.drawDate === this.drawDate
    );
    
    if (existingAllocation) {
      this.showInfoMessage(`Vote allocation already exists for ${this.lottery} on ${this.drawDate}. Please choose a different date or delete the existing allocation.`);
      return;
    }
    
    this.isSaving = true;
    this.loadingMessage = 'Allocating votes...';
    
    const mainNumbers = Object.keys(this.mainNumberVotes).map(n => parseInt(n));
    const bonusNumbers = Object.keys(this.bonusNumberVotes).map(n => parseInt(n));
    const totalVotes = this.getTotalMainVotes() + this.getTotalBonusVotes();
    
    const votingData = {
      mainNumberVotes: this.mainNumberVotes,
      bonusNumberVotes: this.bonusNumberVotes,
      mainNumbers: mainNumbers,
      bonusNumbers: bonusNumbers
    };
    
    this.http.post(`${environment.apiUrl}/admin-vote`, {
      lottery: this.lottery,
      numbers: mainNumbers,
      bonusNumbers: bonusNumbers,
      allocatedVotes: totalVotes,
      votingData: votingData,
      totalVotes: totalVotes,
      mainNumbers: mainNumbers,
      voteDate: this.drawDate
    }, {
      headers: { 'Authorization': `Bearer ${token}` }
    }).subscribe({
      next: () => {
        this.isSaving = false;
        this.deleteSuccessMessage = `${totalVotes} votes allocated successfully for ${this.lottery} on ${this.drawDate}!`;
        this.showSuccessPopup = true;
        setTimeout(() => {
          this.showSuccessPopup = false;
          this.resetForm();
          this.loadAdminVotes();
        }, 3000);
      },
      error: (err) => {
        this.isSaving = false;
        this.showInfoMessage(err.error?.error || 'Failed to allocate votes');
      }
    });
  }
  
  resetForm() {
    this.selectedNumbers = [];
    this.selectedBonus = [];
    this.currentStep = 'section1';
    this.mainNumberVotes = {};
    this.bonusNumberVotes = {};
    this.allocatedVotes = 1000;
    this.drawDate = this.getTodayDate();
  }
  
  loadAdminVotes() {
    const token = localStorage.getItem('token');
    if (!token) return;
    
    this.isLoading = true;
    this.loadingMessage = 'Loading vote allocations...';
    
    this.http.get<any>(`${environment.apiUrl}/admin-vote`, {
      headers: { 'Authorization': `Bearer ${token}` }
    }).subscribe({
      next: (data) => {
        this.isLoading = false;
        this.adminVotes = data.adminVotes;
        this.applyFilters();
      },
      error: (err) => {
        this.isLoading = false;
        console.error(err);
        this.showInfoMessage('Failed to load vote allocations');
      }
    });
  }
  
  deleteVoteAllocation(vote: any) {
    this.voteToDelete = vote;
    this.showDeleteDialog = true;
  }
  
  confirmDelete() {
    if (!this.voteToDelete) return;
    
    const token = localStorage.getItem('token');
    if (!token) return;
    
    this.isDeleting = true;
    this.loadingMessage = 'Deleting vote allocation...';
    
    // Use the original endpoint without .php extension
    this.http.delete(`${environment.apiUrl}/admin-vote?id=${this.voteToDelete.id}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    }).subscribe({
      next: () => {
        this.isDeleting = false;
        this.deleteSuccessMessage = 'Vote allocation deleted successfully!';
        this.showSuccessPopup = true;
        setTimeout(() => {
          this.showSuccessPopup = false;
          this.loadAdminVotes(); // Refresh the list
        }, 2000);
        this.cancelDelete();
      },
      error: (err) => {
        this.isDeleting = false;
        this.showInfoMessage(err.error?.error || 'Failed to delete vote allocation');
        this.cancelDelete();
      }
    });
  }
  
  cancelDelete() {
    this.showDeleteDialog = false;
    this.voteToDelete = null;
  }
  
  showSuccessMessage(message: string) {
    // Deprecated - now using success popup
    this.deleteSuccessMessage = message;
    this.showSuccessPopup = true;
    setTimeout(() => {
      this.showSuccessPopup = false;
    }, 2000);
  }
  
  showInfoMessage(message: string) {
    this.infoMessage = message;
    this.showInfoDialog = true;
  }
  
  closeInfoDialog() {
    this.showInfoDialog = false;
    this.infoMessage = '';
  }
  
  viewVoteDetails(vote: any) {
    this.selectedVoteDetails = vote;
    this.currentView = 'details';
  }
  
  closeVoteDetails() {
    this.currentView = 'management';
    this.selectedVoteDetails = null;
  }
  
  editVoteAllocation(vote: any) {
    this.editingVote = vote;
    this.editedMainVotes = { ...vote.votingData?.mainNumberVotes || {} };
    this.editedBonusVotes = { ...vote.votingData?.bonusNumberVotes || {} };
    this.currentView = 'edit';
  }
  
  cancelEditVote() {
    this.currentView = 'management';
    this.editingVote = null;
    this.editedMainVotes = {};
    this.editedBonusVotes = {};
  }
  
  openEditVotePopup(number: number, type: 'main' | 'bonus') {
    this.selectedEditNumber = number;
    this.selectedEditType = type;
    this.editVoteAmount = type === 'main' ? 
      (this.editedMainVotes[number] || 0) : 
      (this.editedBonusVotes[number] || 0);
    this.showEditVotePopup = true;
  }
  
  closeEditVotePopup() {
    this.showEditVotePopup = false;
    this.selectedEditNumber = null;
    this.editVoteAmount = 0;
  }
  
  updateEditVoteAmount() {
    if (this.selectedEditNumber === null) return;
    
    if (this.selectedEditType === 'main') {
      if (this.editVoteAmount <= 0) {
        delete this.editedMainVotes[this.selectedEditNumber];
      } else {
        this.editedMainVotes[this.selectedEditNumber] = this.editVoteAmount;
      }
    } else {
      if (this.editVoteAmount <= 0) {
        delete this.editedBonusVotes[this.selectedEditNumber];
      } else {
        this.editedBonusVotes[this.selectedEditNumber] = this.editVoteAmount;
      }
    }
    
    this.closeEditVotePopup();
  }
  
  saveEditedVotes() {
    const token = localStorage.getItem('token');
    if (!token) {
      this.showInfoMessage('Authentication required');
      return;
    }
    
    // Validate that we still have the required numbers
    const mainCount = Object.keys(this.editedMainVotes).length;
    const bonusCount = Object.keys(this.editedBonusVotes).length;
    
    if (mainCount !== 5) {
      this.showInfoMessage('Must have exactly 5 main numbers with votes');
      return;
    }
    
    if (bonusCount !== 2) {
      this.showInfoMessage('Must have exactly 2 bonus numbers with votes');
      return;
    }
    
    this.isSaving = true;
    this.loadingMessage = 'Saving changes...';
    
    const mainNumbers = Object.keys(this.editedMainVotes).map(n => parseInt(n));
    const bonusNumbers = Object.keys(this.editedBonusVotes).map(n => parseInt(n));
    const totalVotes = this.getEditedMainTotal() + this.getEditedBonusTotal();
    
    const votingData = {
      mainNumberVotes: this.editedMainVotes,
      bonusNumberVotes: this.editedBonusVotes,
      mainNumbers: mainNumbers,
      bonusNumbers: bonusNumbers
    };
    
    // Update the existing allocation
    this.http.put(`${environment.apiUrl}/admin-vote`, {
      id: this.editingVote.id,
      lottery: this.editingVote.lottery,
      numbers: mainNumbers,
      bonusNumbers: bonusNumbers,
      allocatedVotes: totalVotes,
      votingData: votingData,
      totalVotes: totalVotes,
      voteDate: this.editingVote.drawDate || this.editingVote.voteDate
    }, {
      headers: { 'Authorization': `Bearer ${token}` }
    }).subscribe({
      next: () => {
        this.isSaving = false;
        this.deleteSuccessMessage = `Vote allocation updated successfully! Total: ${totalVotes} votes`;
        this.showSuccessPopup = true;
        setTimeout(() => {
          this.showSuccessPopup = false;
          this.cancelEditVote();
          this.loadAdminVotes(); // Refresh the list
        }, 2000);
      },
      error: (err) => {
        this.isSaving = false;
        this.showInfoMessage(err.error?.error || 'Failed to update vote allocation');
      }
    });
  }
  
  getEditedMainTotal(): number {
    return Object.values(this.editedMainVotes).reduce((sum, votes) => sum + votes, 0);
  }
  
  getEditedBonusTotal(): number {
    return Object.values(this.editedBonusVotes).reduce((sum, votes) => sum + votes, 0);
  }
  
  removeEditMainVotes(num: number) {
    delete this.editedMainVotes[num];
  }
  
  applyFilters() {
    let filtered = [...this.adminVotes];
    
    // Lottery type filter
    if (this.selectedLotteryFilter) {
      filtered = filtered.filter(vote => vote.lottery === this.selectedLotteryFilter);
    }
    
    // Date range filter
    if (this.dateFromFilter) {
      filtered = filtered.filter(vote => {
        const voteDate = vote.drawDate || vote.voteDate;
        return voteDate >= this.dateFromFilter;
      });
    }
    
    if (this.dateToFilter) {
      filtered = filtered.filter(vote => {
        const voteDate = vote.drawDate || vote.voteDate;
        return voteDate <= this.dateToFilter;
      });
    }
    
    // Vote amount filter
    if (this.minVotesFilter) {
      const minVotes = parseInt(this.minVotesFilter);
      filtered = filtered.filter(vote => (vote.allocatedVotes || vote.totalVotes) >= minVotes);
    }
    
    if (this.maxVotesFilter) {
      const maxVotes = parseInt(this.maxVotesFilter);
      filtered = filtered.filter(vote => (vote.allocatedVotes || vote.totalVotes) <= maxVotes);
    }
    
    // Sorting
    filtered.sort((a, b) => {
      let aValue, bValue;
      
      switch (this.sortBy) {
        case 'lottery':
          aValue = a.lottery;
          bValue = b.lottery;
          break;
        case 'votes':
          aValue = a.allocatedVotes || a.totalVotes;
          bValue = b.allocatedVotes || b.totalVotes;
          break;
        case 'date':
        default:
          aValue = a.drawDate || a.voteDate;
          bValue = b.drawDate || b.voteDate;
          break;
      }
      
      if (this.sortOrder === 'asc') {
        return aValue > bValue ? 1 : -1;
      } else {
        return aValue < bValue ? 1 : -1;
      }
    });
    
    this.filteredAdminVotes = filtered;
  }
  
  clearFilters() {
    this.selectedLotteryFilter = '';
    this.minVotesFilter = '';
    this.maxVotesFilter = '';
    this.sortBy = 'date';
    this.sortOrder = 'desc';
    this.setDefaultDateRange(); // Reset to default 30-day range
    this.applyFilters();
  }
  
  onFilterChange() {
    this.applyFilters();
  }
  
  removeEditBonusVotes(num: number) {
    delete this.editedBonusVotes[num];
  }
  
  quickPick() {
    this.selectedNumbers = [];
    const available = [...this.numbers];
    for (let i = 0; i < 5; i++) {
      const randomIndex = Math.floor(Math.random() * available.length);
      this.selectedNumbers.push(available[randomIndex]);
      available.splice(randomIndex, 1);
    }
    this.selectedNumbers.sort((a, b) => a - b);
  }
  
  quickPickBonus() {
    this.selectedBonus = [];
    const available = [...this.numbers];
    for (let i = 0; i < 2; i++) {
      const randomIndex = Math.floor(Math.random() * available.length);
      this.selectedBonus.push(available[randomIndex]);
      available.splice(randomIndex, 1);
    }
    this.selectedBonus.sort((a, b) => a - b);
  }
}
