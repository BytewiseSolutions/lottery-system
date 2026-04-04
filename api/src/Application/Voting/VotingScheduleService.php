<?php
declare(strict_types=1);

namespace App\Application\Voting;

use App\Domain\Voting\UpcomingDrawRepository;
use DateTimeImmutable;
use DateTimeZone;

final class VotingScheduleService
{
    private DateTimeZone $timezone;

    public function __construct(private readonly UpcomingDrawRepository $draws)
    {
        $this->timezone = new DateTimeZone('Africa/Johannesburg');
    }

    public function currentVotingDraw(): array
    {
        $now = new DateTimeImmutable('now');
        $scheduledDraws = $this->draws->scheduledDraws();
        $currentVotingDraw = null;

        foreach ($scheduledDraws as $draw) {
            $drawDateTime = new DateTimeImmutable($draw['draw_date']);
            $votingCloseTime = $drawDateTime->setTime(19, 59, 59);

            if ($now <= $votingCloseTime) {
                $currentVotingDraw = $this->mapVotingDraw($draw, $votingCloseTime, true);
                break;
            }
        }

        if ($currentVotingDraw === null && !empty($scheduledDraws)) {
            $draw = $scheduledDraws[0];
            $drawDateTime = new DateTimeImmutable($draw['draw_date']);
            $votingCloseTime = $drawDateTime->setTime(19, 59, 59);
            $currentVotingDraw = $this->mapVotingDraw($draw, $votingCloseTime, $now <= $votingCloseTime);
        }

        if ($currentVotingDraw === null) {
            return [
                'success' => false,
                'message' => 'No voting draws available',
                'server_time' => $now->format('Y-m-d H:i:s'),
            ];
        }

        return [
            'success' => true,
            'current_voting_draw' => $currentVotingDraw,
            'server_time' => $now->format('Y-m-d H:i:s'),
        ];
    }

    public function countdown(): array
    {
        $now = new DateTimeImmutable('now', $this->timezone);
        $draw = $this->draws->nextScheduledDraw();

        if ($draw === null) {
            return [
                'success' => false,
                'error' => 'No upcoming draws found',
            ];
        }

        $drawDateTime = new DateTimeImmutable($draw['draw_date'], $this->timezone);
        $votingCloseTime = $drawDateTime->setTime(19, 59, 0);
        $totalSeconds = max(0, $votingCloseTime->getTimestamp() - $now->getTimestamp());

        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;

        return [
            'success' => true,
            'countdown' => sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds),
            'totalSeconds' => $totalSeconds,
            'votingCloseTime' => $votingCloseTime->format('Y-m-d H:i:s'),
            'currentTime' => $now->format('Y-m-d H:i:s'),
            'drawDate' => $drawDateTime->format('Y-m-d H:i:s'),
            'isVotingOpen' => $totalSeconds > 0,
        ];
    }

    private function mapVotingDraw(array $draw, DateTimeImmutable $votingCloseTime, bool $isVotingOpen): array
    {
        return [
            'id' => (int) $draw['id'],
            'lottery' => $draw['lottery'],
            'lottery_type' => $draw['lottery'],
            'draw_date' => $draw['draw_date'],
            'jackpot' => $draw['jackpot'],
            'status' => $draw['status'],
            'voting_closes_at' => $votingCloseTime->format('Y-m-d H:i:s'),
            'is_voting_open' => $isVotingOpen,
        ];
    }
}
