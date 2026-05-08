<?php

class DrawDto
{
    public $lottery_id;
    public $draw_date;
    public $status;
    public $jackpot;

    public function __construct($data = [])
    {
        $this->lottery_id = $data['lottery_id'] ?? null;
        $this->draw_date  = $data['draw_date'] ?? null;
        $this->status     = $data['status'] ?? 'scheduled';
        $this->jackpot    = $data['jackpot'] ?? 10.00;
    }

    public function toDraw()
    {
        return new Draw([
            'lottery_id' => $this->lottery_id,
            'draw_date'  => $this->draw_date,
            'status'     => $this->status,
            'jackpot'    => $this->jackpot
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
            'lottery_id' => $this->lottery_id,
            'draw_date'  => $this->draw_date,
            'status'     => $this->status,
            'jackpot'    => $this->jackpot
        ];
    }
}