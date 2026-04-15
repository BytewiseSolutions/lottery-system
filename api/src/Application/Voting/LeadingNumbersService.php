<?php
declare(strict_types=1);

namespace App\Application\Voting;

use App\Core\ApiException;
use App\Domain\Voting\AdminVoteRepository;
use App\Domain\Voting\LeadingNumbersSnapshotRepository;
use App\Domain\Voting\VoteRepository;

final class LeadingNumbersService
{
    public function __construct(
        private readonly VoteRepository $votes,
        private readonly AdminVoteRepository $adminVotes,
        private readonly ?LeadingNumbersSnapshotRepository $snapshots = null
    ) {}

    public function summary(string $lottery, ?string $drawDate = null): array
    {
        $lottery = trim($lottery);
        if ($lottery === '') {
            throw new ApiException('Lottery parameter required', 400);
        }

        $drawDate = $this->resolveDrawDate($lottery, $drawDate);

        // Check if we have a recent snapshot (less than 1 minute old)
        if ($this->snapshots !== null) {
            $snapshot = $this->snapshots->find($lottery, $drawDate);
            if ($snapshot !== null && isset($snapshot['updated_at'])) {
                $snapshotTime = strtotime($snapshot['updated_at']);
                $currentTime = time();
                
                // If snapshot is less than 60 seconds old, use it
                if (($currentTime - $snapshotTime) < 60) {
                    return $snapshot;
                }
            }
        }

        // Refresh snapshot with latest data
        return $this->refreshSnapshot($lottery, $drawDate);
    }

    public function refreshSnapshot(string $lottery, string $drawDate): array
    {
        $lottery = trim($lottery);
        if ($lottery === '') {
            throw new ApiException('Lottery parameter required', 400);
        }

        $drawDate = substr(trim($drawDate), 0, 10);
        if ($drawDate === '') {
            $drawDate = date('Y-m-d');
        }

        $numberCounts = [];
        $bonusCounts = [];
        $userVotes = $this->votes->forLotteryAndDrawDate($lottery, $drawDate);
        $adminVotes = $this->adminVotes->forLotteryAndDrawDate($lottery, $drawDate);

        // Check if we have any votes at all
        if (empty($userVotes) && empty($adminVotes)) {
            // Return empty data structure instead of creating a snapshot
            return [
                'lottery' => $lottery,
                'draw_date' => $drawDate,
                'section1' => [],
                'section2' => [],
                'topSection1' => [],
                'topSection2' => [],
                'top_five' => [],
                'top_two' => [],
                'total_user_votes' => 0,
                'total_admin_allocations' => 0,
                'total_main_votes' => 0,
                'total_bonus_votes' => 0,
                'from_snapshot' => false,
            ];
        }

        foreach ($userVotes as $entry) {
            foreach (json_decode($entry['numbers'], true) ?: [] as $number) {
                $numberCounts[(int) $number] = ($numberCounts[(int) $number] ?? 0) + 1;
            }

            foreach (json_decode($entry['bonus_numbers'], true) ?: [] as $number) {
                $bonusCounts[(int) $number] = ($bonusCounts[(int) $number] ?? 0) + 1;
            }
        }

        foreach ($adminVotes as $entry) {
            $mainNumbers = json_decode($entry['numbers'], true) ?: [];
            $bonusNumbers = json_decode($entry['bonus_numbers'], true) ?: [];
            $votingData = $entry['voting_data'] ? json_decode($entry['voting_data'], true) : null;

            if (
                is_array($votingData)
                && isset($votingData['mainNumberVotes'], $votingData['bonusNumberVotes'])
            ) {
                foreach ($votingData['mainNumberVotes'] as $number => $votes) {
                    $numberCounts[(int) $number] = ($numberCounts[(int) $number] ?? 0) + (int) $votes;
                }

                foreach ($votingData['bonusNumberVotes'] as $number => $votes) {
                    $bonusCounts[(int) $number] = ($bonusCounts[(int) $number] ?? 0) + (int) $votes;
                }

                continue;
            }

            $allocatedVotes = (int) ($entry['allocated_votes'] ?? 0);
            $totalNumbers = count($mainNumbers) + count($bonusNumbers);
            $votesPerNumber = $totalNumbers > 0 ? (int) floor($allocatedVotes / $totalNumbers) : 0;

            foreach ($mainNumbers as $number) {
                $numberCounts[(int) $number] = ($numberCounts[(int) $number] ?? 0) + $votesPerNumber;
            }

            foreach ($bonusNumbers as $number) {
                $bonusCounts[(int) $number] = ($bonusCounts[(int) $number] ?? 0) + $votesPerNumber;
            }
        }

        // Only proceed if we have actual vote data
        if (empty($numberCounts) && empty($bonusCounts)) {
            return [
                'lottery' => $lottery,
                'draw_date' => $drawDate,
                'section1' => [],
                'section2' => [],
                'topSection1' => [],
                'topSection2' => [],
                'top_five' => [],
                'top_two' => [],
                'total_user_votes' => count($userVotes),
                'total_admin_allocations' => count($adminVotes),
                'total_main_votes' => 0,
                'total_bonus_votes' => 0,
                'from_snapshot' => false,
            ];
        }

        $numberCounts = $this->sortCounts($numberCounts);
        $bonusCounts = $this->sortCounts($bonusCounts);

        // Limit to top 5 main numbers and top 2 bonus numbers
        $topMainNumbers = array_slice($numberCounts, 0, 5, true);
        $topBonusNumbers = array_slice($bonusCounts, 0, 2, true);

        $topSection1 = array_slice(array_keys($topMainNumbers), 0, 5);
        $topSection2 = array_slice(array_keys($topBonusNumbers), 0, 2);
        sort($topSection1);
        sort($topSection2);

        $summary = [
            'lottery' => $lottery,
            'draw_date' => $drawDate,
            'section1' => $this->mapCounts($topMainNumbers),
            'section2' => $this->mapCounts($topBonusNumbers),
            'topSection1' => array_map('intval', $topSection1),
            'topSection2' => array_map('intval', $topSection2),
            'top_five' => array_map('intval', $topSection1),
            'top_two' => array_map('intval', $topSection2),
            'total_user_votes' => count($userVotes),
            'total_admin_allocations' => count($adminVotes),
            'total_main_votes' => array_sum($topMainNumbers),
            'total_bonus_votes' => array_sum($topBonusNumbers),
            'from_snapshot' => false,
        ];

        // Only save snapshot if we have actual data
        if ($this->snapshots !== null && (array_sum($topMainNumbers) > 0 || array_sum($topBonusNumbers) > 0)) {
            $this->snapshots->upsert($summary);
            $stored = $this->snapshots->find($lottery, $drawDate);
            if ($stored !== null) {
                return $stored;
            }
        }

        return $summary;
    }

