import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { ModalConfig } from './modal-config.interface';

@Injectable({
  providedIn: 'root'
})
export class ModalService {
  private modalSubject = new BehaviorSubject<ModalConfig | null>(null);
  public modal$ = this.modalSubject.asObservable();

  private nextId = 1;

  showError(message: string, title: string = 'Error') {
    this.show({
      id: this.generateId(),
      title,
      message,
      type: 'error',
      showFooter: true,
      showCancelButton: false,
      showConfirmButton: true,
      confirmText: 'OK',
      modalSize: 'modal-md'
    });
  }

  showSuccess(message: string, title: string = 'Success') {
    this.show({
      id: this.generateId(),
      title,
      message,
      type: 'success',
      showFooter: true,
      showCancelButton: false,
      showConfirmButton: true,
      confirmText: 'OK',
      modalSize: 'modal-md'
    });
  }

  showWarning(message: string, title: string = 'Warning') {
    this.show({
      id: this.generateId(),
      title,
      message,
      type: 'warning',
      showFooter: true,
      showCancelButton: false,
      showConfirmButton: true,
      confirmText: 'OK',
      modalSize: 'modal-md'
    });
  }

  showInfo(message: string, title: string = 'Information') {
    this.show({
      id: this.generateId(),
      title,
      message,
      type: 'info',
      showFooter: true,
      showCancelButton: false,
      showConfirmButton: true,
      confirmText: 'OK',
      modalSize: 'modal-md'
    });
  }

  showConfirm(
    message: string, 
    title: string = 'Confirm Action',
    onConfirm?: () => void,
    onCancel?: () => void
  ) {
    this.show({
      id: this.generateId(),
      title,
      message,
      type: 'confirm',
      showFooter: true,
      showCancelButton: true,
      showConfirmButton: true,
      cancelText: 'Cancel',
      confirmText: 'Confirm',
      modalSize: 'modal-md',
      onConfirm,
      onCancel
    });
  }

  show(config: ModalConfig) {
    this.modalSubject.next(config);
  }

  hide() {
    this.modalSubject.next(null);
  }

  private generateId(): string {
    return `modal_${this.nextId++}_${Date.now()}`;
  }
}