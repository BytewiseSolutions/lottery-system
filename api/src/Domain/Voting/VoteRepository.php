<?php
declare(strict_types=1);

namespace App\Domain\Voting;

use PDO;

final class VoteRepository
{
    public function __construct(private readonly PDO $db) {}

    public function store(int $userId, string $lottery, array $numbers, array $bonusNumbers, string $drawDate): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO vote (user_id, lottery, numbers, bonus_numbers, vote_date, draw_date) VALUES (?, ?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $userId,
            $lottery,
            json_encode($numbers),
            json_encode($bonusNumbers),
            date('Y-m-d'),
            $drawDate,
        ]);
    }

    public function historyForUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, lottery, numbers, bonus_numbers, vote_date, draw_date, created_at
             FROM vote
             WHERE user_id = ?
             ORDER BY created_at DESC'
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function forLotteryAndDrawDate(string $lottery, string $drawDate): array
    {
        $stmt = $this->db->prepare(
            'SELECT numbers, bonus_numbers
             FROM vote
             WHERE lottery = ?
               AND (
                 DATE(draw_date) = DATE(?)
                 OR (draw_date IS NULL AND DATE(vote_date) = DATE(?))
               )'
        );
        $stmt->execute([$lottery, $drawDate, $drawDate]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
