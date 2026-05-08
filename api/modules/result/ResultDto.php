<?php

class ResultDto
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

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->draw_id = $data['draw_id'] ?? $data['drawId'] ?? null;
        $this->lottery = $data['lottery'] ?? null;
        $this->draw_date = $data['draw_date'] ?? $data['drawDate'] ?? null;
        $this->winning_numbers = $this->normalizeArray($data['winning_numbers'] ?? $data['numbers'] ?? []);
        $this->bonus_numbers = $this->normalizeArray($data['bonus_numbers'] ?? $data['bonusNumbers'] ?? []);
        $this->jackpot = $data['jackpot'] ?? null;
        $this->winners_count = (int)($data['winners_count'] ?? $data['winnersCount'] ?? 0);
        $this->status = $data['status'] ?? 'published';
        $this->notes = $data['notes'] ?? null;
    }

    public function toResult()
    {
        return new Result([
            'id' => $this->id,
            'draw_id' => $this->draw_id,
            'lottery' => $this->lottery,
            'draw_date' => $this->draw_date,
            'winning_numbers' => $this->winning_numbers,
            'bonus_numbers' => $this->bonus_numbers,
            'jackpot' => $this->jackpot,
            'winners_count' => $this->winners_count,
            'status' => $this->status,
            'notes' => $this->notes
        ]);
    }

    public static function fromRequest()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        return new self($data ?? []);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'draw_id' => $this->draw_id,
            'drawId' => $this->draw_id,
            'lottery' => $this->lottery,
            'draw_date' => $this->draw_date,
            'drawDate' => $this->draw_date,
            'winning_numbers' => $this->winning_numbers,
            'numbers' => $this->winning_numbers,
            'bonus_numbers' => $this->bonus_numbers,
            'bonusNumbers' => $this->bonus_numbers,
            'jackpot' => $this->jackpot,
            'winners_count' => $this->winners_count,
            'winnersCount' => $this->winners_count,
            'status' => $this->status,
            'notes' => $this->notes
        ];
    }

    private function normalizeArray($value)
    {
        if (is_array($value)) {
            return array_values(array_map('intval', $value));
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                return array_values(array_map('intval', $decoded));
            }
        }

        return [];
    }
}
