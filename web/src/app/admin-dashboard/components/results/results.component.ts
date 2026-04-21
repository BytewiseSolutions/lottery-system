import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { SidebarComponent } from '../../sidebar/sidebar.component';
import { ResultsFormComponent } from './results-form/results-form.component';

@Component({
  selector: 'app-results',
  imports: [CommonModule, SidebarComponent, ResultsFormComponent],
  templateUrl: './results.component.html',
  styleUrl: './results.component.css'
})
export class ResultsComponent {
  isModalOpen = false;

  openModal() {
    this.isModalOpen = true;
  }

  closeModal() {
    this.isModalOpen = false;
  }

  saveResult(formData: any) {
    console.log('Saving result:', formData);
    this.closeModal();
  }
}
