import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-vote-allocation-details',
  imports: [CommonModule],
  templateUrl: './vote-allocation-details.component.html',
  styleUrls: ['./vote-allocation-details.component.css']
})
export class VoteAllocationDetailsComponent {
  @Input() voteDetails: any = null;
  @Output() close = new EventEmitter<void>();

  Object = Object;
  Number = Number;

  onClose() {
    this.close.emit();
  }

  getIndividualVotes(number: number, type: 'main' | 'bonus'): number {
    if (!this.voteDetails?.votingData) return 0;
    
    if (type === 'main') {
      return this.voteDetails.votingData.mainNumberVotes?.[number] || 0;
    } else {
      return this.voteDetails.votingData.bonusNumberVotes?.[number] || 0;
    }
  }

  getMainVotesTotal(): number {
    if (!this.voteDetails?.votingData?.mainNumberVotes) return 0;
    return Object.values(this.voteDetails.votingData.mainNumberVotes).reduce((sum: number, votes: any) => sum + votes, 0);
  }

  getBonusVotesTotal(): number {
    if (!this.voteDetails?.votingData?.bonusNumberVotes) return 0;
    return Object.values(this.voteDetails.votingData.bonusNumberVotes).reduce((sum: number, votes: any) => sum + votes, 0);
  }

  getVotePercentage(votes: number): number {
    const total = this.voteDetails?.allocatedVotes || this.voteDetails?.totalVotes || 1;
    return (votes / total) * 100;
  }

  getAverageVotesPerNumber(): number {
    const total = this.voteDetails?.allocatedVotes || this.voteDetails?.totalVotes || 0;
    const numberCount = (this.voteDetails?.numbers?.length || 0) + (this.voteDetails?.bonusNumbers?.length || 0);
    return numberCount > 0 ? Math.round(total / numberCount) : 0;
  }
}