<?php

class Winner
{
    public $id;
    public $user_id;
    public $result_id;
    public $entry_id;
    public $prize_amount;
    public $claim_status;
    public $payment_status;
    public $created_at;
    public $name;
    public $email;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->result_id = $data['result_id'] ?? null;
        $this->entry_id = $data['entry_id'] ?? null;
        $this->prize_amount = $data['prize_amount'] ?? 0;
        $this->claim_status = $data['claim_status'] ?? CLAIM_PENDING;
        $this->payment_status = $data['payment_status'] ?? PAYMENT_PENDING;
        $this->created_at = $data['created_at'] ?? null;
        $this->name = $data['name'] ?? trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        $this->email = $data['email'] ?? null;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'result_id' => $this->result_id,
            'entry_id' => $this->entry_id,
            'prize_amount' => $this->prize_amount,
            'claim_status' => $this->claim_status,
            'payment_status' => $this->payment_status,
            'created_at' => $this->created_at,
            'name' => $this->name,
            'email' => $this->email
        ];
    }
}
