export interface User {
  id: number;
  first_name?: string;
  last_name?: string;
  full_name: string;
  email: string;
  phone?: string;
  country?: string;
  is_active: boolean | number;
  role?: string;
  created_at: string;
  updated_at?: string;
}
