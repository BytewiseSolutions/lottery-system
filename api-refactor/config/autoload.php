<?php

spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../';
    
    $classMap = [
        'CorsMiddleware' => 'middleware/CorsMiddleware.php',
        'AuthMiddleware' => 'middleware/AuthMiddleware.php',
        'RoleMiddleware' => 'middleware/RoleMiddleware.php',
        'RateLimitMiddleware' => 'middleware/RateLimitMiddleware.php',
        'RequestMiddleware' => 'middleware/RequestMiddleware.php',
        
        'Connection' => 'database/Connection.php',
        'QueryBuilder' => 'database/QueryBuilder.php',
        'Migration' => 'database/Migration.php',
        'Seeder' => 'database/Seeder.php',
        
        'Response' => 'utils/Response.php',
        'Hash' => 'utils/Hash.php',
        'DateHelper' => 'utils/DateHelper.php',
        'NumberHelper' => 'utils/NumberHelper.php',
        'Validator' => 'utils/Validator.php',
        'Logger' => 'utils/Logger.php',
        
        'User' => 'modules/user/User.php',
        'UserController' => 'modules/user/UserController.php',
        'UserService' => 'modules/user/UserService.php',
        'UserRepository' => 'modules/user/UserRepository.php',
        'UserDto' => 'modules/user/UserDto.php',
        
        'AuthController' => 'modules/auth/AuthController.php',
        'AuthService' => 'modules/auth/AuthService.php',
        'AuthRepository' => 'modules/auth/AuthRepository.php',
        'AuthDto' => 'modules/auth/AuthDto.php',
        
        'Vote' => 'modules/vote/Vote.php',
        'VoteController' => 'modules/vote/VoteController.php',
        'VoteService' => 'modules/vote/VoteService.php',
        'VoteRepository' => 'modules/vote/VoteRepository.php',
        'VoteDto' => 'modules/vote/VoteDto.php',

        'Entry' => 'modules/entry/Entry.php',
        'EntryController' => 'modules/entry/EntryController.php',
        'EntryService' => 'modules/entry/EntryService.php',
        'EntryRepository' => 'modules/entry/EntryRepository.php',
        'EntryDto' => 'modules/entry/EntryDto.php',
        
        'Draw' => 'modules/draw/Draw.php',
        'DrawController' => 'modules/draw/DrawController.php',
        'DrawService' => 'modules/draw/DrawService.php',
        'DrawRepository' => 'modules/draw/DrawRepository.php',
        'DrawDto' => 'modules/draw/DrawDto.php',
        
        'Result' => 'modules/result/Result.php',
        'ResultController' => 'modules/result/ResultController.php',
        'ResultService' => 'modules/result/ResultService.php',
        'ResultRepository' => 'modules/result/ResultRepository.php',
        'ResultDto' => 'modules/result/ResultDto.php',
        
        'Winner' => 'modules/winner/Winner.php',
        'WinnerController' => 'modules/winner/WinnerController.php',
        'WinnerService' => 'modules/winner/WinnerService.php',
        'WinnerRepository' => 'modules/winner/WinnerRepository.php',
        'WinnerDto' => 'modules/winner/WinnerDto.php',
        
        'Payment' => 'modules/payment/Payment.php',
        'PaymentController' => 'modules/payment/PaymentController.php',
        'PaymentService' => 'modules/payment/PaymentService.php',
        'PaymentRepository' => 'modules/payment/PaymentRepository.php',
        'PaymentDto' => 'modules/payment/PaymentDto.php',
        
        'Notification' => 'modules/notification/Notification.php',
        'NotificationController' => 'modules/notification/NotificationController.php',
        'NotificationService' => 'modules/notification/NotificationService.php',
        'NotificationRepository' => 'modules/notification/NotificationRepository.php',
        'NotificationDto' => 'modules/notification/NotificationDto.php',
        
        'DataFile' => 'modules/file/DataFile.php',
        'FileController' => 'modules/file/FileController.php',
        'FileService' => 'modules/file/FileService.php',
        'FileRepository' => 'modules/file/FileRepository.php',
        'FileDto' => 'modules/file/FileDto.php',
        
        'Analytics' => 'modules/analytics/Analytics.php',
        'AnalyticsController' => 'modules/analytics/AnalyticsController.php',
        'AnalyticsService' => 'modules/analytics/AnalyticsService.php',
        'AnalyticsRepository' => 'modules/analytics/AnalyticsRepository.php',
        'AnalyticsDto' => 'modules/analytics/AnalyticsDto.php',
        'HighestVoteService' => 'modules/analytics/HighestVoteService.php',
        'VoteStatisticsService' => 'modules/analytics/VoteStatisticsService.php',
        
        'ActivityLog' => 'modules/audit/ActivityLog.php',
        'ActivityLogController' => 'modules/audit/ActivityLogController.php',
        'ActivityLogService' => 'modules/audit/ActivityLogService.php',
        'ActivityLogRepository' => 'modules/audit/ActivityLogRepository.php',
        'ActivityLogDto' => 'modules/audit/ActivityLogDto.php',
        
        'Settings' => 'modules/settings/Settings.php',
        'SettingsController' => 'modules/settings/SettingsController.php',
        'SettingsService' => 'modules/settings/SettingsService.php',
        'SettingsRepository' => 'modules/settings/SettingsRepository.php',
        'SettingsDto' => 'modules/settings/SettingsDto.php',

        
    ];
    
    if (isset($classMap[$class])) {
        $file = $baseDir . $classMap[$class];
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
