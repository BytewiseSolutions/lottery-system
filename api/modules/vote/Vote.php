<?php

class Vote
{
    public $id;
    public $user_id;
    public $lottery;
    public $draw_id;
    public $numbers;
    public $bonus_numbers;
    public $source;
    public $vote_date;
    public $allocated_votes;
    public $total_votes;
    public $created_at;
    public $draw_date;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->lottery = $data['lottery'] ?? null;
        $this->draw_id = $data['draw_id'] ?? null;
        $this->numbers = $this->normalizeArray($data['numbers'] ?? []);
        $this->bonus_numbers = $this->normalizeArray($data['bonus_numbers'] ?? []);
        $this->source = $data['source'] ?? VOTE_SOURCE_USER;
        $this->vote_date = $data['vote_date'] ?? date('Y-m-d');
        $this->allocated_votes = (int)($data['allocated_votes'] ?? 1);
        $this->total_votes = (int)($data['total_votes'] ?? 1);
        $this->created_at = $data['created_at'] ?? null;
        $this->draw_date = $data['draw_date'] ?? null;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'lottery' => $this->lottery,
            'draw_id' => $this->draw_id,
            'numbers' => $this->numbers,
            'bonusNumbers' => $this->bonus_numbers,
            'source' => $this->source,
            'voteDate' => $this->vote_date,
            'drawDate' => $this->draw_date,
            'allocated_votes' => $this->allocated_votes,
            'total_votes' => $this->total_votes,
            'createdAt' => $this->created_at,
            'created_at' => $this->created_at
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
