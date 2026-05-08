<?php

class PaymentController
{
    private $paymentService;
    private $authRepository;
    private $userRepository;

    public function __construct()
    {
        $this->paymentService = new PaymentService();
        $this->authRepository = new AuthRepository();
        $this->userRepository = new UserRepository();
    }

    public function processPayment()
    {
        try {
            $currentUser = $this->getCurrentUser();
            $paymentDto = PaymentDto::fromRequest();
            $result = $this->paymentService->processPayment($paymentDto, $currentUser);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_OK);
            }

            Response::json(false, $result['message'], null, HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            Logger::error('PaymentController process payment error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to process payment', null, HTTP_INTERNAL_ERROR);
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
