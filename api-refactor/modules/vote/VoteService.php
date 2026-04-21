<?php

class VoteService
{
    private $voteRepository;
    private $activityLogService;

    public function __construct()
    {
        $this->voteRepository = new VoteRepository();
        $this->activityLogService = new ActivityLogService();
    }

    public function submitVote(User $user, VoteDto $voteDto)
    {
        $lotteryId = $this->resolveLotteryId($voteDto->lottery);
        $drawDate = $this->normalizeDrawDate($voteDto->drawDate);

        if (!$lotteryId || !$drawDate) {
            return [
                'success' => false,
                'message' => 'Missing required fields'
            ];
        }

        try {
            $mainNumbers = $this->normalizeNumbers($voteDto->numbers, REQUIRED_MAIN_NUMBERS);
            $bonusNumbers = $this->normalizeNumbers($voteDto->bonusNumbers, REQUIRED_BONUS_NUMBERS, $mainNumbers);

            $draw = $this->voteRepository->findScheduledDraw($lotteryId, $drawDate);

            if (!$draw) {
                return [
                    'success' => false,
                    'message' => 'This draw is no longer available for play'
                ];
            }

            $drawDateTime = new DateTime($draw->draw_date);
            $now = new DateTime();

            if ($now >= $drawDateTime) {
                return [
                    'success' => false,
                    'message' => 'Draw time has passed. You cannot play this lottery anymore.'
                ];
            }

            $this->voteRepository->beginTransaction();

            $vote = new Vote([
                'user_id' => $user->id,
                'lottery' => $draw->getLotteryName(),
                'draw_id' => $draw->id,
                'numbers' => $mainNumbers,
                'bonus_numbers' => $bonusNumbers,
                'source' => VOTE_SOURCE_USER,
                'vote_date' => date('Y-m-d'),
                'allocated_votes' => 1,
                'total_votes' => 1,
                'draw_date' => $draw->draw_date
            ]);

            $createdVote = $this->voteRepository->create($vote);
            $summary = $this->buildVoteSummary($lotteryId, $drawDate);

            $this->voteRepository->upsertHighestVote(
                $draw->getLotteryName(),
                $draw->id,
                $summary['topSection1'],
                $summary['topSection2'],
                $summary['totalMainVotes'],
                $summary['totalBonusVotes']
            );

            $this->voteRepository->commit();

            Logger::info('Vote submitted successfully', [
                'user_id' => $user->id,
                'vote_id' => $createdVote->id,
                'draw_id' => $draw->id
            ]);

            $this->activityLogService->log(
                $user->id,
                ACTION_VOTE_SUBMIT,
                "Vote submitted for {$draw->getLotteryName()} of {$drawDate}"
            );

            return [
                'success' => true,
                'message' => 'Vote submitted successfully!',
                'data' => [
                    'voteId' => $createdVote->id,
                    'numbers' => $mainNumbers,
                    'bonusNumbers' => $bonusNumbers,
                    'lottery' => $draw->getLotteryName(),
                    'drawDate' => $drawDate
                ]
            ];

        } catch (InvalidArgumentException $e) {
            $this->voteRepository->rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];

        } catch (Exception $e) {
            $this->voteRepository->rollBack();

            Logger::error('Submit vote failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);

            return [
                'success' => false,
                'message' => 'Failed to submit entry'
            ];
        }
    }

    public function getVoteHistory($userId)
    {
        try {
            $votes = $this->voteRepository->getVoteHistory($userId);

            return [
                'success' => true,
                'data' => [
                    'votes' => array_map(function ($vote) {
                        return $vote->toArray();
                    }, $votes)
                ]
            ];

        } catch (Exception $e) {
            Logger::error('Get vote history failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load vote history'
            ];
        }
    }

