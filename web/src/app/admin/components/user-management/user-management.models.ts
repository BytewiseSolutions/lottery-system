export interface UserRecord {
  id: number;
  full_name: string;
  email: string;
  phone?: string;
  country?: string;
  is_active: boolean;
  email_verified: boolean;
  phone_verified: boolean;
  role?: string;
  last_login?: string;
  created_at: string;
  updated_at?: string;
  profile_picture?: string;
  total_votes?: number;
  total_winnings?: number;
  login_count?: number;
}

export interface UserStats {
  total_users: number;
  active_users: number;
  verified_users: number;
  new_users_today: number;
  new_users_this_week: number;
  new_users_this_month: number;
}

export interface UserFormValue {
  full_name: string;
  email: string;
  phone: string;
  country: string;
  password: string;
  role: string;
  is_active: boolean;
  email_verified: boolean;
  phone_verified: boolean;
}

export type UserFormPayload = Partial<UserRecord> & {
  password?: string;
};

export interface UserListResponse {
  users?: UserRecord[];
  total?: number;
  totalPages?: number;
  currentPage?: number;
  countries?: string[];
}

export interface UserStatsResponse {
  stats?: UserStats;
}

export interface UserResponse {
  user: UserRecord;
}
