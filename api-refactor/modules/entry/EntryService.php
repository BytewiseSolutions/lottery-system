<?php

class EntryService
{
    private $entryRepository;
    private $activityLogService;

    public function __construct()
    {
        $this->entryRepository = new EntryRepository();
        $this->activityLogService = new ActivityLogService();
    }

    public function submitEntry(User $user, EntryDto $entryDto)
    {
        $lotteryId = $this->resolveLotteryId($entryDto->lottery);
        $drawDate = $this->normalizeDrawDate($entryDto->drawDate);

        if (!$lotteryId || !$drawDate) {
            return [
                'success' => false,
                'message' => 'Missing required fields'
            ];
        }

        try {
            $mainNumbers = $this->normalizeNumbers($entryDto->numbers, REQUIRED_MAIN_NUMBERS);
            $bonusNumbers = $this->normalizeNumbers($entryDto->bonusNumbers, REQUIRED_BONUS_NUMBERS, $mainNumbers);

            $draw = $this->entryRepository->findScheduledDraw($lotteryId, $drawDate);

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

            $this->entryRepository->beginTransaction();

            $entry = new Entry([
                'user_id' => $user->id,
                'draw_id' => $draw->id,
                'lottery' => $draw->getLotteryName(),
                'numbers' => $mainNumbers,
                'bonus_numbers' => $bonusNumbers,
                'draw_date' => $drawDate
            ]);

            $createdEntry = $this->entryRepository->create($entry);
            $updatedJackpot = number_format(((float)$draw->jackpot) + 0.01, 2, '.', '');
            $this->entryRepository->updateDrawJackpot($draw->id, $updatedJackpot);

            $this->entryRepository->commit();

            Logger::info('Entry submitted successfully', [
                'user_id' => $user->id,
                'entry_id' => $createdEntry->id,
                'draw_id' => $draw->id
            ]);

            $this->activityLogService->log(
                $user->id,
                ACTION_ENTRY_SUBMIT,
                "Entry submitted for {$draw->getLotteryName()} of {$drawDate}"
            );

            return [
                'success' => true,
                'message' => 'Entry submitted successfully!',
                'data' => [
                    'entryId' => $createdEntry->id,
                    'numbers' => $mainNumbers,
                    'bonusNumbers' => $bonusNumbers,
                    'lottery' => $draw->getLotteryName(),
                    'drawDate' => $drawDate,
                    'updatedJackpot' => $updatedJackpot
                ]
            ];

        } catch (InvalidArgumentException $e) {
            $this->entryRepository->rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];

        } catch (Exception $e) {
            $this->entryRepository->rollBack();

            Logger::error('Submit entry failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);

            return [
                'success' => false,
                'message' => 'Failed to submit entry'
            ];
        }
    }

    public function getEntryHistory($userId)
    {
        try {
            $entries = $this->entryRepository->getEntryHistory($userId);

            return [
                'success' => true,
                'data' => array_map(function ($entry) {
                    return $entry->toArray();
                }, $entries)
            ];

        } catch (Exception $e) {
            Logger::error('Get entry history failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load entry history'
            ];
        }
    }

    public function getEntriesByDraw($drawId)
    {
        try {
            $entries = $this->entryRepository->getEntriesByDraw($drawId);

            return [
                'success' => true,
                'data' => array_map(function ($entry) {
                    return $entry->toArray();
                }, $entries)
            ];

        } catch (Exception $e) {
            Logger::error('Get entries by draw failed', [
                'error' => $e->getMessage(),
                'draw_id' => $drawId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load draw entries'
            ];
        }
    }

    public function getAllEntries()
    {
        try {
            $entries = $this->entryRepository->getAllEntries();

            return [
                'success' => true,
                'data' => array_map(function ($entry) {
                    return $entry->toArray();
                }, $entries)
            ];

        } catch (Exception $e) {
            Logger::error('Get all entries failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load entries'
            ];
        }
    }

    public function getEntryById($entryId)
    {
        $entryId = (int)$entryId;

        if ($entryId <= 0) {
            return [
                'success' => false,
                'message' => 'Valid entry ID is required'
            ];
        }

        try {
            $entry = $this->entryRepository->getEntryById($entryId);

            if (!$entry) {
                return [
                    'success' => false,
                    'message' => 'Entry not found'
                ];
            }

            return [
                'success' => true,
                'data' => $entry->toArray()
            ];

        } catch (Exception $e) {
            Logger::error('Get entry by ID failed', [
                'error' => $e->getMessage(),
                'entry_id' => $entryId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to load entry details'
            ];
        }
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
