<?php

class VoteDto
{
    public $lottery;
    public $numbers;
    public $bonusNumbers;
    public $drawDate;
    public $humanVerified;

    public function __construct($data = [])
    {
        $this->lottery = $data['lottery'] ?? null;
        $this->numbers = is_array($data['numbers'] ?? null) ? $data['numbers'] : [];
        $this->bonusNumbers = is_array($data['bonusNumbers'] ?? null) ? $data['bonusNumbers'] : [];
        $this->drawDate = $data['drawDate'] ?? null;
        $this->humanVerified = (bool)($data['humanVerified'] ?? false);
    }

    public static function fromRequest()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        return new self($data ?? []);
    }

    public function toArray()
    {
        return [
            'lottery' => $this->lottery,
            'numbers' => $this->numbers,
            'bonusNumbers' => $this->bonusNumbers,
            'drawDate' => $this->drawDate,
            'humanVerified' => $this->humanVerified
        ];
    }
}
