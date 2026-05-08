<?php

return [
    // Application Information
    'name' => 'Lottery System API',
    'version' => '1.0.0',
    'url' => Env::get('APP_URL', 'http://localhost'),
    'debug' => Env::get('APP_DEBUG', 'true') === 'true',
    'environment' => Env::get('APP_ENV', 'development'),
    'timezone' => Env::get('TIMEZONE', 'Africa/Maseru'),

    // API Configuration
    'api' => [
        'rate_limit' => 100,                    // requests per minute
        'timeout' => 30,                        // seconds
        'max_file_size' => 5 * 1024 * 1024,    // 5MB
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
        'pagination_limit' => 20,               // default items per page
        'max_pagination_limit' => 100,         // maximum items per page
    ],

    // Security Configuration
    'security' => [
        'jwt_secret' => Env::get('JWT_SECRET', 'your_jwt_secret_key_here'),
        'jwt_expiry' => 3600,                   // 1 hour in seconds
        'refresh_token_expiry' => 604800,       // 7 days in seconds
        'password_min_length' => 8,
        'password_require_special' => true,
        'max_login_attempts' => 5,
        'lockout_duration' => 900,              // 15 minutes in seconds
        'session_timeout' => 1800,              // 30 minutes in seconds
        'encryption_key' => Env::get('ENCRYPTION_KEY', 'your_encryption_key_here'),
    ],

    // Lottery System Configuration
    'lottery' => [
        'main_numbers_count' => 5,
        'bonus_numbers_count' => 2,
        'main_number_range' => [1, 50],         // [min, max]
        'bonus_number_range' => [1, 12],        // [min, max]
        'default_jackpot' => 10.00,             // Default jackpot amount
        'min_jackpot' => 5.00,                  // Minimum jackpot
        'max_votes_per_user' => 10,             // Maximum votes per user per draw
        'draw_close_hours' => 2,                // Hours before draw to close voting
        'auto_generate_winners' => true,        // Auto generate winners after draw
    ],

    // Draw Schedule Configuration
    'draw_schedule' => [
        'frequency' => 'weekly',                // daily, weekly, monthly
        'draw_days' => ['saturday'],            // Days of week for draws
        'draw_time' => '20:00',                 // Time of day (24-hour format)
        'timezone' => 'Africa/Maseru',
    ],

    // Payment Configuration
    'payment' => [
        'min_payout' => 1.00,                   // Minimum payout amount
        'max_payout' => 1000000.00,             // Maximum payout amount
        'processing_fee_percent' => 2.5,        // Processing fee percentage
        'auto_approve_under' => 100.00,         // Auto-approve payments under this amount
        'require_verification_over' => 1000.00, // Require ID verification over this amount
    ],

    // Notification Configuration
    'notifications' => [
        'email_enabled' => true,
        'sms_enabled' => false,
        'push_enabled' => false,
        'notify_on_win' => true,
        'notify_on_draw' => true,
        'notify_on_payment' => true,
        'batch_size' => 100,                    // Notifications to send per batch
    ],

    // File Upload Configuration
    'uploads' => [
        'path' => 'uploads/',
        'max_size' => 5 * 1024 * 1024,         // 5MB
        'allowed_types' => [
            'profile_picture' => ['jpg', 'jpeg', 'png'],
            'id_document' => ['jpg', 'jpeg', 'png', 'pdf'],
            'proof_of_address' => ['jpg', 'jpeg', 'png', 'pdf'],
        ],
        'image_quality' => 85,                  // JPEG quality for resized images
        'thumbnail_size' => [150, 150],         // Thumbnail dimensions
    ],

    // Logging Configuration
    'logging' => [
        'enabled' => true,
        'level' => Env::get('LOG_LEVEL', 'info'), // debug, info, warning, error
        'max_files' => 30,                      // Keep logs for 30 days
        'log_queries' => Env::get('APP_DEBUG', 'false') === 'true',
        'log_api_requests' => true,
    ],

    // Cache Configuration
    'cache' => [
        'enabled' => true,
        'default_ttl' => 3600,                  // 1 hour
        'draw_results_ttl' => 86400,            // 24 hours
        'user_profile_ttl' => 1800,             // 30 minutes
    ],

    // Email Configuration
    'mail' => [
        'host' => Env::get('MAIL_HOST', 'localhost'),
        'port' => Env::get('MAIL_PORT', 587),
        'username' => Env::get('MAIL_USERNAME', ''),
        'password' => Env::get('MAIL_PASSWORD', ''),
        'from_email' => Env::get('MAIL_FROM', 'noreply@lottery-system.com'),
        'from_name' => 'Lottery System',
        'encryption' => 'tls',
    ],

    // Analytics Configuration
    'analytics' => [
        'track_user_activity' => true,
        'track_api_usage' => true,
        'retention_days' => 365,                // Keep analytics data for 1 year
        'popular_numbers_count' => 10,          // Show top 10 popular numbers
    ],
];