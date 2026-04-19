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
        try {
            $page = max(1, (int)$page);
            $limit = min(MAX_PAGE_SIZE, max(MIN_PAGE_SIZE, (int)$limit));

            $users = $this->userRepository->getAll($page, $limit);
            $totalCount = $this->userRepository->getTotalCount();
            $totalPages = ceil($totalCount / $limit);

            $usersArray = [];

            foreach ($users as $user) {
                $usersArray[] = [
                    'id' => $user->id,
                    'name' => $user->getFullName(),
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'created_at' => $user->created_at
                ];
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
}