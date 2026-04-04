<?php
declare(strict_types=1);

namespace App\Domain\Stats;

use PDO;

final class StatsRepository
{
    public function __construct(private readonly PDO $db) {}

    public function fetchSummary(): array
    {
        $totalUsers = (int) $this->scalar('SELECT COUNT(*) as total FROM user');
        $totalEntries = (int) $this->scalar('SELECT COUNT(*) as total FROM entry');
        $totalPayouts = (float) ($this->scalar('SELECT SUM(jackpot) as total FROM result') ?? 0);
        $winnersLastMonth = (int) ($this->scalar(
            'SELECT COUNT(*) as winners FROM winner WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)'
        ) ?? 0);

        return [
            'totalUsers' => $totalUsers,
            'winnersLastMonth' => $winnersLastMonth,
            'totalEntries' => $totalEntries,
            'totalPayouts' => $totalPayouts,
        ];
    }

    private function scalar(string $query): mixed
    {
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? reset($result) : null;
    }
}
