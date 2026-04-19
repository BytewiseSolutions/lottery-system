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
        $sql = "SELECT r.*, d.draw_date, l.name AS lottery
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
}
