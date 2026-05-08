<?php

class AuthService
{
    private $authRepository;
    private $userRepository;
    private $activityLogService;

    public function __construct()
    {
        $this->authRepository = new AuthRepository();
        $this->userRepository = new UserRepository();
        $this->activityLogService = new ActivityLogService();
    }

    public function login($data)
    {
        $identifier = $data['identifier'] ?? null;
        $password   = $data['password'] ?? null;

        if (!$identifier || !$password) {
            Logger::info('Login failed - missing credentials');

            return [
                'success' => false,
                'message' => 'Email/Phone and password are required'
            ];
        }

        Logger::info('Login attempt', [
            'identifier' => $identifier,
            'time' => DateHelper::now()
        ]);

        $user = $this->userRepository->findByEmail($identifier);

        if (!$user) {
            $user = $this->userRepository->findByPhone($identifier);
        }

        if (!$user || !Hash::check($password, $user->password)) {
            Logger::info('Login failed - invalid credentials', [
                'identifier' => $identifier
            ]);

            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        $token = bin2hex(random_bytes(32));
        $this->authRepository->storeToken($user->id, $token);

        Logger::info('Login successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'time' => DateHelper::now()
        ]);

        $this->activityLogService->log(
            $user->id,
            'LOGIN',
            'User logged in'
        );

        return [
            'success' => true,
            'message' => 'Login successful',
            'token'   => $token,
            'user' => [
                'id'         => $user->id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'country'    => $user->country,
                'role'       => $user->role
            ]
        ];
    }

    public function register($data)
    {
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['password'])) {
            Logger::info('Registration failed - missing fields');

            return [
                'success' => false,
                'message' => 'Missing required fields'
            ];
        }

        if (!empty($data['email']) && $this->userRepository->findByEmail($data['email'])) {
            Logger::info('Registration failed - email exists', [
                'email' => $data['email']
            ]);

            return [
                'success' => false,
                'message' => 'Email already exists'
            ];
        }

        if (!empty($data['phone']) && $this->userRepository->findByPhone($data['phone'])) {
            Logger::info('Registration failed - phone exists', [
                'phone' => $data['phone']
            ]);

            return [
                'success' => false,
                'message' => 'Phone already exists'
            ];
        }

        try {
            $user = new User([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'email'      => $data['email'] ?? null,
                'phone'      => $data['phone'] ?? null,
                'country'    => $data['country'] ?? null,
                'password'   => Hash::make($data['password']),
                'role'       => ROLE_USER,
                'is_active'  => 1
            ]);

            $createdUser = $this->userRepository->create($user);

            if (!$createdUser) {
                Logger::error('User creation failed');

                return [
                    'success' => false,
                    'message' => 'Failed to create user'
                ];
            }

            Logger::info('User registered successfully', [
                'user_id' => $createdUser->id,
                'email' => $createdUser->email,
                'time' => DateHelper::now()
            ]);

            $this->activityLogService->log(
                $createdUser->id,
                'REGISTER',
                'User registered'
            );

            return [
                'success' => true,
                'message' => 'User registered successfully',
                'user_id' => $createdUser->id
            ];

        } catch (Exception $e) {
            Logger::error('Registration exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ];
        }
    }

    public function logout($token = null)
    {
        if (!$token) {
            Logger::info('Logout attempted without token');

            return [
                'success' => true,
                'message' => 'Logged out successfully'
            ];
        }

        $user = $this->authRepository->findUserByToken($token);

        $this->authRepository->deleteToken($token);

        if ($user) {
            Logger::info('Logout successful', [
                'user_id' => $user->id,
                'time' => DateHelper::now()
            ]);

            $this->activityLogService->log(
                $user->id,
                'LOGOUT',
                'User logged out'
            );
        }

        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }

    public function forgotPassword($data)
    {
        $email = $data['email'] ?? null;

        if (!$email) {
            Logger::info('Forgot password failed - missing email');

            return [
                'success' => false,
                'message' => 'Email is required'
            ];
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            Logger::info('Forgot password - email not found', [
                'email' => $email
            ]);

            return [
                'success' => false,
                'message' => 'Email not found'
            ];
        }

        $token = bin2hex(random_bytes(32));

        $this->authRepository->storeResetToken($user->id, $token);

        Logger::info('Password reset requested', [
            'user_id' => $user->id,
            'time' => DateHelper::now()
        ]);

        $this->activityLogService->log(
            $user->id,
            'FORGOT_PASSWORD',
            'Password reset requested'
        );

        return [
            'success' => true,
            'message' => 'Reset token generated',
            'token'   => $token
        ];
    }

    public function resetPassword($data)
    {
        $token    = $data['token'] ?? null;
        $password = $data['password'] ?? null;
        $confirm  = $data['confirm_password'] ?? null;

        if (!$token || !$password || !$confirm) {
            return [
                'success' => false,
                'message' => 'All fields are required'
            ];
        }

        if ($password !== $confirm) {
            return [
                'success' => false,
                'message' => 'Passwords do not match'
            ];
        }

        $reset = $this->authRepository->findResetToken($token);

        if (!$reset) {
            Logger::info('Reset password failed - invalid token');

            return [
                'success' => false,
                'message' => 'Invalid token'
            ];
        }

        $hashedPassword = Hash::make($password);

        $this->authRepository->updatePassword($reset['user_id'], $hashedPassword);
        $this->authRepository->deleteResetToken($token);

        Logger::info('Password reset successful', [
            'user_id' => $reset['user_id'],
            'time' => DateHelper::now()
        ]);

        $this->activityLogService->log(
            $reset['user_id'],
            'RESET_PASSWORD',
            'Password changed successfully'
        );

        return [
            'success' => true,
            'message' => 'Password reset successful'
        ];
    }
}