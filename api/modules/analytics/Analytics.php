<?php

class Analytics
{
    public $winnersLastMonth;
    public $totalEntries;
    public $totalPayouts;
    public $totalUsers;

    public function __construct($data = [])
    {
        $this->winnersLastMonth = (int)($data['winnersLastMonth'] ?? 0);
        $this->totalEntries = (int)($data['totalEntries'] ?? 0);
        $this->totalPayouts = (float)($data['totalPayouts'] ?? 0);
        $this->totalUsers = (int)($data['totalUsers'] ?? 0);
    }

    public function toArray()
    {
        return [
            'winnersLastMonth' => $this->winnersLastMonth,
            'totalEntries' => $this->totalEntries,
            'totalPayouts' => $this->totalPayouts,
            'totalUsers' => $this->totalUsers
        ];
    }
}
