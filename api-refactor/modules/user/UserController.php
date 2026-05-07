<?php

class UserController
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
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
            // Get user ID from JWT token or session
            $userId = $this->getCurrentUserId();
            
            if (!$userId) {
                Response::json(false, 'Authentication required', null, HTTP_UNAUTHORIZED);
            }

            $result = $this->userService->getUserById($userId);
            
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

    private function getCurrentUserId()
    {
        // This should extract user ID from JWT token
        // For now, return a default admin user ID
        // You'll need to implement proper JWT token parsing
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (strpos($authHeader, 'Bearer ') === 0) {
            $token = substr($authHeader, 7);
            // TODO: Decode JWT token and extract user ID
            // For now, return admin user ID from database
            try {
                $pdo = Connection::get();
                $stmt = $pdo->prepare('SELECT id FROM user WHERE role = ? LIMIT 1');
                $stmt->execute([ROLE_ADMIN]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result ? $result['id'] : null;
            } catch (Exception $e) {
                return null;
            }
        }
        
        return null;
    }

    public function getUsers()
    {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? DEFAULT_PAGE_SIZE;
            
            $result = $this->userService->getAllUsers($page, $limit);
            
            if ($result['success']) {
                Response::json(true, 'Users retrieved successfully', $result['data'], HTTP_OK, $result['pagination'] ?? null);
            } else {
                Response::json(false, $result['message'], null, HTTP_INTERNAL_ERROR);
            }
            
        } catch (Exception $e) {
            error_log("Get users controller error: " . $e->getMessage());
            Response::json(false, 'Failed to retrieve users', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function updateProfile()
    {
        try {
            Response::json(false, 'Update profile not implemented yet', null, HTTP_NOT_FOUND);
            
        } catch (Exception $e) {
            error_log("Update profile controller error: " . $e->getMessage());
            Response::json(false, 'Failed to update profile', null, HTTP_INTERNAL_ERROR);
        }
    }
}