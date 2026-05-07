export interface ResultDetail {
  id: number;
  draw_id: number;
  lottery: string;
  draw_date: string;
  jackpot: number | string;
  status: string;
  winning_numbers: number[];
  bonus_numbers: number[];
  created_at?: string;
  updated_at?: string;
  total_entries?: number;
}