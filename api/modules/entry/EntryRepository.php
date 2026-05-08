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

    public function getEntriesByDraw($drawId)
    {
        $sql = "SELECT e.*
                FROM entry e
                WHERE e.draw_id = :draw_id
                ORDER BY e.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':draw_id' => $drawId
        ]);

        $entries = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entries[] = new Entry($row);
        }

        return $entries;
    }

    public function getAllEntries()
    {
        return $this->getEntriesPage(1, PHP_INT_MAX);
    }

    public function getEntriesPage($page = 1, $limit = 20, $filters = [])
    {
        $page = max(1, (int)$page);
        $limit = max(1, (int)$limit);
        $offset = ($page - 1) * $limit;

        $params = [];
        $whereSql = $this->buildEntryFiltersWhereClause($filters, $params);
        $orderSql = $this->buildEntrySortClause($filters['sort_order'] ?? 'newest');

        $sql = "SELECT e.*,
                       CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) AS user_name,
                       u.email AS user_email,
                       u.phone AS user_phone,
                       d.draw_date AS draw_datetime
                FROM entry e
                INNER JOIN user u ON u.id = e.user_id
                LEFT JOIN draw d ON d.id = e.draw_id
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

        $entries = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entries[] = new Entry($row);
        }

        return $entries;
    }

    public function countEntries($filters = [])
    {
        $params = [];
        $whereSql = $this->buildEntryFiltersWhereClause($filters, $params);

        $sql = "SELECT COUNT(*) AS total
                FROM entry e
                INNER JOIN user u ON u.id = e.user_id
                LEFT JOIN draw d ON d.id = e.draw_id
                {$whereSql}";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['total'] ?? 0);
    }

    public function getDistinctLotteries()
    {
        $sql = "SELECT DISTINCT lottery
                FROM entry
                WHERE lottery IS NOT NULL AND lottery != ''
                ORDER BY lottery ASC";

        $stmt = $this->pdo->query($sql);

        return array_values(array_filter($stmt->fetchAll(PDO::FETCH_COLUMN), function ($lottery) {
            return !empty($lottery);
        }));
    }

    public function getEntryById($entryId)
    {
        $sql = "SELECT e.*,
                       CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) AS user_name,
                       u.email AS user_email,
                       u.phone AS user_phone,
                       d.draw_date AS draw_datetime
                FROM entry e
                INNER JOIN user u ON u.id = e.user_id
                LEFT JOIN draw d ON d.id = e.draw_id
                WHERE e.id = :entry_id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':entry_id' => $entryId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Entry($row) : null;
    }

    private function buildEntryFiltersWhereClause($filters, &$params)
    {
        $conditions = [];

        if (!empty($filters['lottery']) && $filters['lottery'] !== 'all') {
            $conditions[] = 'e.lottery = :lottery';
            $params[':lottery'] = $filters['lottery'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(
                CAST(e.id AS CHAR) LIKE :search
                OR CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) LIKE :search
                OR COALESCE(u.email, '') LIKE :search
                OR COALESCE(u.phone, '') LIKE :search
                OR COALESCE(e.lottery, '') LIKE :search
                OR DATE_FORMAT(COALESCE(d.draw_date, e.draw_date), '%e %M %Y') LIKE :search
                OR DATE_FORMAT(e.created_at, '%e %M %Y %H:%i') LIKE :search
            )";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (empty($conditions)) {
            return '';
        }

        return 'WHERE ' . implode(' AND ', $conditions);
    }

    private function buildEntrySortClause($sortOrder)
    {
        return strtolower((string)$sortOrder) === 'oldest'
            ? 'ORDER BY e.created_at ASC'
            : 'ORDER BY e.created_at DESC';
    }
}
