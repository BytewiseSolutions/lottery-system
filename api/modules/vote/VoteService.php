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

            $now = new DateTime();
            $votingCloseTime = new DateTime($draw->getVotingClosesAt());

            if ($now > $votingCloseTime) {
                return [
                    'success' => false,
                    'message' => 'Voting closed at 7:59 PM. You cannot vote for this lottery anymore.'
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

    public function getHighestVoteForDraw($drawId)
    {
        $drawId = (int)$drawId;

        if ($drawId <= 0) {
            return [
                'success' => false,
                'message' => 'Valid draw ID required'
            ];
        }

        try {
            $draw = $this->voteRepository->findDrawById($drawId);

            if (!$draw) {
                return [
                    'success' => false,
                    'message' => 'Draw not found'
                ];
            }

            $highestVote = $this->voteRepository->getHighestVoteByDrawId($drawId);

            if (!$highestVote) {
                $drawDate = (new DateTime($draw->draw_date))->format('Y-m-d');
                $summary = $this->buildVoteSummary((int)$draw->lottery_id, $drawDate);

                $this->voteRepository->upsertHighestVote(
                    $draw->getLotteryName(),
                    $draw->id,
                    $summary['topSection1'],
                    $summary['topSection2'],
                    $summary['totalMainVotes'],
                    $summary['totalBonusVotes']
                );

                $highestVote = $this->voteRepository->getHighestVoteByDrawId($drawId);
            }

            $highestVote = $highestVote ?: [];

            return [
                'success' => true,
                'data' => [
                    'draw_id' => $draw->id,
                    'lottery' => $draw->getLotteryName(),
                    'draw_date' => $draw->draw_date,
                    'jackpot' => $draw->jackpot,
                    'winning_numbers' => $this->sanitizeHighestVoteNumbers([
                        $highestVote['main_1'] ?? 0,
                        $highestVote['main_2'] ?? 0,
                        $highestVote['main_3'] ?? 0,
                        $highestVote['main_4'] ?? 0,
                        $highestVote['main_5'] ?? 0
                    ]),
                    'bonus_numbers' => $this->sanitizeHighestVoteNumbers([
                        $highestVote['bonus_1'] ?? 0,
                        $highestVote['bonus_2'] ?? 0
                    ]),
                    'total_main_votes' => (int)($highestVote['total_main_votes'] ?? 0),
                    'total_bonus_votes' => (int)($highestVote['total_bonus_votes'] ?? 0)
                ]
            ];

        } catch (Exception $e) {
            Logger::error('Get highest vote for draw failed', [
                'error' => $e->getMessage(),
                'draw_id' => $drawId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load highest vote for draw'
            ];
        }
    }

    public function getAdminVotesPage($page, $limit, array $filters = [])
    {
        $page = max(1, (int)$page);
        $limit = max(MIN_PAGE_SIZE, min(MAX_PAGE_SIZE, (int)$limit ?: DEFAULT_PAGE_SIZE));

        try {
            $votes = $this->voteRepository->getAdminVotesPage($page, $limit, $filters);
            $totalItems = $this->voteRepository->countAdminVotes($filters);
            $stats = $this->voteRepository->getAdminVoteStats($filters);

            return [
                'success' => true,
                'data' => $votes,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total_items' => $totalItems,
                    'total_pages' => $totalItems > 0 ? (int)ceil($totalItems / $limit) : 0
                ],
                'filters' => [
                    'lottery_options' => array_values(array_map(function ($lottery) {
                        return $lottery['name'];
                    }, Draw::getDefaultLotteries()))
                ],
                'stats' => [
                    'total_allocated_votes' => (int)($stats['total_allocated_votes'] ?? 0),
                    'allocations_count' => (int)($stats['allocations_count'] ?? 0),
                    'latest_created_at' => $stats['latest_created_at'] ?? null
                ]
            ];

        } catch (Exception $e) {
            Logger::error('Get admin votes page failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load vote allocations'
            ];
        }
    }

    public function getAdminVoteById($voteId)
    {
        $voteId = (int)$voteId;

        if ($voteId <= 0) {
            return [
                'success' => false,
                'message' => 'Valid vote allocation ID required'
            ];
        }

        try {
            $vote = $this->voteRepository->findAdminVoteById($voteId);

            if (!$vote) {
                return [
                    'success' => false,
                    'message' => 'Vote allocation not found'
                ];
            }

            return [
                'success' => true,
                'data' => $vote
            ];

        } catch (Exception $e) {
            Logger::error('Get admin vote by id failed', [
                'error' => $e->getMessage(),
                'vote_id' => $voteId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load vote allocation'
            ];
        }
    }

    public function createAdminVote(User $admin, array $input)
    {
        $drawId = isset($input['draw_id']) ? (int)$input['draw_id'] : 0;
        $votingDataInput = is_array($input['voting_data'] ?? null) ? $input['voting_data'] : [];

        if ($drawId <= 0) {
            return [
                'success' => false,
                'message' => 'Draw is required'
            ];
        }

        try {
            $mainNumberVotes = $this->normalizeVoteMap($votingDataInput['mainNumberVotes'] ?? [], REQUIRED_MAIN_NUMBERS);
            $bonusNumberVotes = $this->normalizeVoteMap($votingDataInput['bonusNumberVotes'] ?? [], REQUIRED_BONUS_NUMBERS, array_keys($mainNumberVotes));
            $mainNumbers = array_map('intval', array_keys($mainNumberVotes));
            $bonusNumbers = array_map('intval', array_keys($bonusNumberVotes));
            sort($mainNumbers);
            sort($bonusNumbers);
            $allocatedVotes = array_sum($mainNumberVotes) + array_sum($bonusNumberVotes);
            $draw = $this->voteRepository->findDrawById($drawId);

            if (!$draw) {
                return [
                    'success' => false,
                    'message' => 'Draw not found'
                ];
            }

            if ($draw->isCompleted() || $draw->isCancelled()) {
                return [
                    'success' => false,
                    'message' => 'Votes cannot be allocated to a completed or cancelled draw'
                ];
            }

            $this->voteRepository->beginTransaction();
            $createdVoteId = $this->voteRepository->createAdminVote(
                $admin->id,
                $draw->id,
                $draw->getLotteryName(),
                $mainNumbers,
                $bonusNumbers,
                $allocatedVotes,
                $draw->draw_date,
                [
                    'mainNumberVotes' => $mainNumberVotes,
                    'bonusNumberVotes' => $bonusNumberVotes,
                    'mainNumbers' => $mainNumbers,
                    'bonusNumbers' => $bonusNumbers
                ]
            );
            $this->refreshHighestVoteForDraw($draw->id);
            $this->voteRepository->commit();

            $this->activityLogService->log(
                $admin->id,
                ACTION_ADMIN_VOTE_CREATE,
                "Admin vote allocation created for {$draw->getLotteryName()} draw {$draw->draw_date}"
            );

            return [
                'success' => true,
                'message' => 'Vote allocation created successfully',
                'data' => $this->voteRepository->findAdminVoteById($createdVoteId)
            ];

        } catch (InvalidArgumentException $e) {
            $this->voteRepository->rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (Exception $e) {
            $this->voteRepository->rollBack();

            Logger::error('Create admin vote failed', [
                'error' => $e->getMessage(),
                'admin_id' => $admin->id
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create vote allocation'
            ];
        }
    }

    public function updateAdminVote(User $admin, $voteId, array $input)
    {
        $voteId = (int)$voteId;
        $drawId = isset($input['draw_id']) ? (int)$input['draw_id'] : 0;
        $votingDataInput = is_array($input['voting_data'] ?? null) ? $input['voting_data'] : [];

        if ($voteId <= 0 || $drawId <= 0) {
            return [
                'success' => false,
                'message' => 'Vote allocation and draw are required'
            ];
        }

        try {
            $existingVote = $this->voteRepository->findAdminVoteById($voteId);

            if (!$existingVote) {
                return [
                    'success' => false,
                    'message' => 'Vote allocation not found'
                ];
            }

            $draw = $this->voteRepository->findDrawById($drawId);

            if (!$draw) {
                return [
                    'success' => false,
                    'message' => 'Draw not found'
                ];
            }

            if ($draw->isCompleted() || $draw->isCancelled()) {
                return [
                    'success' => false,
                    'message' => 'Votes cannot be allocated to a completed or cancelled draw'
                ];
            }

            $mainNumberVotes = $this->normalizeVoteMap($votingDataInput['mainNumberVotes'] ?? [], REQUIRED_MAIN_NUMBERS);
            $bonusNumberVotes = $this->normalizeVoteMap($votingDataInput['bonusNumberVotes'] ?? [], REQUIRED_BONUS_NUMBERS, array_keys($mainNumberVotes));
            $mainNumbers = array_map('intval', array_keys($mainNumberVotes));
            $bonusNumbers = array_map('intval', array_keys($bonusNumberVotes));
            sort($mainNumbers);
            sort($bonusNumbers);
            $allocatedVotes = array_sum($mainNumberVotes) + array_sum($bonusNumberVotes);

            $this->voteRepository->beginTransaction();
            $this->voteRepository->updateAdminVote(
                $voteId,
                $draw->id,
                $draw->getLotteryName(),
                $mainNumbers,
                $bonusNumbers,
                $allocatedVotes,
                $draw->draw_date,
                [
                    'mainNumberVotes' => $mainNumberVotes,
                    'bonusNumberVotes' => $bonusNumberVotes,
                    'mainNumbers' => $mainNumbers,
                    'bonusNumbers' => $bonusNumbers
                ]
            );
            $this->refreshHighestVoteForDraw((int)$existingVote['draw_id']);

            if ((int)$existingVote['draw_id'] !== (int)$draw->id) {
                $this->refreshHighestVoteForDraw($draw->id);
            } else {
                $this->refreshHighestVoteForDraw($draw->id);
            }

            $this->voteRepository->commit();

            $this->activityLogService->log(
                $admin->id,
                ACTION_ADMIN_VOTE_UPDATE,
                "Admin vote allocation updated for {$draw->getLotteryName()} draw {$draw->draw_date}"
            );

            return [
                'success' => true,
                'message' => 'Vote allocation updated successfully',
                'data' => $this->voteRepository->findAdminVoteById($voteId)
            ];

        } catch (InvalidArgumentException $e) {
            $this->voteRepository->rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (Exception $e) {
            $this->voteRepository->rollBack();

            Logger::error('Update admin vote failed', [
                'error' => $e->getMessage(),
                'admin_id' => $admin->id,
                'vote_id' => $voteId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update vote allocation'
            ];
        }
    }

    public function deleteAdminVote(User $admin, $voteId)
    {
        $voteId = (int)$voteId;

        if ($voteId <= 0) {
            return [
                'success' => false,
                'message' => 'Valid vote allocation ID required'
            ];
        }

        try {
            $existingVote = $this->voteRepository->findAdminVoteById($voteId);

            if (!$existingVote) {
                return [
                    'success' => false,
                    'message' => 'Vote allocation not found'
                ];
            }

            $this->voteRepository->beginTransaction();
            $this->voteRepository->deleteAdminVote($voteId);
            $this->refreshHighestVoteForDraw((int)$existingVote['draw_id']);
            $this->voteRepository->commit();

            $this->activityLogService->log(
                $admin->id,
                ACTION_ADMIN_VOTE_DELETE,
                "Admin vote allocation deleted for {$existingVote['lottery']} draw {$existingVote['drawDate']}"
            );

            return [
                'success' => true,
                'message' => 'Vote allocation deleted successfully'
            ];

        } catch (Exception $e) {
            $this->voteRepository->rollBack();

            Logger::error('Delete admin vote failed', [
                'error' => $e->getMessage(),
                'admin_id' => $admin->id,
                'vote_id' => $voteId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to delete vote allocation'
            ];
        }
    }

    private function buildVoteSummary($lotteryId, $drawDate)
    {
        $votes = $this->voteRepository->getVotesForDraw($lotteryId, $drawDate);
        $mainCounts = [];
        $bonusCounts = [];

        foreach ($votes as $vote) {
            $weight = max(
                1,
                (int)($vote['total_votes'] ?? 0),
                (int)($vote['allocated_votes'] ?? 0)
            );

            foreach ($this->decodeNumbers($vote['numbers'] ?? null) as $number) {
                $mainCounts[$number] = ($mainCounts[$number] ?? 0) + $weight;
            }

            foreach ($this->decodeNumbers($vote['bonus_numbers'] ?? null) as $number) {
                $bonusCounts[$number] = ($bonusCounts[$number] ?? 0) + $weight;
            }
        }

        $lotteryName = Draw::lotteryNameFromId($lotteryId);

        foreach ($this->voteRepository->getAdminVotesForDraw($lotteryName, $drawDate) as $adminVote) {
            $votingData = is_string($adminVote['voting_data'] ?? null)
                ? json_decode($adminVote['voting_data'], true)
                : ($adminVote['voting_data'] ?? null);

            if (is_array($votingData)
                && isset($votingData['mainNumberVotes'], $votingData['bonusNumberVotes'])
                && is_array($votingData['mainNumberVotes'])
                && is_array($votingData['bonusNumberVotes'])) {
                foreach ($votingData['mainNumberVotes'] as $number => $votes) {
                    $number = (int)$number;
                    $mainCounts[$number] = ($mainCounts[$number] ?? 0) + max(0, (int)$votes);
                }

                foreach ($votingData['bonusNumberVotes'] as $number => $votes) {
                    $number = (int)$number;
                    $bonusCounts[$number] = ($bonusCounts[$number] ?? 0) + max(0, (int)$votes);
                }

                continue;
            }

            $mainNumbers = $this->decodeNumbers($adminVote['numbers'] ?? null);
            $bonusNumbers = $this->decodeNumbers($adminVote['bonus_numbers'] ?? null);
            $allocatedVotes = max(0, (int)($adminVote['allocated_votes'] ?? $adminVote['total_votes'] ?? 0));
            $totalNumbers = count($mainNumbers) + count($bonusNumbers);
            $votesPerNumber = $totalNumbers > 0 ? (int)floor($allocatedVotes / $totalNumbers) : 0;

            foreach ($mainNumbers as $number) {
                $mainCounts[$number] = ($mainCounts[$number] ?? 0) + $votesPerNumber;
            }

            foreach ($bonusNumbers as $number) {
                $bonusCounts[$number] = ($bonusCounts[$number] ?? 0) + $votesPerNumber;
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

    private function normalizeVoteMap(array $voteMap, $requiredCount, array $excluded = [])
    {
        $normalized = [];

        foreach ($voteMap as $number => $votes) {
            $number = (int)$number;
            $votes = (int)$votes;

            if ($number < 1 || $number > 75) {
                throw new InvalidArgumentException('Invalid number selection');
            }

            if (in_array($number, array_map('intval', $excluded), true)) {
                throw new InvalidArgumentException('Bonus numbers must be different from main numbers');
            }

            if ($votes <= 0) {
                throw new InvalidArgumentException('Allocated votes must be greater than 0 for each selected number');
            }

            $normalized[$number] = $votes;
        }

        if (count($normalized) !== $requiredCount) {
            throw new InvalidArgumentException('Invalid number selection');
        }

        ksort($normalized);

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

    private function sanitizeHighestVoteNumbers(array $numbers)
    {
        return array_values(array_filter(array_map('intval', $numbers), function ($number) {
            return $number > 0;
        }));
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

    private function refreshHighestVoteForDraw($drawId)
    {
        $draw = $this->voteRepository->findDrawById((int)$drawId);

        if (!$draw) {
            return;
        }

        $drawDate = (new DateTime($draw->draw_date))->format('Y-m-d');
        $summary = $this->buildVoteSummary((int)$draw->lottery_id, $drawDate);

        $this->voteRepository->upsertHighestVote(
            $draw->getLotteryName(),
            $draw->id,
            $summary['topSection1'],
            $summary['topSection2'],
            $summary['totalMainVotes'],
            $summary['totalBonusVotes']
        );
    }
}
