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

    public function getWinnersPage($page = 1, $limit = 20, $filters = [])
    {
        $page = max(1, (int)$page);
        $limit = max(1, (int)$limit);
        $offset = ($page - 1) * $limit;

        $params = [];
        $whereSql = $this->buildWinnerFiltersWhereClause($filters, $params);
        $orderSql = $this->buildWinnerSortClause($filters['sort_order'] ?? 'newest');

        $sql = "SELECT w.*, u.first_name, u.last_name, u.email,
                       CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) AS name
                FROM winner w
                INNER JOIN user u ON u.id = w.user_id
                {$whereSql}
                {$orderSql}
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $winners = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $winners[] = new Winner($row);
        }

        return $winners;
    }

    public function countWinners($filters = [])
    {
        $params = [];
        $whereSql = $this->buildWinnerFiltersWhereClause($filters, $params);

        $sql = "SELECT COUNT(*) AS total
                FROM winner w
                INNER JOIN user u ON u.id = w.user_id
                {$whereSql}";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['total'] ?? 0);
    }

    public function getWinnerStats($filters = [])
    {
        $params = [];
        $whereSql = $this->buildWinnerFiltersWhereClause($filters, $params);

        $sql = "SELECT
                    COALESCE(SUM(w.prize_amount), 0) AS total_prize_pool,
                    SUM(CASE WHEN w.payment_status = 'paid' THEN 1 ELSE 0 END) AS paid_winners_count,
                    SUM(CASE WHEN w.payment_status != 'paid' THEN 1 ELSE 0 END) AS pending_winners_count,
                    MAX(w.created_at) AS latest_winner_created_at
                FROM winner w
                INNER JOIN user u ON u.id = w.user_id
                {$whereSql}";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total_prize_pool' => (float)($row['total_prize_pool'] ?? 0),
            'paid_winners_count' => (int)($row['paid_winners_count'] ?? 0),
            'pending_winners_count' => (int)($row['pending_winners_count'] ?? 0),
            'latest_winner_created_at' => $row['latest_winner_created_at'] ?? null
        ];
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

    private function buildWinnerFiltersWhereClause($filters, &$params)
    {
        $conditions = [];

        if (!empty($filters['result_id'])) {
            $conditions[] = 'w.result_id = :result_id';
            $params[':result_id'] = (int)$filters['result_id'];
        }

        if (!empty($filters['claim_status']) && $filters['claim_status'] !== 'all') {
            $conditions[] = 'w.claim_status = :claim_status';
            $params[':claim_status'] = $filters['claim_status'];
        }

        if (!empty($filters['payment_status']) && $filters['payment_status'] !== 'all') {
            $conditions[] = 'w.payment_status = :payment_status';
            $params[':payment_status'] = $filters['payment_status'];
        }

        if (empty($conditions)) {
            return '';
        }

        return 'WHERE ' . implode(' AND ', $conditions);
    }

    private function buildWinnerSortClause($sortOrder)
    {
        return strtolower((string)$sortOrder) === 'oldest'
            ? 'ORDER BY w.created_at ASC'
            : 'ORDER BY w.created_at DESC';
    }
}
