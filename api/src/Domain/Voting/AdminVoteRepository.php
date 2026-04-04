<?php
declare(strict_types=1);

namespace App\Domain\Voting;

use PDO;

final class AdminVoteRepository
{
    public function __construct(private readonly PDO $db) {}

    public function forLotteryAndDrawDate(string $lottery, string $drawDate): array
    {
        $stmt = $this->db->prepare(
            'SELECT numbers, bonus_numbers, allocated_votes, voting_data
             FROM admin_vote
             WHERE lottery = ? AND draw_date = ?'
        );
        $stmt->execute([$lottery, $drawDate]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
