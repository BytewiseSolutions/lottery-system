<?php

class Result
{
    public $id;
    public $draw_id;
    public $lottery;
    public $draw_date;
    public $winning_numbers;
    public $bonus_numbers;
    public $jackpot;
    public $winners_count;
    public $status;
    public $notes;
    public $created_at;
    public $updated_at;
    public $total_entries;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->draw_id = $data['draw_id'] ?? null;
        $this->lottery = $data['lottery'] ?? null;
        $this->draw_date = $data['draw_date'] ?? null;
        $this->winning_numbers = $this->normalizeArray($data['winning_numbers'] ?? $data['numbers'] ?? []);
        $this->bonus_numbers = $this->normalizeArray($data['bonus_numbers'] ?? $data['bonusNumbers'] ?? []);
        $this->jackpot = $data['jackpot'] ?? null;
        $this->winners_count = (int)($data['winners_count'] ?? 0);
        $this->status = $data['status'] ?? 'published';
        $this->notes = $data['notes'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        $this->total_entries = (int)($data['total_entries'] ?? 0);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'draw_id' => $this->draw_id,
            'lottery' => $this->lottery,
            'draw_date' => $this->draw_date,
            'drawDate' => $this->draw_date,
            'winning_numbers' => $this->winning_numbers,
            'numbers' => $this->winning_numbers,
            'bonus_numbers' => $this->bonus_numbers,
            'bonusNumbers' => $this->bonus_numbers,
            'jackpot' => $this->jackpot,
            'winners_count' => $this->winners_count,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'total_entries' => $this->total_entries
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
