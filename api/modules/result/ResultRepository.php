<?php

class ResultRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function getPublishedResults($limit = null)
    {
        $sql = "SELECT r.*, d.draw_date, l.name AS lottery,
                       (
                           SELECT COUNT(*)
                           FROM entry e
                           WHERE e.draw_id = d.id
                       ) AS total_entries
                FROM result r
                INNER JOIN draw d ON d.id = r.draw_id
                INNER JOIN lottery l ON l.id = d.lottery_id
                WHERE r.status = :status
                ORDER BY d.draw_date DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', 'published');

        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        }

        $stmt->execute();

        $results = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new Result($row);
        }

        return $results;
    }

    public function getById($id)
    {
        return $this->findById($id);
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

    public function create(Result $result)
    {
        $sql = "INSERT INTO result (
                    draw_id,
                    winning_numbers,
                    bonus_numbers,
                    jackpot,
                    winners_count,
                    status,
                    notes
                ) VALUES (
                    :draw_id,
                    :winning_numbers,
                    :bonus_numbers,
                    :jackpot,
                    :winners_count,
                    :status,
                    :notes
                )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':draw_id' => $result->draw_id,
            ':winning_numbers' => json_encode($result->winning_numbers),
            ':bonus_numbers' => json_encode($result->bonus_numbers),
            ':jackpot' => $result->jackpot,
            ':winners_count' => $result->winners_count,
            ':status' => $result->status,
            ':notes' => $result->notes
        ]);

        $result->id = (int)$this->pdo->lastInsertId();

        return $this->findById($result->id);
    }

    public function update(Result $result)
    {
        $sql = "UPDATE result
                SET draw_id = :draw_id,
                    winning_numbers = :winning_numbers,
                    bonus_numbers = :bonus_numbers,
                    jackpot = :jackpot,
                    winners_count = :winners_count,
                    status = :status,
                    notes = :notes
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $result->id,
            ':draw_id' => $result->draw_id,
            ':winning_numbers' => json_encode($result->winning_numbers),
            ':bonus_numbers' => json_encode($result->bonus_numbers),
            ':jackpot' => $result->jackpot,
            ':winners_count' => $result->winners_count,
            ':status' => $result->status,
            ':notes' => $result->notes
        ]);

        return $this->findById($result->id);
    }

    public function updateWinnersCount($resultId, $winnersCount)
    {
        $stmt = $this->pdo->prepare('UPDATE result SET winners_count = :winners_count WHERE id = :id');
        $stmt->execute([
            ':winners_count' => $winnersCount,
            ':id' => $resultId
        ]);

        return $this->findById($resultId);
    }

    public function findById($id)
    {
        $sql = "SELECT r.*, d.draw_date, l.name AS lottery,
                       (
                           SELECT COUNT(*)
                           FROM entry e
                           WHERE e.draw_id = d.id
                       ) AS total_entries
                FROM result r
                INNER JOIN draw d ON d.id = r.draw_id
                INNER JOIN lottery l ON l.id = d.lottery_id
                WHERE r.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Result($row) : null;
    }

    public function drawExists($drawId)
    {
        $sql = "SELECT COUNT(*) as total
                FROM draw
                WHERE id = :draw_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':draw_id' => $drawId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['total'] ?? 0) > 0;
    }

    public function drawHasResult($drawId, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as total
                FROM result
                WHERE draw_id = :draw_id";
        $params = [
            ':draw_id' => $drawId
        ];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['total'] ?? 0) > 0;
    }

    public function syncDrawAfterResult($drawId, $jackpot)
    {
        $sql = "UPDATE draw
                SET jackpot = :jackpot,
                    status = :status
                WHERE id = :draw_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':jackpot' => $jackpot,
            ':status' => DRAW_COMPLETED,
            ':draw_id' => $drawId
        ]);
    }
}
