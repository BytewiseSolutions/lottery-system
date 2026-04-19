<?php

class EntryRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function beginTransaction()
    {
        if (!$this->pdo->inTransaction()) {
            $this->pdo->beginTransaction();
        }
    }

    public function commit()
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->commit();
        }
    }

    public function rollBack()
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    public function findScheduledDraw($lotteryId, $drawDate)
    {
        $sql = "SELECT d.*, l.name AS lottery
                FROM draw d
                INNER JOIN lottery l ON l.id = d.lottery_id
                WHERE d.lottery_id = :lottery_id
                AND DATE(d.draw_date) = :draw_date
                AND d.status = :status
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':lottery_id' => $lotteryId,
            ':draw_date' => $drawDate,
            ':status' => DRAW_SCHEDULED
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Draw($row) : null;
    }

    public function create(Entry $entry)
    {
        $sql = "INSERT INTO entry (
                    user_id,
                    draw_id,
                    lottery,
                    numbers,
                    bonus_numbers,
                    draw_date
                ) VALUES (
                    :user_id,
                    :draw_id,
                    :lottery,
                    :numbers,
                    :bonus_numbers,
                    :draw_date
                )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $entry->user_id,
            ':draw_id' => $entry->draw_id,
            ':lottery' => $entry->lottery,
            ':numbers' => json_encode($entry->numbers),
            ':bonus_numbers' => json_encode($entry->bonus_numbers),
            ':draw_date' => $entry->draw_date
        ]);

        $entry->id = $this->pdo->lastInsertId();

        return $entry;
    }

    public function updateDrawJackpot($drawId, $jackpot)
    {
        $sql = "UPDATE draw
                SET jackpot = :jackpot
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':jackpot' => $jackpot,
            ':id' => $drawId
        ]);
    }

    public function getEntryHistory($userId)
    {
        $sql = "SELECT e.*
                FROM entry e
                WHERE e.user_id = :user_id
                ORDER BY e.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId
        ]);

        $entries = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entries[] = new Entry($row);
        }

        return $entries;
    }
}
