export interface EntryDetail {
  id: number;
  user_id: number;
  draw_id: number;
  lottery: string;
  numbers: number[];
  bonus_numbers: number[];
  draw_date: string;
  draw_datetime?: string;
  created_at?: string;
  user_name?: string;
  user_email?: string;
  user_phone?: string;
}
