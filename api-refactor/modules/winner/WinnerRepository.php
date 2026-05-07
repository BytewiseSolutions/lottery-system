<?php

class WinnerRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function getWinners($resultId = null)
    {
        $sql = "SELECT w.*, u.first_name, u.last_name, u.email,
                       CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) AS name
                FROM winner w
                INNER JOIN user u ON u.id = w.user_id";
        $params = [];

        if ($resultId !== null) {
            $sql .= " WHERE w.result_id = :result_id";
            $params[':result_id'] = $resultId;
        }

        $sql .= " ORDER BY w.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $winners = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $winners[] = new Winner($row);
        }

        return $winners;
    }

    public function findById($winnerId)
    {
        $sql = "SELECT w.*, u.first_name, u.last_name, u.email,
                       CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) AS name
                FROM winner w
                INNER JOIN user u ON u.id = w.user_id
                WHERE w.id = :winner_id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':winner_id' => $winnerId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Winner($row) : null;
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

    public function updateClaimStatus($winnerId, $status)
    {
        $sql = "UPDATE winner
                SET claim_status = :status
                WHERE id = :winner_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':winner_id' => $winnerId
        ]);
    }

    public function deleteByResultId($resultId)
    {
        $stmt = $this->pdo->prepare('DELETE FROM winner WHERE result_id = :result_id');

        return $stmt->execute([
            ':result_id' => $resultId
        ]);
    }

    public function createMany(array $winners)
    {
        if (empty($winners)) {
            return true;
        }

        $sql = "INSERT INTO winner (
                    user_id,
                    result_id,
                    entry_id,
                    prize_amount,
                    claim_status,
                    payment_status
                ) VALUES (
                    :user_id,
                    :result_id,
                    :entry_id,
                    :prize_amount,
                    :claim_status,
                    :payment_status
                )";

        $stmt = $this->pdo->prepare($sql);

        foreach ($winners as $winner) {
            $stmt->execute([
                ':user_id' => $winner['user_id'],
                ':result_id' => $winner['result_id'],
                ':entry_id' => $winner['entry_id'],
                ':prize_amount' => $winner['prize_amount'],
                ':claim_status' => $winner['claim_status'] ?? CLAIM_PENDING,
                ':payment_status' => $winner['payment_status'] ?? PAYMENT_PENDING
            ]);
        }

        return true;
    }
}
