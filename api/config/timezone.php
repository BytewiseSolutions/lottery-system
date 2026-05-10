<?php

return [
    'default' => Env::get('TIMEZONE', 'Africa/Maseru'),
    
    'available' => [
        'Africa/Maseru' => 'Maseru (LST)',
        'Africa/Johannesburg' => 'Johannesburg (SAST)',
        'UTC' => 'UTC (Coordinated Universal Time)',
        'Africa/Cairo' => 'Cairo (EET)',
        'Africa/Lagos' => 'Lagos (WAT)',
        'Africa/Nairobi' => 'Nairobi (EAT)',
        'Europe/London' => 'London (GMT/BST)',
        'America/New_York' => 'New York (EST/EDT)',
        'America/Los_Angeles' => 'Los Angeles (PST/PDT)',
        'Asia/Tokyo' => 'Tokyo (JST)',
        'Australia/Sydney' => 'Sydney (AEST/AEDT)',
    ],
    
    'groups' => [
        'Africa' => [
            'Africa/Maseru' => 'Maseru (LST)',
            'Africa/Johannesburg' => 'Johannesburg (SAST)',
            'Africa/Cairo' => 'Cairo (EET)',
            'Africa/Lagos' => 'Lagos (WAT)',
            'Africa/Nairobi' => 'Nairobi (EAT)',
            'Africa/Casablanca' => 'Casablanca (WET)',
        ],
        'Europe' => [
            'Europe/London' => 'London (GMT/BST)',
            'Europe/Paris' => 'Paris (CET/CEST)',
            'Europe/Berlin' => 'Berlin (CET/CEST)',
            'Europe/Rome' => 'Rome (CET/CEST)',
        ],
        'America' => [
            'America/New_York' => 'New York (EST/EDT)',
            'America/Chicago' => 'Chicago (CST/CDT)',
            'America/Denver' => 'Denver (MST/MDT)',
            'America/Los_Angeles' => 'Los Angeles (PST/PDT)',
        ],
        'Asia' => [
            'Asia/Tokyo' => 'Tokyo (JST)',
            'Asia/Shanghai' => 'Shanghai (CST)',
            'Asia/Dubai' => 'Dubai (GST)',
            'Asia/Kolkata' => 'Mumbai (IST)',
        ],
        'Australia' => [
            'Australia/Sydney' => 'Sydney (AEST/AEDT)',
            'Australia/Melbourne' => 'Melbourne (AEST/AEDT)',
            'Australia/Perth' => 'Perth (AWST)',
        ],
    ],
    
    'draw_timezones' => [
        'lesotho' => 'Africa/Maseru',
        'south_africa' => 'Africa/Johannesburg',
        'international' => 'UTC',
    ],
    
    'business_hours' => [
        'timezone' => 'Africa/Maseru',
        'start' => '08:00',
        'end' => '17:00',
        'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
    ],
    
    'formats' => [
        'date' => 'Y-m-d',
        'time' => 'H:i:s',
        'datetime' => 'Y-m-d H:i:s',
        'display_date' => 'd M Y',
        'display_time' => 'H:i',
        'display_datetime' => 'd M Y H:i',
        'draw_time' => 'l, d F Y \a\t H:i',
    ],
];

class TimezoneHelper
{
    public static function getDefault(): string
    {
        $config = require __DIR__ . '/timezone.php';
        return $config['default'];
    }
    
    public static function getAvailable(): array
    {
        $config = require __DIR__ . '/timezone.php';
        return $config['available'];
    }

    public static function getGroups(): array
    {
        $config = require __DIR__ . '/timezone.php';
        return $config['groups'];
    }
    
    public static function convert(string $datetime, string $fromTimezone, string $toTimezone): string
    {
        $date = new DateTime($datetime, new DateTimeZone($fromTimezone));
        $date->setTimezone(new DateTimeZone($toTimezone));
        return $date->format('Y-m-d H:i:s');
    }

    public static function toUserTimezone(string $datetime, string $userTimezone = null): string
    {
        $userTimezone = $userTimezone ?: self::getDefault();
        return self::convert($datetime, 'UTC', $userTimezone);
    }
    
    public static function toUTC(string $datetime, string $fromTimezone = null): string
    {
        $fromTimezone = $fromTimezone ?: self::getDefault();
        return self::convert($datetime, $fromTimezone, 'UTC');
    }

    public static function now(string $timezone = null): string
    {
        $timezone = $timezone ?: self::getDefault();
        $date = new DateTime('now', new DateTimeZone($timezone));
        return $date->format('Y-m-d H:i:s');
    }
    
    public static function formatForDisplay(string $datetime, string $format = null, string $timezone = null): string
    {
        $config = require __DIR__ . '/timezone.php';
        $format = $format ?: $config['formats']['display_datetime'];
        $timezone = $timezone ?: self::getDefault();
        
        $date = new DateTime($datetime, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($timezone));
        return $date->format($format);
    }
    
    public static function formatDrawTime(string $datetime, string $timezone = null): string
    {
        $config = require __DIR__ . '/timezone.php';
        return self::formatForDisplay($datetime, $config['formats']['draw_time'], $timezone);
    }
    
    public static function isBusinessHours(): bool
    {
        $config = require __DIR__ . '/timezone.php';
        $businessConfig = $config['business_hours'];
        
        $now = new DateTime('now', new DateTimeZone($businessConfig['timezone']));
        $dayOfWeek = strtolower($now->format('l'));
        $currentTime = $now->format('H:i');
        
        if (!in_array($dayOfWeek, $businessConfig['days'])) {
            return false;
        }
        
        return $currentTime >= $businessConfig['start'] && $currentTime <= $businessConfig['end'];
    }
    
    public static function getOffset(string $timezone): string
    {
        $date = new DateTime('now', new DateTimeZone($timezone));
        return $date->format('P');
    }

    public static function isValid(string $timezone): bool
    {
        return in_array($timezone, timezone_identifiers_list());
    }

    public static function getNextDrawTime(string $drawTime, string $userTimezone = null): array
    {
        $userTimezone = $userTimezone ?: self::getDefault();
        
        $displayTime = self::toUserTimezone($drawTime, $userTimezone);
        $formatted = self::formatDrawTime($drawTime, $userTimezone);
        
        return [
            'utc' => $drawTime,
            'user_timezone' => $displayTime,
            'formatted' => $formatted,
            'timezone' => $userTimezone,
            'offset' => self::getOffset($userTimezone)
        ];
    }
}