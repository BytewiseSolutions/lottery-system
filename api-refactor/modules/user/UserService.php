<?php

class UserService
{
    private $userRepository;
    private $activityLogService;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->activityLogService = new ActivityLogService();
    }

    public function register(UserDto $userDto)
    {
        $validationErrors = Validator::user($userDto);

        if (!empty($validationErrors)) {

            Logger::info('User registration validation failed', [
                'errors' => $validationErrors
            ]);

            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validationErrors
            ];
        }

        if ($this->userRepository->emailExists($userDto->email)) {

            Logger::info('Duplicate email attempt', [
                'email' => $userDto->email
            ]);

            return [
                'success' => false,
                'message' => 'Email already exists',
                'errors' => ['email' => 'This email is already registered']
            ];
        }

        if (!empty($userDto->phone) && $this->userRepository->phoneExists($userDto->phone)) {

            Logger::info('Duplicate phone attempt', [
                'phone' => $userDto->phone
            ]);

            return [
                'success' => false,
                'message' => 'Phone number already exists',
                'errors' => ['phone' => 'This phone number is already registered']
            ];
        }

        try {
            $user = $userDto->toUser();
            $user->role = ROLE_USER;
            $user->is_active = 1;
            $user->password = Hash::make($userDto->password);

            $createdUser = $this->userRepository->create($user);

            if ($createdUser) {

                Logger::info('User registered successfully', [
                    'user_id' => $createdUser->id,
                    'email' => $createdUser->email
                ]);

                $this->activityLogService->log(
                    $createdUser->id,
                    ACTION_USER_REGISTER,
                    'User registered successfully'
                );

                return [
                    'success' => true,
                    'message' => SUCCESS_REGISTER,
                    'data' => $createdUser->toArray()
                ];
            }

            Logger::error('User creation failed');

            return [
                'success' => false,
                'message' => 'Failed to create user account'
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

    public function getUserById($id)
    {
        try {
            $user = $this->userRepository->findById($id);

            if ($user) {

                Logger::info('User fetched', [
                    'user_id' => $id
                ]);

                return [
                    'success' => true,
                    'data' => $user->toArray()
                ];
            }

            Logger::info('User not found', [
                'user_id' => $id
            ]);

            return [
                'success' => false,
                'message' => ERROR_USER_NOT_FOUND
            ];

        } catch (Exception $e) {

            Logger::error('Get user by ID failed', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return [
                'success' => false,
                'message' => 'Failed to retrieve user'
            ];
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $user = $this->userRepository->findByEmail($email);

            if ($user) {

                Logger::info('User fetched by email', [
                    'email' => $email
                ]);

                return [
                    'success' => true,
                    'data' => $user->toArray()
                ];
            }

            return [
                'success' => false,
                'message' => ERROR_USER_NOT_FOUND
            ];

        } catch (Exception $e) {

            Logger::error('Get user by email failed', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);

            return [
                'success' => false,
                'message' => 'Failed to retrieve user'
            ];
        }
    }

    public function getAllUsers($page = 1, $limit = 20)
    {
        return $this->getUsersPage($page, $limit, []);
    }

    public function getUsersPage($page = 1, $limit = 20, $filters = [])
    {
        try {
            $page = max(1, (int)$page);
            $limit = min(MAX_PAGE_SIZE, max(MIN_PAGE_SIZE, (int)$limit));

            $users = $this->userRepository->getAll($page, $limit, $filters);
            $totalCount = (int)$this->userRepository->getTotalCount($filters);
            $totalPages = ceil($totalCount / $limit);

            $usersArray = [];

            foreach ($users as $user) {
                $usersArray[] = $user->toArray();
            }

            Logger::info('Fetched users list', [
                'count' => NumberHelper::format($totalCount),
                'page' => $page
            ]);

            return [
                'success' => true,
                'data' => $usersArray,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total_items' => $totalCount,
                    'total_pages' => $totalPages,
                    'has_next' => $page < $totalPages,
                    'has_prev' => $page > 1
                ]
            ];

        } catch (Exception $e) {

            Logger::error('Get all users failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to retrieve users'
            ];
        }
    }

    public function createAdminUser($currentUser, $data)
    {
        try {
            $validation = $this->validateAdminUserPayload($data, false);

            if (!empty($validation['errors'])) {
                return [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation['errors']
                ];
            }

            $payload = $validation['payload'];

            if ($this->userRepository->emailExists($payload['email'])) {
                return [
                    'success' => false,
                    'message' => 'Email already exists',
                    'errors' => ['email' => 'This email is already registered']
                ];
            }

            if (!empty($payload['phone']) && $this->userRepository->phoneExists($payload['phone'])) {
                return [
                    'success' => false,
                    'message' => 'Phone number already exists',
                    'errors' => ['phone' => 'This phone number is already registered']
                ];
            }

            $user = new User([
                'first_name' => $payload['first_name'],
                'last_name' => $payload['last_name'],
                'email' => $payload['email'],
                'phone' => $payload['phone'],
                'country' => $payload['country'],
                'password' => Hash::make($payload['password']),
                'role' => $payload['role'],
                'is_active' => $payload['is_active']
            ]);

            $createdUser = $this->userRepository->create($user);

            if (!$createdUser) {
                return [
                    'success' => false,
                    'message' => 'Failed to create user'
                ];
            }

            $this->activityLogService->log(
                $currentUser->id,
                'USER_CREATE',
                'Created user account for ' . $createdUser->email
            );

            return [
                'success' => true,
                'message' => 'User created successfully',
                'data' => $createdUser->toArray()
            ];
        } catch (Exception $e) {
            Logger::error('Create admin user failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create user'
            ];
        }
    }

    public function updateUser($currentUser, $id, $data)
    {
        try {
            $existingUser = $this->userRepository->findById($id);

            if (!$existingUser) {
                return [
                    'success' => false,
                    'message' => ERROR_USER_NOT_FOUND
                ];
            }

            $validation = $this->validateAdminUserPayload($data, true);

            if (!empty($validation['errors'])) {
                return [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation['errors']
                ];
            }

            $payload = $validation['payload'];

            if ($this->userRepository->emailExists($payload['email'], $existingUser->id)) {
                return [
                    'success' => false,
                    'message' => 'Email already exists',
                    'errors' => ['email' => 'This email is already registered']
                ];
            }

            if (!empty($payload['phone']) && $this->userRepository->phoneExists($payload['phone'], $existingUser->id)) {
                return [
                    'success' => false,
                    'message' => 'Phone number already exists',
                    'errors' => ['phone' => 'This phone number is already registered']
                ];
            }

            $existingUser->first_name = $payload['first_name'];
            $existingUser->last_name = $payload['last_name'];
            $existingUser->email = $payload['email'];
            $existingUser->phone = $payload['phone'];
            $existingUser->country = $payload['country'];
            $existingUser->role = $payload['role'];
            $existingUser->is_active = $payload['is_active'];

            if (!$this->userRepository->update($existingUser)) {
                return [
                    'success' => false,
                    'message' => 'Failed to update user'
                ];
            }

            $updatedUser = $this->userRepository->findById($existingUser->id);

            $this->activityLogService->log(
                $currentUser->id,
                'USER_UPDATE',
                'Updated user account for ' . $updatedUser->email
            );

            return [
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $updatedUser ? $updatedUser->toArray() : $existingUser->toArray()
            ];
        } catch (Exception $e) {
            Logger::error('Update user failed', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update user'
            ];
        }
    }

    private function validateAdminUserPayload($data, $isEdit = false)
    {
        $errors = [];

        $firstName = trim((string)($data['first_name'] ?? ''));
        $lastName = trim((string)($data['last_name'] ?? ''));
        $fullName = trim((string)($data['full_name'] ?? ''));

        if (($firstName === '' || $lastName === '') && $fullName !== '') {
            [$firstName, $lastName] = $this->splitFullName($fullName);
        }

        $email = trim((string)($data['email'] ?? ''));
        $phone = trim((string)($data['phone'] ?? ''));
        $country = trim((string)($data['country'] ?? ''));
        $password = (string)($data['password'] ?? '');
        $role = trim((string)($data['role'] ?? ROLE_USER));
        $isActive = isset($data['is_active']) ? (int)(filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? ((int)$data['is_active'])) : 1;

        if ($firstName === '') {
            $errors['first_name'] = 'First name is required';
        } elseif (strlen($firstName) < MIN_NAME_LENGTH) {
            $errors['first_name'] = 'First name is too short';
        }

        if ($lastName === '') {
            $errors['last_name'] = 'Last name is required';
        }

        if ($email === '') {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address';
        }

        if ($phone !== '' && strlen($phone) > MAX_PHONE_LENGTH) {
            $errors['phone'] = 'Phone number is too long';
        }

        if (!$isEdit && $password === '') {
            $errors['password'] = 'Password is required';
        } elseif (!$isEdit && strlen($password) < MIN_PASSWORD_LENGTH) {
            $errors['password'] = 'Password must be at least ' . MIN_PASSWORD_LENGTH . ' characters';
        }

        if (!in_array($role, [ROLE_USER, ROLE_ADMIN], true)) {
            $errors['role'] = 'Invalid role selected';
        }

        return [
            'errors' => $errors,
            'payload' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'country' => $country,
                'password' => $password,
                'role' => $role,
                'is_active' => $isActive === 1 ? 1 : 0
            ]
        ];
    }

    private function splitFullName($fullName)
    {
        $parts = preg_split('/\s+/', trim($fullName)) ?: [];
        $firstName = $parts[0] ?? '';
        $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        return [$firstName, $lastName];
    }
}
