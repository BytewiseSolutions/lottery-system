<?php

class PaymentService
{
    private $paymentRepository;
    private $activityLogService;

    public function __construct()
    {
        $this->paymentRepository = new PaymentRepository();
        $this->activityLogService = new ActivityLogService();
    }

    public function processPayment(PaymentDto $paymentDto, $currentUser = null)
    {
        if (!$paymentDto->winner_id) {
            return [
                'success' => false,
                'message' => 'Winner is required'
            ];
        }

        if ($paymentDto->amount === null || $paymentDto->amount <= 0) {
            return [
                'success' => false,
                'message' => 'Valid payment amount is required'
            ];
        }

        if (empty($paymentDto->payment_method)) {
            return [
                'success' => false,
                'message' => 'Payment method is required'
            ];
        }

        try {
            $winner = $this->paymentRepository->getWinnerById($paymentDto->winner_id);

            if (!$winner) {
                return [
                    'success' => false,
                    'message' => 'Winner not found'
                ];
            }

            if (($winner['payment_status'] ?? null) === PAYMENT_PAID) {
                return [
                    'success' => false,
                    'message' => 'This winner has already been paid'
                ];
            }

            if (($winner['claim_status'] ?? null) !== CLAIM_CLAIMED) {
                return [
                    'success' => false,
                    'message' => 'Winner must be marked as claimed before payment'
                ];
            }

            $this->paymentRepository->beginTransaction();

            $payment = new Payment([
                'winner_id' => $winner['id'],
                'user_id' => $winner['user_id'],
                'amount' => $paymentDto->amount,
                'payment_method' => $paymentDto->payment_method,
                'transaction_id' => $paymentDto->transaction_id,
                'status' => PAYMENT_COMPLETED,
                'approved_at' => date('Y-m-d H:i:s')
            ]);

            $createdPayment = $this->paymentRepository->create($payment);
            $this->paymentRepository->updateWinnerPaymentStatus($winner['id'], PAYMENT_PAID);

            $this->paymentRepository->commit();

            if ($currentUser) {
                $this->activityLogService->log(
                    $currentUser->id,
                    ACTION_PAYMENT_PROCESS,
                    'Processed winner payment of M' . number_format((float)$paymentDto->amount, 2, '.', '') . ' for winner ID ' . $winner['id']
                );
            }

            return [
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => $createdPayment->toArray()
            ];

        } catch (Exception $e) {
            $this->paymentRepository->rollBack();

            Logger::error('Process payment failed', [
                'error' => $e->getMessage(),
                'winner_id' => $paymentDto->winner_id
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process payment'
            ];
        }
    }
}
