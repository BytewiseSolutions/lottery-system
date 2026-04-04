<?php
declare(strict_types=1);

namespace App\Domain\Voting;

use PDO;

final class UpcomingDrawRepository
{
    public function __construct(private readonly PDO $db) {}

    public function scheduledDraws(): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, lottery, draw_date, jackpot, status
             FROM upcoming_draw
             WHERE status = 'scheduled'
             ORDER BY draw_date ASC"
        );
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function nextScheduledDraw(): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, lottery, draw_date, jackpot, status
             FROM upcoming_draw
             WHERE status = 'scheduled'
             ORDER BY draw_date ASC
             LIMIT 1"
        );
        $stmt->execute();

        $draw = $stmt->fetch(PDO::FETCH_ASSOC);

        return $draw ?: null;
    }
}
