export interface ModalConfig {
  id: string;
  title: string;
  message: string;
  type: 'success' | 'error' | 'warning' | 'info' | 'confirm';
  showFooter?: boolean;
  showCancelButton?: boolean;
  showConfirmButton?: boolean;
  cancelText?: string;
  confirmText?: string;
  modalSize?: 'modal-sm' | 'modal-md' | 'modal-lg' | 'modal-xl';
  onConfirm?: () => void;
  onCancel?: () => void;
}