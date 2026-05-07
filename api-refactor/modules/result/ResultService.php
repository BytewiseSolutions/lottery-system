<?php

class ResultService
{
    private $resultRepository;
    private $entryRepository;
    private $winnerRepository;
    private $drawRepository;
    private $voteService;

    public function __construct()
    {
        $this->resultRepository = new ResultRepository();
        $this->entryRepository = new EntryRepository();
        $this->winnerRepository = new WinnerRepository();
        $this->drawRepository = new DrawRepository();
        $this->voteService = new VoteService();
    }

    public function getLatestResults($limit = 1)
    {
        try {
            $results = $this->resultRepository->getPublishedResults($limit);

            return [
                'success' => true,
                'data' => array_map(function ($result) {
                    return $result->toArray();
                }, $results)
            ];

        } catch (Exception $e) {
            Logger::error('Get latest results failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load latest results'
            ];
        }
    }

    public function getResults()
    {
        try {
            $results = $this->resultRepository->getPublishedResults();

            return [
                'success' => true,
                'data' => array_map(function ($result) {
                    return $result->toArray();
                }, $results)
            ];

        } catch (Exception $e) {
            Logger::error('Get results failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load results'
            ];
        }
    }

    public function getResultById($resultId)
    {
        $resultId = (int)$resultId;

        if ($resultId <= 0) {
            return [
                'success' => false,
                'message' => 'Valid result ID is required'
            ];
        }

        try {
            $result = $this->resultRepository->getById($resultId);

            if (!$result) {
                return [
                    'success' => false,
                    'message' => 'Result not found'
                ];
            }

            return [
                'success' => true,
                'data' => $result->toArray()
            ];

        } catch (Exception $e) {
            Logger::error('Get result by ID failed', [
                'error' => $e->getMessage(),
                'result_id' => $resultId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load result details'
            ];
        }
    }

    public function createResult(ResultDto $resultDto)
    {
        return $this->saveResult($resultDto, false);
    }

    public function updateResult(ResultDto $resultDto)
    {
        return $this->saveResult($resultDto, true);
    }

    public function autoPublishDueResults($asOf = null)
    {
        $processed = [];
        $skipped = [];

        try {
            $draws = $this->drawRepository->getDueDrawsWithoutResults($asOf);

            foreach ($draws as $draw) {
                $highestVote = $this->voteService->getHighestVoteForDraw($draw->id);

                if (
                    !$highestVote['success']
                    || empty($highestVote['data']['winning_numbers'])
                    || count($highestVote['data']['winning_numbers']) !== REQUIRED_MAIN_NUMBERS
                    || count($highestVote['data']['bonus_numbers'] ?? []) !== REQUIRED_BONUS_NUMBERS
                ) {
                    $skipped[] = [
                        'draw_id' => $draw->id,
                        'lottery' => $draw->getLotteryName(),
                        'draw_date' => $draw->draw_date,
                        'reason' => 'Highest-vote numbers are incomplete for this draw'
                    ];
                    continue;
                }

                $resultDto = new ResultDto([
                    'draw_id' => $draw->id,
                    'lottery' => $draw->getLotteryName(),
                    'draw_date' => $draw->draw_date,
                    'winning_numbers' => $highestVote['data']['winning_numbers'],
                    'bonus_numbers' => $highestVote['data']['bonus_numbers'],
                    'jackpot' => $draw->jackpot,
                    'winners_count' => 0,
                    'status' => RESULT_PUBLISHED,
                    'notes' => 'Auto-published from highest vote'
                ]);

                $created = $this->createResult($resultDto);

                if ($created['success']) {
                    $processed[] = [
                        'draw_id' => $draw->id,
                        'lottery' => $draw->getLotteryName(),
                        'draw_date' => $draw->draw_date,
                        'result_id' => $created['data']['id'] ?? null
                    ];
                } else {
                    $skipped[] = [
                        'draw_id' => $draw->id,
                        'lottery' => $draw->getLotteryName(),
                        'draw_date' => $draw->draw_date,
                        'reason' => $created['message'] ?? 'Failed to create result'
                    ];
                }
            }

            return [
                'success' => true,
                'data' => [
                    'processed' => $processed,
                    'skipped' => $skipped,
                    'processed_count' => count($processed),
                    'skipped_count' => count($skipped)
                ]
            ];

        } catch (Exception $e) {
            Logger::error('Auto publish due results failed', [
                'error' => $e->getMessage(),
                'as_of' => $asOf
            ]);

            return [
                'success' => false,
                'message' => 'Failed to auto-publish due results'
            ];
        }
    }

    private function saveResult(ResultDto $resultDto, $isUpdate)
    {
        if (empty($resultDto->draw_id)) {
            return [
                'success' => false,
                'message' => 'Draw is required'
            ];
        }

        if (count($resultDto->winning_numbers) !== REQUIRED_MAIN_NUMBERS) {
            return [
                'success' => false,
                'message' => 'Exactly 5 winning numbers are required'
            ];
        }

        if (!empty($resultDto->bonus_numbers) && count($resultDto->bonus_numbers) !== REQUIRED_BONUS_NUMBERS) {
            return [
                'success' => false,
                'message' => 'Exactly 2 bonus numbers are required'
            ];
        }

        if ($resultDto->jackpot === null || !is_numeric($resultDto->jackpot)) {
            return [
                'success' => false,
                'message' => 'Jackpot amount is required'
            ];
        }

        if (!$this->isValidNumberSet($resultDto->winning_numbers, MIN_MAIN_NUMBER, MAX_MAIN_NUMBER, REQUIRED_MAIN_NUMBERS)) {
            return [
                'success' => false,
                'message' => 'Winning numbers must be 5 unique numbers between 1 and 75'
            ];
        }

        if (!empty($resultDto->bonus_numbers) && !$this->isValidNumberSet($resultDto->bonus_numbers, MIN_BONUS_NUMBER, MAX_BONUS_NUMBER, REQUIRED_BONUS_NUMBERS)) {
            return [
                'success' => false,
                'message' => 'Bonus numbers must be 2 unique numbers between 1 and 75'
            ];
        }

        if (!empty(array_intersect($resultDto->winning_numbers, $resultDto->bonus_numbers))) {
            return [
                'success' => false,
                'message' => 'Bonus numbers must be different from winning numbers'
            ];
        }

        if ($isUpdate && empty($resultDto->id)) {
            return [
                'success' => false,
                'message' => 'Result ID is required for update'
            ];
        }

        try {
            if (!$this->resultRepository->drawExists($resultDto->draw_id)) {
                return [
                    'success' => false,
                    'message' => 'Selected draw does not exist'
                ];
            }

            if ($this->resultRepository->drawHasResult($resultDto->draw_id, $isUpdate ? $resultDto->id : null)) {
                return [
                    'success' => false,
                    'message' => 'A result already exists for the selected draw'
                ];
            }

            $this->resultRepository->beginTransaction();

            $result = $resultDto->toResult();
            $savedResult = $isUpdate
                ? $this->resultRepository->update($result)
                : $this->resultRepository->create($result);

            $this->winnerRepository->deleteByResultId($savedResult->id);
            $generatedWinners = $this->generateWinnersForResult($savedResult);

            if (!empty($generatedWinners)) {
                $this->winnerRepository->createMany($generatedWinners);
            }

            $savedResult->winners_count = count($generatedWinners);
            $savedResult = $this->resultRepository->updateWinnersCount($savedResult->id, $savedResult->winners_count);

            $this->resultRepository->syncDrawAfterResult($result->draw_id, $result->jackpot);
            $this->resultRepository->commit();

            return [
                'success' => true,
                'message' => $isUpdate ? 'Result updated successfully' : 'Result created successfully',
                'data' => $savedResult ? $savedResult->toArray() : null
            ];

        } catch (Exception $e) {
            $this->resultRepository->rollBack();

            Logger::error('Save result failed', [
                'error' => $e->getMessage(),
                'draw_id' => $resultDto->draw_id,
                'result_id' => $resultDto->id
            ]);

            return [
                'success' => false,
                'message' => 'Failed to save result'
            ];
        }
    }

    private function isValidNumberSet(array $numbers, $min, $max, $expectedCount)
    {
        $normalized = array_values(array_map('intval', $numbers));

        if (count($normalized) !== $expectedCount || count(array_unique($normalized)) !== $expectedCount) {
            return false;
        }

        foreach ($normalized as $number) {
            if ($number < $min || $number > $max) {
                return false;
            }
        }

        return true;
    }

    private function generateWinnersForResult(Result $result)
    {
        $entries = $this->entryRepository->getEntriesByDraw($result->draw_id);
        $matchingEntries = [];

        foreach ($entries as $entry) {
            $entryMain = array_values(array_map('intval', $entry->numbers));
            $entryBonus = array_values(array_map('intval', $entry->bonus_numbers));
            sort($entryMain);
            sort($entryBonus);

            $winningNumbers = array_values(array_map('intval', $result->winning_numbers));
            $bonusNumbers = array_values(array_map('intval', $result->bonus_numbers));
            sort($winningNumbers);
            sort($bonusNumbers);

            if ($entryMain === $winningNumbers && $entryBonus === $bonusNumbers) {
                $matchingEntries[] = $entry;
            }
        }

        if (empty($matchingEntries)) {
            return [];
        }

        $prizeAmount = round(((float)$result->jackpot) / count($matchingEntries), 2);

        return array_map(function ($entry) use ($result, $prizeAmount) {
            return [
                'user_id' => $entry->user_id,
                'result_id' => $result->id,
                'entry_id' => $entry->id,
                'prize_amount' => $prizeAmount,
                'claim_status' => CLAIM_PENDING,
                'payment_status' => PAYMENT_PENDING
            ];
        }, $matchingEntries);
    }
}
