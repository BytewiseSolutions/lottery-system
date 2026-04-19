<?php

class AuthController
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                Response::json(false, 'Invalid JSON input', null, HTTP_BAD_REQUEST);
            }

            $result = $this->authService->login($input);

            if ($result['success']) {
                Response::json(true, $result['message'], $result, HTTP_OK);
            } else {
                Response::json(false, $result['message'], $result, HTTP_UNAUTHORIZED);
            }

        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            Response::json(false, 'Login failed', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function register()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                Response::json(false, 'Invalid JSON input', null, HTTP_BAD_REQUEST);
            }

            $result = $this->authService->register($input);

            if ($result['success']) {
                Response::json(true, $result['message'], $result, HTTP_CREATED);
            } else {
                Response::json(false, $result['message'], $result, HTTP_BAD_REQUEST);
            }

        } catch (Exception $e) {
            error_log("Register error: " . $e->getMessage());
            Response::json(false, 'Registration failed', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function logout()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $token = $input['token'] ?? null;

            $result = $this->authService->logout($token);

            Response::json(true, $result['message'], null, HTTP_OK);

        } catch (Exception $e) {
            error_log("Logout error: " . $e->getMessage());
            Response::json(false, 'Logout failed', null, HTTP_INTERNAL_ERROR);
        }
    }
public function forgotPassword()
{
    try {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            Response::json(false, 'Invalid JSON input', null, HTTP_BAD_REQUEST);
            return;
        }

        if (empty($input['email'])) {
            Response::json(false, 'Email is required', null, HTTP_BAD_REQUEST);
            return;
        }

        $result = $this->authService->forgotPassword($input);

        if ($result['success']) {
            Response::json(true, $result['message'], $result, HTTP_OK);
        } else {
            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);
        }

    } catch (Exception $e) {
        error_log("Forgot password error: " . $e->getMessage());
        Response::json(false, 'Request failed', null, HTTP_INTERNAL_ERROR);
    }
}
public function resetPassword()
{
    try {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            Response::json(false, 'Invalid JSON input', null, HTTP_BAD_REQUEST);
            return;
        }

        $required = ['token', 'password', 'confirm_password'];

        foreach ($required as $field) {
            if (empty($input[$field])) {
                Response::json(false, "$field is required", null, HTTP_BAD_REQUEST);
                return;
            }
        }

        $result = $this->authService->resetPassword($input);

        if ($result['success']) {
            Response::json(true, $result['message'], null, HTTP_OK);
        } else {
            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);
        }

    } catch (Exception $e) {
        error_log("Reset password error: " . $e->getMessage());
        Response::json(false, 'Request failed', null, HTTP_INTERNAL_ERROR);
    }
}

}