export interface NotificationCreateRequest {
  title: string;
  message: string;
  type: string;
  recipient_type: string;
}