    private function mapCounts(array $counts): array
    {
        $items = [];

        foreach ($counts as $number => $votes) {
            $items[] = [
                'number' => (int) $number,
                'votes' => (int) $votes,
            ];
        }

        return $items;
    }

    private function resolveDrawDate(string $lottery, ?string $drawDate): string
    {
        $normalized = substr(trim((string) $drawDate), 0, 10);
        if ($normalized !== '') {
            return $normalized;
        }

        if ($this->snapshots !== null) {
            $latestSnapshot = $this->snapshots->latestForLottery($lottery);
            if ($latestSnapshot !== null) {
                return $latestSnapshot['draw_date'];
            }
        }

        // If no snapshot exists, find the next upcoming draw date for this lottery
        $today = date('Y-m-d');
        $dayOfWeek = date('N'); // 1 = Monday, 7 = Sunday
        
        switch (strtolower($lottery)) {
            case 'monday lotto':
                $targetDay = 1; // Monday
                break;
            case 'wednesday lotto':
                $targetDay = 3; // Wednesday
                break;
            case 'friday lotto':
                $targetDay = 5; // Friday
                break;
            default:
                return $today;
        }
        
        // Calculate days until next target day
        $daysUntilTarget = ($targetDay - $dayOfWeek + 7) % 7;
        if ($daysUntilTarget === 0) {
            $daysUntilTarget = 7; // If today is the target day, get next week's
        }
        
        return date('Y-m-d', strtotime("+{$daysUntilTarget} days"));
    }

    private function sortCounts(array $counts): array
    {
        uksort($counts, static function (int|string $left, int|string $right) use ($counts): int {
            $leftVotes = (int) ($counts[$left] ?? 0);
            $rightVotes = (int) ($counts[$right] ?? 0);

            if ($leftVotes === $rightVotes) {
                return (int) $left <=> (int) $right;
            }

            return $rightVotes <=> $leftVotes;
        });

        return $counts;
    }
}
