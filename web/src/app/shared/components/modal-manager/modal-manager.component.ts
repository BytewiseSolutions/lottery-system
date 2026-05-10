import { Component, Input, Output, EventEmitter, TemplateRef } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-modal-manager',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './modal-manager.component.html',
  styleUrls: ['./modal-manager.component.scss']
})
export class ModalManagerComponent {
  @Input() isVisible = false;
  @Input() title = '';
  @Input() content = '';
  @Input() contentTemplate: TemplateRef<any> | null = null;
  @Input() modalSize = 'modal-md';
  @Input() showCloseButton = true;
  @Input() showFooter = false;
  @Input() showCancelButton = true;
  @Input() showConfirmButton = true;
  @Input() cancelText = 'Cancel';
  @Input() confirmText = 'Confirm';

  @Output() modalClosed = new EventEmitter<void>();
  @Output() modalConfirmed = new EventEmitter<void>();
  @Output() modalCancelled = new EventEmitter<void>();

  close() {
    this.isVisible = false;
    this.modalClosed.emit();
  }

  confirm() {
    this.modalConfirmed.emit();
    this.close();
  }

  cancel() {
    this.modalCancelled.emit();
    this.close();
  }

  show() {
    this.isVisible = true;
  }

  hide() {
    this.isVisible = false;
  }
}