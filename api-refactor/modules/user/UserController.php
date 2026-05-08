<?php

class UserController
{
    private $userService;
    private $authRepository;
    private $userRepository;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->authRepository = new AuthRepository();
        $this->userRepository = new UserRepository();
    }

    public function register()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                Response::json(false, 'Invalid JSON input', null, HTTP_BAD_REQUEST);
            }

            $userDto = new UserDto($input);
            
            $result = $this->userService->register($userDto);
            
            if ($result['success']) {
                Response::json(
                    true, 
                    $result['message'], 
                    $result['data'], 
                    HTTP_CREATED
                );
            } else {
                $statusCode = isset($result['errors']) ? HTTP_UNPROCESSABLE_ENTITY : HTTP_BAD_REQUEST;
                $data = isset($result['errors']) ? ['errors' => $result['errors']] : null;
                
                Response::json(
                    false, 
                    $result['message'], 
                    $data, 
                    $statusCode
                );
            }
            
        } catch (Exception $e) {
            error_log("Registration controller error: " . $e->getMessage());
            Response::json(false, 'Registration failed', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getProfile()
    {
        try {
            $userId = $_GET['user_id'] ?? null;
            
            if (!$userId) {
                Response::json(false, 'User ID required', null, HTTP_BAD_REQUEST);
            }

            $result = $this->userService->getUserById($userId);
            
            if ($result['success']) {
                Response::json(true, 'Profile retrieved successfully', $result['data'], HTTP_OK);
            } else {
                Response::json(false, $result['message'], null, HTTP_NOT_FOUND);
            }
            
        } catch (Exception $e) {
            error_log("Get profile controller error: " . $e->getMessage());
            Response::json(false, 'Failed to retrieve profile', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getCurrentProfile()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Authentication required', null, HTTP_UNAUTHORIZED);
            }

            $result = $this->userService->getUserById($currentUser->id);
            
            if ($result['success']) {
                Response::json(true, 'Profile retrieved successfully', $result['data'], HTTP_OK);
            } else {
                Response::json(false, $result['message'], null, HTTP_NOT_FOUND);
            }
            
        } catch (Exception $e) {
            error_log("Get current profile controller error: " . $e->getMessage());
            Response::json(false, 'Failed to retrieve profile', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getUsers()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$currentUser->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? DEFAULT_PAGE_SIZE;
            $filters = [
                'search' => trim((string)($_GET['search'] ?? '')),
                'role' => trim((string)($_GET['role'] ?? 'all')),
                'status' => trim((string)($_GET['status'] ?? 'all')),
                'sort_order' => trim((string)($_GET['sort_order'] ?? 'newest'))
            ];

            $result = $this->userService->getUsersPage($page, $limit, $filters);
            
            if ($result['success']) {
                Response::json(
                    true,
                    'Users retrieved successfully',
                    $result['data'],
                    HTTP_OK,
                    [
                        'pagination' => $result['pagination'] ?? null
                    ]
                );
            } else {
                Response::json(false, $result['message'], null, HTTP_INTERNAL_ERROR);
            }
            
        } catch (Exception $e) {
            error_log("Get users controller error: " . $e->getMessage());
            Response::json(false, 'Failed to retrieve users', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getUserDetails()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$currentUser->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $result = $this->userService->getUserById($userId);

            if ($result['success']) {
                Response::json(true, 'User retrieved successfully', $result['data'], HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_NOT_FOUND);
        } catch (Exception $e) {
            error_log("Get user details controller error: " . $e->getMessage());
            Response::json(false, 'Failed to retrieve user details', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function createUser()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$currentUser->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $result = $this->userService->createAdminUser($currentUser, $input);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_CREATED);
            }

            $statusCode = isset($result['errors']) ? HTTP_UNPROCESSABLE_ENTITY : HTTP_BAD_REQUEST;
            $data = isset($result['errors']) ? ['errors' => $result['errors']] : null;
            Response::json(false, $result['message'], $data, $statusCode);
        } catch (Exception $e) {
            error_log("Create user controller error: " . $e->getMessage());
            Response::json(false, 'Failed to create user', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function updateUser()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$currentUser->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $userId = isset($input['id']) ? (int)$input['id'] : 0;
            $result = $this->userService->updateUser($currentUser, $userId, $input);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_OK);
            }

            $statusCode = isset($result['errors']) ? HTTP_UNPROCESSABLE_ENTITY : HTTP_BAD_REQUEST;
            $data = isset($result['errors']) ? ['errors' => $result['errors']] : null;
            Response::json(false, $result['message'], $data, $statusCode);
        } catch (Exception $e) {
            error_log("Update user controller error: " . $e->getMessage());
            Response::json(false, 'Failed to update user', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function updateUserStatus()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$currentUser->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $userId = isset($input['id']) ? (int)$input['id'] : 0;
            $isActive = filter_var($input['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $result = $this->userService->updateUserStatus($currentUser, $userId, $isActive);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            error_log("Update user status controller error: " . $e->getMessage());
            Response::json(false, 'Failed to update user status', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function resetUserPassword()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Unauthorized', null, HTTP_UNAUTHORIZED);
            }

            if (!$currentUser->isAdmin()) {
                Response::json(false, 'Admin access required', null, HTTP_FORBIDDEN);
            }

            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $userId = isset($input['id']) ? (int)$input['id'] : 0;
            $result = $this->userService->resetUserPassword($currentUser, $userId, $input);

            if ($result['success']) {
                Response::json(true, $result['message'], null, HTTP_OK);
            }

            $statusCode = isset($result['errors']) ? HTTP_UNPROCESSABLE_ENTITY : HTTP_BAD_REQUEST;
            $data = isset($result['errors']) ? ['errors' => $result['errors']] : null;
            Response::json(false, $result['message'], $data, $statusCode);
        } catch (Exception $e) {
            error_log("Reset user password controller error: " . $e->getMessage());
            Response::json(false, 'Failed to reset user password', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function updateProfile()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Authentication required', null, HTTP_UNAUTHORIZED);
            }

            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $result = $this->userService->updateCurrentProfile($currentUser, $input);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_OK);
            }

            $statusCode = isset($result['errors']) ? HTTP_UNPROCESSABLE_ENTITY : HTTP_BAD_REQUEST;
            $data = isset($result['errors']) ? ['errors' => $result['errors']] : null;
            Response::json(false, $result['message'], $data, $statusCode);
            
        } catch (Exception $e) {
            error_log("Update profile controller error: " . $e->getMessage());
            Response::json(false, 'Failed to update profile', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function changeCurrentPassword()
    {
        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser) {
                Response::json(false, 'Authentication required', null, HTTP_UNAUTHORIZED);
            }

            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $result = $this->userService->changeCurrentPassword($currentUser, $input);

            if ($result['success']) {
                Response::json(true, $result['message'], null, HTTP_OK);
            }

            $statusCode = isset($result['errors']) ? HTTP_UNPROCESSABLE_ENTITY : HTTP_BAD_REQUEST;
            $data = isset($result['errors']) ? ['errors' => $result['errors']] : null;
            Response::json(false, $result['message'], $data, $statusCode);
        } catch (Exception $e) {
            error_log("Change current password controller error: " . $e->getMessage());
            Response::json(false, 'Failed to change password', null, HTTP_INTERNAL_ERROR);
        }
    }

    private function getCurrentUser()
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? '';

        if (!$header && function_exists('getallheaders')) {
            $headers = getallheaders();
            $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (!preg_match('/Bearer\s+(.+)$/i', $header, $matches)) {
            return null;
        }

        $token = trim($matches[1]);
        $user = $this->authRepository->findUserByToken($token);

        if ($user) {
            return $user;
        }

        return $this->getUserFromLegacyJwt($token);
    }

    private function getUserFromLegacyJwt($token)
    {
        $jwtPath = dirname(__DIR__, 3) . '/api/config/jwt.php';

        if (!file_exists($jwtPath)) {
            return null;
        }

        require_once $jwtPath;

        if (!class_exists('JWT') || !method_exists('JWT', 'decode')) {
            return null;
        }

        $payload = JWT::decode($token);

        if (!$payload || !is_array($payload)) {
            return null;
        }

        if (isset($payload['exp']) && (int)$payload['exp'] < time()) {
            return null;
        }

        $userId = isset($payload['id']) ? (int)$payload['id'] : 0;

        if ($userId <= 0) {
            return null;
        }

        return $this->userRepository->findById($userId);
    }
}
