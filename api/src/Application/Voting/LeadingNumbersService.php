<?php
declare(strict_types=1);

namespace App\Application\Voting;

use App\Core\ApiException;
use App\Domain\Voting\AdminVoteRepository;
use App\Domain\Voting\VoteRepository;

final class LeadingNumbersService
{
    public function __construct(
        private readonly VoteRepository $votes,
        private readonly AdminVoteRepository $adminVotes
    ) {}

    public function summary(string $lottery, string $drawDate): array
    {
        $lottery = trim($lottery);
        if ($lottery === '') {
            throw new ApiException('Lottery parameter required', 400);
        }

        $numberCounts = [];
        $bonusCounts = [];

        foreach ($this->votes->forLotteryAndDrawDate($lottery, $drawDate) as $entry) {
            foreach (json_decode($entry['numbers'], true) ?: [] as $number) {
                $numberCounts[(int) $number] = ($numberCounts[(int) $number] ?? 0) + 1;
            }

            foreach (json_decode($entry['bonus_numbers'], true) ?: [] as $number) {
                $bonusCounts[(int) $number] = ($bonusCounts[(int) $number] ?? 0) + 1;
            }
        }

        foreach ($this->adminVotes->forLotteryAndDrawDate($lottery, $drawDate) as $entry) {
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

        arsort($numberCounts);
        arsort($bonusCounts);

        $topSection1 = array_slice(array_keys($numberCounts), 0, 5);
        $topSection2 = array_slice(array_keys($bonusCounts), 0, 2);
        sort($topSection1);
        sort($topSection2);

        return [
            'lottery' => $lottery,
            'draw_date' => $drawDate,
            'section1' => $this->mapCounts($numberCounts),
            'section2' => $this->mapCounts($bonusCounts),
            'topSection1' => array_map('intval', $topSection1),
            'topSection2' => array_map('intval', $topSection2),
        ];
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
}
