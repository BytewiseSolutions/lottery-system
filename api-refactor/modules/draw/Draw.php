<?php

class Draw
{
    private const DEFAULT_LOTTERIES = [
        1 => [
            'name' => 'Monday Lotto',
            'code' => 'monday',
            'day' => 'Monday'
        ],
        2 => [
            'name' => 'Wednesday Lotto',
            'code' => 'wednesday',
            'day' => 'Wednesday'
        ],
        3 => [
            'name' => 'Friday Lotto',
            'code' => 'friday',
            'day' => 'Friday'
        ]
    ];

    public $id;
    public $lottery_id;
    public $lottery;
    public $draw_date;
    public $status;
    public $jackpot;
    public $created_at;

    public function __construct($data = [])
    {
        $this->id         = $data['id'] ?? null;
        $this->lottery_id = $data['lottery_id'] ?? null;
        $this->lottery    = $data['lottery'] ?? $data['name'] ?? self::lotteryNameFromId($this->lottery_id);
        $this->draw_date  = $data['draw_date'] ?? null;
        $this->status     = $data['status'] ?? DRAW_SCHEDULED;
        $this->jackpot    = $data['jackpot'] ?? 10.00;
        $this->created_at = $data['created_at'] ?? null;
    }

    public function toArray()
    {
        $lotteryName = $this->getLotteryName();

        return [
            'id'         => $this->id,
            'lottery_id' => $this->lottery_id,
            'lottery'    => $lotteryName,
            'lottery_type' => $lotteryName,
            'name'       => $lotteryName,
            'code'       => $this->getLotteryCode(),
            'draw_date'  => $this->draw_date,
            'drawDate'   => $this->draw_date,
            'nextDraw'   => $this->draw_date,
            'status'     => $this->status,
            'jackpot'    => $this->jackpot,
            'created_at' => $this->created_at,
            'voting_closes_at' => $this->getVotingClosesAt(),
            'is_voting_open' => $this->isVotingOpen()
        ];
    }

    public function isScheduled()
    {
        return $this->status === DRAW_SCHEDULED;
    }

    public function isClosed()
    {
        return $this->status === DRAW_CLOSED;
    }

    public function isCompleted()
    {
        return $this->status === DRAW_COMPLETED;
    }

    public function isCancelled()
    {
        return $this->status === DRAW_CANCELLED;
    }

    public function getLotteryName()
    {
        return $this->lottery ?: self::lotteryNameFromId($this->lottery_id);
    }

    public function getLotteryCode()
    {
        $lottery = self::DEFAULT_LOTTERIES[$this->lottery_id] ?? null;

        return $lottery['code'] ?? null;
    }

    public function getVotingClosesAt()
    {
        if (!$this->draw_date) {
            return null;
        }

        $closeTime = new DateTime($this->draw_date);
        $closeTime->setTime(19, 59, 59);

        return $closeTime->format('Y-m-d H:i:s');
    }

    public function isVotingOpen(?DateTime $now = null)
    {
        if (!$this->isScheduled() || !$this->draw_date) {
            return false;
        }

        $now = $now ?: new DateTime();
        $closeTime = new DateTime($this->getVotingClosesAt());

        return $now <= $closeTime;
    }

    public static function getDefaultLotteries()
    {
        return self::DEFAULT_LOTTERIES;
    }

    public static function lotteryNameFromId($lotteryId)
    {
        $lottery = self::DEFAULT_LOTTERIES[$lotteryId] ?? null;

        return $lottery['name'] ?? null;
    }

    public static function lotteryDayFromId($lotteryId)
    {
        $lottery = self::DEFAULT_LOTTERIES[$lotteryId] ?? null;

        return $lottery['day'] ?? null;
    }
}
