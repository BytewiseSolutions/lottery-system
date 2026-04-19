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
            $this->generateUpcomingDraws();

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
            $this->generateUpcomingDraws();

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

    private function generateUpcomingDraws()
    {
        $today = new DateTime();
        $targets = Draw::getDefaultLotteries();

        foreach ($targets as $lotteryId => $lottery) {
            $day = $lottery['day'];

            $date = $this->nextDay($day, clone $today);

            $exists = $this->drawRepository->exists($lotteryId, $date);

            if (!$exists) {

                $draw = new Draw([
                    'lottery_id' => $lotteryId,
                    'draw_date'  => $date . ' 20:00:00',
                    'status'     => DRAW_SCHEDULED,
                    'jackpot'    => $this->calculateJackpot($lotteryId)
                ]);

                $this->drawRepository->create($draw);

                Logger::info('Auto draw created', [
                    'lottery_id' => $lotteryId,
                    'draw_date' => $date
                ]);
            }
        }
    }

    private function nextDay($dayName, DateTime $date)
    {
        if ($date->format('l') === $dayName) {
            return $date->format('Y-m-d');
        }

        $date->modify('next ' . $dayName);

        return $date->format('Y-m-d');
    }

    private function calculateJackpot($lotteryId)
    {
        $votes = $this->drawRepository->countVotesByLottery($lotteryId);

        return number_format($this->defaultJackpot + ($votes * 0.01), 2, '.', '');
    }
}
