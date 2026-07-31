<?php

class SettingsService
{
    private $settingsRepository;
    private $activityLogService;

    public function __construct()
    {
        $this->settingsRepository = new SettingsRepository();
        $this->activityLogService = new ActivityLogService();
    }

    public function getSettings()
    {
        try {
            $storedSettings = $this->settingsRepository->getAll();
            $defaults = $this->getDefaultSettings();
            $settings = array_merge($defaults, $storedSettings);

            return [
                'success' => true,
                'data' => $this->normalizeSettings($settings)
            ];
        } catch (Exception $e) {
            Logger::error('Get settings failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to retrieve settings'
            ];
        }
    }

    public function updateSettings($currentUser, $data)
    {
        try {
            $validation = $this->validateSettings($data);

            if (!empty($validation['errors'])) {
                return [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation['errors']
                ];
            }

            $payload = $validation['payload'];

            $this->settingsRepository->upsertMany($payload, $currentUser->id);

            $this->activityLogService->log(
                $currentUser->id,
                'SETTINGS_UPDATE',
                'Updated system settings'
            );

            return [
                'success' => true,
                'message' => 'Settings updated successfully',
                'data' => $this->normalizeSettings($payload)
            ];
        } catch (Exception $e) {
            Logger::error('Update settings failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update settings'
            ];
        }
    }

    private function getDefaultSettings()
    {
        return [
            'site_name' => 'Total Free Lotto',
            'support_email' => '',
            'support_phone' => '',
            'currency_symbol' => '$',
            'default_draw_jackpot' => '10.00',
            'maintenance_mode' => '0',
            'registration_enabled' => '1',
            'notifications_enabled' => '1'
        ];
    }

    private function normalizeSettings($settings)
    {
        return [
            'site_name' => (string)($settings['site_name'] ?? 'Total Free Lotto'),
            'support_email' => (string)($settings['support_email'] ?? ''),
            'support_phone' => (string)($settings['support_phone'] ?? ''),
            'currency_symbol' => (string)($settings['currency_symbol'] ?? '$'),
            'default_draw_jackpot' => number_format((float)($settings['default_draw_jackpot'] ?? 10), 2, '.', ''),
            'maintenance_mode' => $this->toBoolean($settings['maintenance_mode'] ?? false),
            'registration_enabled' => $this->toBoolean($settings['registration_enabled'] ?? true),
            'notifications_enabled' => $this->toBoolean($settings['notifications_enabled'] ?? true)
        ];
    }

    private function validateSettings($data)
    {
        $errors = [];

        $siteName = trim((string)($data['site_name'] ?? ''));
        $supportEmail = trim((string)($data['support_email'] ?? ''));
        $supportPhone = trim((string)($data['support_phone'] ?? ''));
        $currencySymbol = trim((string)($data['currency_symbol'] ?? '$'));
        $defaultDrawJackpot = trim((string)($data['default_draw_jackpot'] ?? '10.00'));
        $maintenanceMode = $this->toBoolean($data['maintenance_mode'] ?? false) ? '1' : '0';
        $registrationEnabled = $this->toBoolean($data['registration_enabled'] ?? true) ? '1' : '0';
        $notificationsEnabled = $this->toBoolean($data['notifications_enabled'] ?? true) ? '1' : '0';

        if ($siteName === '') {
            $errors['site_name'] = 'Site name is required';
        } elseif (strlen($siteName) > 120) {
            $errors['site_name'] = 'Site name is too long';
        }

        if ($supportEmail !== '' && !filter_var($supportEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['support_email'] = 'Support email is invalid';
        }

        if ($supportPhone !== '' && strlen($supportPhone) > MAX_PHONE_LENGTH) {
            $errors['support_phone'] = 'Support phone number is too long';
        }

        if ($defaultDrawJackpot === '' || !is_numeric($defaultDrawJackpot)) {
            $errors['default_draw_jackpot'] = 'Default jackpot must be a valid number';
        } elseif ((float)$defaultDrawJackpot < 0) {
            $errors['default_draw_jackpot'] = 'Default jackpot cannot be negative';
        }

        return [
            'errors' => $errors,
            'payload' => [
                'site_name' => $siteName,
                'support_email' => $supportEmail,
                'support_phone' => $supportPhone,
                'currency_symbol' => $currencySymbol ?: '$',
                'default_draw_jackpot' => number_format((float)$defaultDrawJackpot, 2, '.', ''),
                'maintenance_mode' => $maintenanceMode,
                'registration_enabled' => $registrationEnabled,
                'notifications_enabled' => $notificationsEnabled
            ]
        ];
    }

    private function toBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
