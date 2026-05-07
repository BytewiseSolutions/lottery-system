import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Subscription } from 'rxjs';
import { ModalService } from '../../../util/modal.service';
import { ModalConfig } from '../../../util/modal-config.interface';
import { ModalManagerComponent } from '../modal-manager/modal-manager.component';

@Component({
  selector: 'app-global-modal',
  standalone: true,
  imports: [CommonModule, ModalManagerComponent],
  template: `
    <app-modal-manager
      [isVisible]="currentModal !== null"
      [title]="currentModal?.title || ''"
      [content]="getModalContent()"
      [modalSize]="currentModal?.modalSize || 'modal-md'"
      [showFooter]="currentModal?.showFooter || false"
      [showCancelButton]="currentModal?.showCancelButton || false"
      [showConfirmButton]="currentModal?.showConfirmButton || true"
      [cancelText]="currentModal?.cancelText || 'Cancel'"
      [confirmText]="currentModal?.confirmText || 'OK'"
      (modalClosed)="onModalClosed()"
      (modalConfirmed)="onModalConfirmed()"
      (modalCancelled)="onModalCancelled()"
    ></app-modal-manager>
  `,
  styles: [`
    .modal-icon {
      font-size: 24px;
      margin-right: 12px;
      vertical-align: middle;
    }
    .modal-icon.success { color: #28a745; }
    .modal-icon.error { color: #dc3545; }
    .modal-icon.warning { color: #ffc107; }
    .modal-icon.info { color: #17a2b8; }
    .modal-icon.confirm { color: #6c757d; }
    
    .modal-message {
      display: inline-block;
      vertical-align: middle;
      line-height: 1.5;
    }
  `]
})
export class GlobalModalComponent implements OnInit, OnDestroy {
  currentModal: ModalConfig | null = null;
  private subscription: Subscription = new Subscription();

  constructor(private modalService: ModalService) {}

  ngOnInit(): void {
    this.subscription.add(
      this.modalService.modal$.subscribe(modal => {
        this.currentModal = modal;
      })
    );
  }

  ngOnDestroy(): void {
    this.subscription.unsubscribe();
  }

  getModalContent(): string {
    if (!this.currentModal) return '';
    
    const icon = this.getIcon(this.currentModal.type);
    return `
      <div style="display: flex; align-items: flex-start;">
        <i class="modal-icon ${this.currentModal.type} ${icon}"></i>
        <div class="modal-message">${this.currentModal.message}</div>
      </div>
    `;
  }

  private getIcon(type: string): string {
    switch (type) {
      case 'success': return 'fas fa-check-circle';
      case 'error': return 'fas fa-exclamation-circle';
      case 'warning': return 'fas fa-exclamation-triangle';
      case 'info': return 'fas fa-info-circle';
      case 'confirm': return 'fas fa-question-circle';
      default: return 'fas fa-info-circle';
    }
  }

  onModalClosed(): void {
    this.modalService.hide();
  }

  onModalConfirmed(): void {
    if (this.currentModal?.onConfirm) {
      this.currentModal.onConfirm();
    }
    this.modalService.hide();
  }

  onModalCancelled(): void {
    if (this.currentModal?.onCancel) {
      this.currentModal.onCancel();
    }
    this.modalService.hide();
  }
}