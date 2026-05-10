<?php

class Entry
{
    public $id;
    public $user_id;
    public $draw_id;
    public $lottery;
    public $numbers;
    public $bonus_numbers;
    public $draw_date;
    public $created_at;
    public $user_name;
    public $user_email;
    public $user_phone;
    public $draw_datetime;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->draw_id = $data['draw_id'] ?? null;
        $this->lottery = $data['lottery'] ?? null;
        $this->numbers = $this->normalizeArray($data['numbers'] ?? []);
        $this->bonus_numbers = $this->normalizeArray($data['bonus_numbers'] ?? []);
        $this->draw_date = $data['draw_date'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->user_name = $data['user_name'] ?? null;
        $this->user_email = $data['user_email'] ?? null;
        $this->user_phone = $data['user_phone'] ?? null;
        $this->draw_datetime = $data['draw_datetime'] ?? null;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'draw_id' => $this->draw_id,
            'lottery' => $this->lottery,
            'numbers' => $this->numbers,
            'bonus_numbers' => $this->bonus_numbers,
            'draw_date' => $this->draw_date,
            'created_at' => $this->created_at,
            'user_name' => $this->user_name,
            'user_email' => $this->user_email,
            'user_phone' => $this->user_phone,
            'draw_datetime' => $this->draw_datetime
        ];
    }

    private function normalizeArray($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
