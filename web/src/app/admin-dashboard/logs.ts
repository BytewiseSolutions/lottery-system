export interface ActivityLog {
  id: number;
  user_id?: number;
  action: string;
  details?: string;
  ip_address?: string;
  created_at: string;
  user_name?: string;
}