<?php
declare(strict_types=1);

namespace App\Application\Voting;

use App\Core\ApiException;

final class QuickPickService
{
    public function generate(array $payload): array
    {
        $type = $payload['type'] ?? 'main';
        $excludeNumbers = $payload['excludeNumbers'] ?? [];

        if (!is_array($excludeNumbers)) {
            $excludeNumbers = [];
        }

        $excludeNumbers = array_values(array_unique(array_map('intval', $excludeNumbers)));
        $availableNumbers = array_values(array_diff(range(1, 75), $excludeNumbers));

        if ($type === 'main') {
            return [
                'success' => true,
                'numbers' => $this->pick($availableNumbers, 5),
                'type' => 'main',
            ];
        }

        if ($type === 'bonus') {
            return [
                'success' => true,
                'numbers' => $this->pick($availableNumbers, 2),
                'type' => 'bonus',
            ];
        }

        throw new ApiException('Invalid type. Must be "main" or "bonus"', 400);
    }

    private function pick(array $availableNumbers, int $count): array
    {
        if (count($availableNumbers) < $count) {
            throw new ApiException(
                $count === 5
                    ? 'Not enough available numbers for main selection'
                    : 'Not enough available numbers for bonus selection',
                400
            );
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
}
