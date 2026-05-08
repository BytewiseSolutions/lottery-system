<?php

class Payment
{
    public $id;
    public $winner_id;
    public $user_id;
    public $amount;
    public $payment_method;
    public $transaction_id;
    public $status;
    public $approved_by;
    public $approved_at;
    public $created_at;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->winner_id = $data['winner_id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->amount = $data['amount'] ?? 0;
        $this->payment_method = $data['payment_method'] ?? null;
        $this->transaction_id = $data['transaction_id'] ?? null;
        $this->status = $data['status'] ?? PAYMENT_COMPLETED;
        $this->approved_by = $data['approved_by'] ?? null;
        $this->approved_at = $data['approved_at'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'winner_id' => $this->winner_id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->transaction_id,
            'status' => $this->status,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at
        ];
    }
}
