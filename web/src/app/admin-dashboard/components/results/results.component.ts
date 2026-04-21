import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { ResultsFormComponent } from './results-form/results-form.component';
import { Router } from '@angular/router';

@Component({
  selector: 'app-results',
  imports: [
    CommonModule,
    SidebarComponent,
    ResultsFormComponent
  ],
  templateUrl: './results.component.html',
  styleUrl: './results.component.css'
})
export class ResultsComponent {
  isModalOpen = false;
  isEditMode = false;

  selectedResult: any = null;


  constructor(private router: Router) {}

  results = [
    {
      id: 1,
      lottery: 'Monday Lotto',
      draw: '20 April 2026',
      draw_id: '1',
      jackpot: 1568,
      winners_count: 170,
      status: 'published',
      winning_numbers: [5, 10, 18, 27, 44],
      bonus_numbers: [3, 9]
    }
  ];

  openModal() {
    this.isModalOpen = true;
    this.isEditMode = false;
    this.selectedResult = null;
  }

  closeModal() {
    this.isModalOpen = false;
    this.isEditMode = false;
    this.selectedResult = null;
  }
  viewResult(result: any) {
    this.router.navigate(['/admin-dashboard/results', result.id]);
  }
  editResult(result: any) {
    this.isModalOpen = true;
    this.isEditMode = true;

    this.selectedResult = {
      ...result
    };
  }

  saveResult(formData: any) {
    if (this.isEditMode) {
      console.log('Updating result:', formData);
    } else {
      console.log('Saving new result:', formData);
    }

    this.closeModal();
  }
}