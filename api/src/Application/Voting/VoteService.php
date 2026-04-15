<?php
declare(strict_types=1);

namespace App\Application\Voting;

use App\Core\ApiException;
use App\Domain\Voting\VoteRepository;

final class VoteService
{
    public function __construct(
        private readonly VoteRepository $votes,
        private readonly ?LeadingNumbersService $leadingNumbers = null
    ) {}

    public function submit(int $userId, array $payload): array
    {
        $lottery = trim((string) ($payload['lottery'] ?? ''));
        $numbers = $payload['numbers'] ?? [];
        $bonusNumbers = $payload['bonusNumbers'] ?? [];
        $drawDate = substr((string) ($payload['drawDate'] ?? date('Y-m-d')), 0, 10);

        if ($lottery === '' || !is_array($numbers) || !is_array($bonusNumbers)) {
            throw new ApiException('Missing required fields', 400);
        }

        if (count($numbers) !== 5 || count($bonusNumbers) !== 2) {
            throw new ApiException('Invalid number selection', 400);
        }

        $numbers = $this->normalizeNumbers($numbers, 5);
        $bonusNumbers = $this->normalizeNumbers($bonusNumbers, 2, $numbers);

        $this->votes->store($userId, $lottery, $numbers, $bonusNumbers, $drawDate);
        $this->leadingNumbers?->refreshSnapshot($lottery, $drawDate);

        return [
            'success' => true,
            'message' => 'Vote submitted successfully',
        ];
    }

    public function history(int $userId): array
    {
        $votes = $this->votes->historyForUser($userId);

        return [
            'votes' => array_map(static function (array $vote): array {
                return [
                    'id' => (int) $vote['id'],
                    'lottery' => $vote['lottery'],
                    'numbers' => json_decode($vote['numbers'], true) ?: [],
                    'bonusNumbers' => json_decode($vote['bonus_numbers'], true) ?: [],
                    'voteDate' => $vote['vote_date'],
                    'drawDate' => $vote['draw_date'] ? substr($vote['draw_date'], 0, 10) : null,
                    'createdAt' => $vote['created_at'],
                ];
            }, $votes),
        ];
    }

    private function normalizeNumbers(array $numbers, int $requiredCount, array $excluded = []): array
    {
        $normalized = array_map('intval', $numbers);
        $normalized = array_values(array_unique($normalized));
        sort($normalized);

        if (count($normalized) !== $requiredCount) {
            throw new ApiException('Invalid number selection', 400);
        }

        foreach ($normalized as $number) {
            if ($number < 1 || $number > 75) {
                throw new ApiException('Invalid number selection', 400);
            }
            if (in_array($number, $excluded, true)) {
                throw new ApiException('Bonus numbers must be different from main numbers', 400);
            }
        }

        return $normalized;
    }
}
