<?php

class AnalyticsRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function fetchStats()
    {
        return new Analytics([
            'totalUsers' => (int)$this->scalar('SELECT COUNT(*) as total FROM user'),
            'totalEntries' => (int)$this->scalar('SELECT COUNT(*) as total FROM entry'),
            'totalPayouts' => (float)($this->scalar('SELECT SUM(jackpot) as total FROM result') ?? 0),
            'winnersLastMonth' => (int)($this->scalar(
                'SELECT COUNT(*) as winners FROM winner WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)'
            ) ?? 0)
        ]);
    }

    private function scalar($query)
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? reset($result) : null;
    }
}