    public function getQuickPick($type, array $excludeNumbers = [])
    {
        try {
            $excludeNumbers = array_values(array_unique(array_map('intval', $excludeNumbers)));
            $availableNumbers = array_values(array_diff(range(1, 75), $excludeNumbers));

            if ($type === 'main') {
                return [
                    'success' => true,
                    'data' => [
                        'numbers' => $this->pick($availableNumbers, REQUIRED_MAIN_NUMBERS),
                        'type' => 'main'
                    ]
                ];
            }

            if ($type === 'bonus') {
                return [
                    'success' => true,
                    'data' => [
                        'numbers' => $this->pick($availableNumbers, REQUIRED_BONUS_NUMBERS),
                        'type' => 'bonus'
                    ]
                ];
            }

            return [
                'success' => false,
                'message' => 'Invalid type. Must be "main" or "bonus"'
            ];

        } catch (InvalidArgumentException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getLeadingNumbers($lottery, $drawDate)
    {
        $lotteryId = $this->resolveLotteryId($lottery);
        $drawDate = $this->normalizeDrawDate($drawDate) ?: date('Y-m-d');

        if (!$lotteryId) {
            return [
                'success' => false,
                'message' => 'Lottery parameter required'
            ];
        }

        try {
            $summary = $this->buildVoteSummary($lotteryId, $drawDate);
            $draw = $this->voteRepository->findDrawByDate($lotteryId, $drawDate);

            if ($draw && (!empty($summary['section1']) || !empty($summary['section2']))) {
                $this->voteRepository->upsertHighestVote(
                    $draw->getLotteryName(),
                    $draw->id,
                    $summary['topSection1'],
                    $summary['topSection2'],
                    $summary['totalMainVotes'],
                    $summary['totalBonusVotes']
                );
            }

            return [
                'success' => true,
                'data' => [
                    'lottery' => Draw::lotteryNameFromId($lotteryId),
                    'draw_date' => $drawDate,
                    'section1' => $summary['section1'],
                    'section2' => $summary['section2'],
                    'topSection1' => $summary['topSection1'],
                    'topSection2' => $summary['topSection2']
                ]
            ];

        } catch (Exception $e) {
            Logger::error('Get leading numbers failed', [
                'error' => $e->getMessage(),
                'lottery' => $lottery,
                'draw_date' => $drawDate
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load leading numbers'
            ];
        }
    }

    private function buildVoteSummary($lotteryId, $drawDate)
    {
        $votes = $this->voteRepository->getVotesForDraw($lotteryId, $drawDate);
        $mainCounts = [];
        $bonusCounts = [];

        foreach ($votes as $vote) {
            foreach ($this->decodeNumbers($vote['numbers'] ?? null) as $number) {
                $mainCounts[$number] = ($mainCounts[$number] ?? 0) + 1;
            }

            foreach ($this->decodeNumbers($vote['bonus_numbers'] ?? null) as $number) {
                $bonusCounts[$number] = ($bonusCounts[$number] ?? 0) + 1;
            }
        }

        $section1 = $this->mapCounts($mainCounts);
        $section2 = $this->mapCounts($bonusCounts);

        return [
            'section1' => $section1,
            'section2' => $section2,
            'topSection1' => $this->extractTopNumbers($section1, REQUIRED_MAIN_NUMBERS),
            'topSection2' => $this->extractTopNumbers($section2, REQUIRED_BONUS_NUMBERS),
            'totalMainVotes' => array_sum($mainCounts),
            'totalBonusVotes' => array_sum($bonusCounts)
        ];
    }

    private function pick(array $availableNumbers, $count)
    {
        if (count($availableNumbers) < $count) {
            throw new InvalidArgumentException('Not enough available numbers');
        }

        $selectedNumbers = [];

        for ($index = 0; $index < $count; $index++) {
            $randomIndex = random_int(0, count($availableNumbers) - 1);
            $selectedNumbers[] = $availableNumbers[$randomIndex];
            array_splice($availableNumbers, $randomIndex, 1);
        }

        sort($selectedNumbers);

        return $selectedNumbers;
    }

    private function normalizeNumbers(array $numbers, $requiredCount, array $excluded = [])
    {
        $normalized = array_map('intval', $numbers);
        $normalized = array_values(array_unique($normalized));
        sort($normalized);

        if (count($normalized) !== $requiredCount) {
            throw new InvalidArgumentException('Invalid number selection');
        }

        foreach ($normalized as $number) {
            if ($number < 1 || $number > 75) {
                throw new InvalidArgumentException('Invalid number selection');
            }

            if (in_array($number, $excluded, true)) {
                throw new InvalidArgumentException('Bonus numbers must be different from main numbers');
            }
        }

        return $normalized;
    }

    private function decodeNumbers($value)
    {
        if (is_array($value)) {
            return array_map('intval', $value);
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                return array_map('intval', $decoded);
            }
        }

        return [];
    }

    private function mapCounts(array $counts)
    {
        $items = [];

        foreach ($counts as $number => $votes) {
            $items[] = [
                'number' => (int)$number,
                'votes' => (int)$votes
            ];
        }

        usort($items, function ($left, $right) {
            if ($left['votes'] === $right['votes']) {
                return $left['number'] <=> $right['number'];
            }

            return $right['votes'] <=> $left['votes'];
        });

        return $items;
    }

    private function extractTopNumbers(array $items, $requiredCount)
    {
        $numbers = array_map(function ($item) {
            return (int)$item['number'];
        }, array_slice($items, 0, $requiredCount));

        sort($numbers);

        return $numbers;
    }

    private function normalizeDrawDate($drawDate)
    {
        if (!$drawDate) {
            return null;
        }

        return substr((string)$drawDate, 0, 10);
    }

    private function resolveLotteryId($lottery)
    {
        if (!$lottery) {
            return null;
        }

        $lottery = strtolower(trim((string)$lottery));
        $lottery = str_replace(' lotto', '', $lottery);

        $map = [
            'mon' => 1,
            'monday' => 1,
            'wed' => 2,
            'wednesday' => 2,
            'fri' => 3,
            'friday' => 3
        ];

        return $map[$lottery] ?? null;
    }
}
