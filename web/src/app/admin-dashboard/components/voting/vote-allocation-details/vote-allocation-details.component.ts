import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BackendService } from '../../../../util/backend.service';
import { SidebarComponent } from '../../../sidebar/sidebar.component';

interface VoteDistribution {
  mainNumberVotes?: Record<string, number>;
  bonusNumberVotes?: Record<string, number>;
  mainNumbers?: number[];
  bonusNumbers?: number[];
}

interface VoteAllocationDetail {
  id: number;
  draw_id: number;
  lottery: string;
  numbers: number[];
  bonusNumbers: number[];
  bonus_numbers?: number[];
  allocatedVotes: number;
  allocated_votes: number;
  totalVotes: number;
  total_votes: number;
  drawDate: string;
  createdAt?: string;
  admin_name?: string;
  admin_email?: string;
  votingData?: VoteDistribution | null;
  voting_data?: VoteDistribution | null;
}

@Component({
  selector: 'app-vote-allocation-details',
  standalone: true,
  imports: [CommonModule, SidebarComponent],
  templateUrl: './vote-allocation-details.component.html',
  styleUrl: './vote-allocation-details.component.css'
})
export class VoteAllocationDetailsComponent implements OnInit {
  loading = true;
  error = false;
  allocation: VoteAllocationDetail | null = null;
  activeTab: 'main' | 'bonus' | 'meta' = 'main';

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private backendService: BackendService
  ) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));

    if (!id) {
      this.error = true;
      this.loading = false;
      return;
    }

    this.loadAllocation(id);
  }

  private loadAllocation(id: number): void {
    this.backendService.getAdminVoteById(id).subscribe({
      next: (response: any) => {
        if (!response?.success || !response.data) {
          this.error = true;
          this.loading = false;
          return;
        }

        this.allocation = response.data as VoteAllocationDetail;
        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load vote allocation details:', error);
        this.error = true;
        this.loading = false;
      }
    });
  }

  retryLoad(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (id) {
      this.loading = true;
      this.error = false;
      this.loadAllocation(id);
    }
  }

  goBack(): void {
    this.router.navigate(['/admin-dashboard/voting']);
  }

  getVoteMap(type: 'main' | 'bonus'): Array<[string, number]> {
    const votingData = this.allocation?.votingData || this.allocation?.voting_data;
    const map = type === 'main'
      ? (votingData?.mainNumberVotes || {})
      : (votingData?.bonusNumberVotes || {});

    return Object.entries(map).sort((a, b) => Number(a[0]) - Number(b[0]));
  }

  getNumbers(type: 'main' | 'bonus'): number[] {
    if (!this.allocation) return [];
    return type === 'main'
      ? (this.allocation.numbers || [])
      : (this.allocation.bonusNumbers || this.allocation.bonus_numbers || []);
  }

  getTotalVotes(): number {
    return this.allocation?.allocated_votes || this.allocation?.total_votes || 0;
  }

  formatDateTime(value?: string | null): string {
    if (!value) return 'Not available';

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) return value;

    return date.toLocaleString('en-GB', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    });
  }
}
