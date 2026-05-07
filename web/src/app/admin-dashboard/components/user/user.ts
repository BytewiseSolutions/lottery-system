interface User {
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