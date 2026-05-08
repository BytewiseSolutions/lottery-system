export interface Draw {
  id: number;
  lottery?: string;
  name?: string;
  code?: string;
  draw_date?: string;
  drawDate?: string;
  nextDraw?: string;
  jackpot: string | number;
  status: string;
  lottery_type?: string;
  lottery_closes_at?: string;
  entry_closes_at?: string;
  is_entry_open?: boolean;
  voting_closes_at?: string;
  is_voting_open?: boolean;
}
