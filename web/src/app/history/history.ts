interface HistoryEntry {
  id: number;
  lottery: string;
  numbers: number[];
  bonus_numbers: number[];
  created_at: string;
  draw_date: string;
  date: string;
  matchedNumbers?: number[];
  matchedBonus?: number[];
  unmatchedNumbers?: number[];
  unmatchedBonus?: number[];
  status?: 'Won' | 'Lost' | 'Pending';
}
