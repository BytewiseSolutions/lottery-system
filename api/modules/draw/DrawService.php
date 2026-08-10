<?php

class DrawService
{
    private $drawRepository;
    private $defaultJackpot = 10.00;

    public function __construct()
    {
        $this->drawRepository = new DrawRepository();
    }

    public function getCurrentDraw()
    {
        try {
            $this->drawRepository->ensureDefaultLotteries();
            $this->ensureRecentAndUpcomingDraws();

            $draw = $this->drawRepository->getNextUpcomingDraw();

            if (!$draw) {
                return [
                    'success' => false,
                    'message' => 'No upcoming draw found'
                ];
            }

            return [
                'success' => true,
                'data' => $draw->toArray()
            ];

        } catch (Exception $e) {
            Logger::error('Get current draw failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load draw'
            ];
        }
    }

    public function getUpcomingDraws()
    {
        try {
            $this->drawRepository->ensureDefaultLotteries();
            $this->ensureRecentAndUpcomingDraws();

            $draws = $this->drawRepository->getUpcomingDraws();

            $data = [];

            foreach ($draws as $draw) {
                $data[] = $draw->toArray();
            }

            return [
                'success' => true,
                'data' => $data
            ];

        } catch (Exception $e) {
            Logger::error('Get upcoming draws failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load draws'
            ];
        }
    }

    public function getPastDrawsWithoutResults()
    {
        try {
            $this->drawRepository->ensureDefaultLotteries();
            $this->ensureRecentAndUpcomingDraws();

            $draws = $this->drawRepository->getPastDrawsWithoutResults();

            return [
                'success' => true,
                'data' => array_map(function ($draw) {
                    return $draw->toArray();
                }, $draws)
            ];

        } catch (Exception $e) {
            Logger::error('Get past draws without results failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load past draws'
            ];
        }
    }

    private function ensureRecentAndUpcomingDraws()
    {
        $today = new DateTime();
        $targets = Draw::getDefaultLotteries();

        foreach ($targets as $lotteryId => $lottery) {
            $day = $lottery['day'];

            $previousDate = $this->previousDay($day, clone $today);
            $nextDate = $this->nextDay($day, clone $today);

            $this->createDrawIfMissing($lotteryId, $previousDate);
            $this->createDrawIfMissing($lotteryId, $nextDate);
        }
    }

    private function createDrawIfMissing($lotteryId, $date)
    {
        $exists = $this->drawRepository->exists($lotteryId, $date);

        if ($exists) {
            return;
        }

        $draw = new Draw([
            'lottery_id' => $lotteryId,
            'draw_date'  => $date . ' 20:00:00',
            'status'     => DRAW_SCHEDULED,
            'jackpot'    => $this->defaultJackpot
        ]);

        $this->drawRepository->create($draw);

        Logger::info('Auto draw created', [
            'lottery_id' => $lotteryId,
            'draw_date' => $date
        ]);
    }

    private function nextDay($dayName, DateTime $date)
    {
        if ($date->format('l') === $dayName) {
            $drawTime = clone $date;
            $drawTime->setTime(20, 0, 0);
            if (new DateTime() < $drawTime) {
                return $date->format('Y-m-d');
            }
        }

        $date->modify('next ' . $dayName);

        return $date->format('Y-m-d');
    }

    private function previousDay($dayName, DateTime $date)
    {
        if ($date->format('l') === $dayName) {
            return $date->format('Y-m-d');
        }

        $date->modify('last ' . $dayName);

        return $date->format('Y-m-d');
    }

}
