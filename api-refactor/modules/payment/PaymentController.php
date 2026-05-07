<?php

class PaymentController
{
    private $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService();
    }

    public function processPayment()
    {
        try {
            $paymentDto = PaymentDto::fromRequest();
            $result = $this->paymentService->processPayment($paymentDto);

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
}
