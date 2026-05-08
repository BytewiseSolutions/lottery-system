<?php

class PaymentDto
{
    public $winner_id;
    public $amount;
    public $payment_method;
    public $transaction_id;

    public function __construct($data = [])
    {
        $this->winner_id = isset($data['winner_id']) ? (int)$data['winner_id'] : null;
        $this->amount = isset($data['amount']) ? (float)$data['amount'] : null;
        $this->payment_method = $data['payment_method'] ?? null;
        $this->transaction_id = $data['transaction_id'] ?? null;
    }

    public static function fromRequest()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        return new self($data ?? []);
    }
}
