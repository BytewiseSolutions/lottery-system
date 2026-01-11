import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-manage-results',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './manage-results.component.html',
  styleUrls: ['./manage-results.component.scss']
})
export class ManageResultsComponent {
  @Input() results: any[] = [];
  @Input() paginatedResults: any[] = [];
  @Input() selectedResults = new Set<number>();
  @Input() bulkAction = '';
  @Input() currentPage = 1;
  @Input() totalPages = 1;
  @Input() totalResults = 0;

  @Output() uploadClick = new EventEmitter<void>();
  @Output() exportClick = new EventEmitter<void>();
  @Output() selectAllChange = new EventEmitter<Event>();
  @Output() selectResultChange = new EventEmitter<number>();
  @Output() viewResult = new EventEmitter<any>();
  @Output() editResult = new EventEmitter<any>();
  @Output() toggleStatus = new EventEmitter<any>();
  @Output() deleteResult = new EventEmitter<number>();
  @Output() bulkActionApply = new EventEmitter<void>();
  @Output() pageChange = new EventEmitter<number>();

  onUploadClick() {
    this.uploadClick.emit();
  }

  onExportClick() {
    this.exportClick.emit();
  }

  onSelectAllChange(event: Event) {
    this.selectAllChange.emit(event);
  }

  onSelectResultChange(id: number) {
    this.selectResultChange.emit(id);
  }

  onViewResult(result: any) {
    this.viewResult.emit(result);
  }

  onEditResult(result: any) {
    this.editResult.emit(result);
  }

  onToggleStatus(result: any) {
    this.toggleStatus.emit(result);
  }

  onDeleteResult(id: number) {
    this.deleteResult.emit(id);
  }

  onBulkActionApply() {
    this.bulkActionApply.emit();
  }

  onPrevPage() {
    if (this.currentPage > 1) {
      this.pageChange.emit(this.currentPage - 1);
    }
  }

  onNextPage() {
    if (this.currentPage < this.totalPages) {
      this.pageChange.emit(this.currentPage + 1);
    }
  }
}