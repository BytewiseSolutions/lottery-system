<?php

class DrawRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function create(Draw $draw)
    {
        $sql = "INSERT INTO draw (
                    lottery_id,
                    draw_date,
                    status,
                    jackpot
                ) VALUES (
                    :lottery_id,
                    :draw_date,
                    :status,
                    :jackpot
                )";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':lottery_id' => $draw->lottery_id,
            ':draw_date'  => $draw->draw_date,
            ':status'     => $draw->status,
            ':jackpot'    => $draw->jackpot
        ]);

        $draw->id = $this->pdo->lastInsertId();

        return $this->findById($draw->id) ?: $draw;
    }

    public function ensureDefaultLotteries()
    {
        $sql = "INSERT INTO lottery (
                    id,
                    name,
                    code,
                    main_numbers_count,
                    bonus_numbers_count,
                    jackpot,
                    is_active
                ) VALUES (
                    :id,
                    :name,
                    :code,
                    :main_numbers_count,
                    :bonus_numbers_count,
                    :jackpot,
                    :is_active
                )
                ON DUPLICATE KEY UPDATE
                    name = VALUES(name),
                    code = VALUES(code),
                    jackpot = VALUES(jackpot),
                    is_active = VALUES(is_active)";

        $stmt = $this->pdo->prepare($sql);

        foreach (Draw::getDefaultLotteries() as $id => $lottery) {
            $stmt->execute([
                ':id' => $id,
                ':name' => $lottery['name'],
                ':code' => $lottery['code'],
                ':main_numbers_count' => REQUIRED_MAIN_NUMBERS,
                ':bonus_numbers_count' => REQUIRED_BONUS_NUMBERS,
                ':jackpot' => 10.00,
                ':is_active' => 1
            ]);
        }
    }

    public function exists($lotteryId, $date)
    {
        $sql = "SELECT id
                FROM draw
                WHERE lottery_id = :lottery_id
                AND DATE(draw_date) = :draw_date
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':lottery_id' => $lotteryId,
            ':draw_date'  => $date
        ]);

        return $stmt->fetch() ? true : false;
    }

    public function getNextUpcomingDraw()
    {
        $sql = "SELECT d.*, l.name AS lottery
                FROM draw d
                LEFT JOIN lottery l ON l.id = d.lottery_id
                WHERE d.draw_date >= NOW()
                AND d.status = :status
                ORDER BY d.draw_date ASC
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':status' => DRAW_SCHEDULED
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Draw($row) : null;
    }

    public function getUpcomingDraws()
    {
        $sql = "SELECT d.*, l.name AS lottery
                FROM draw d
                LEFT JOIN lottery l ON l.id = d.lottery_id
                WHERE d.draw_date >= NOW()
                AND d.status = :status
                ORDER BY d.draw_date ASC
                LIMIT 10";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':status' => DRAW_SCHEDULED
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $draws = [];

        foreach ($rows as $row) {
            $draws[] = new Draw($row);
        }

        return $draws;
    }

    public function getPastDrawsWithoutResults()
    {
        $sql = "SELECT d.*, l.name AS lottery
                FROM draw d
                LEFT JOIN lottery l ON l.id = d.lottery_id
                LEFT JOIN result r ON r.draw_id = d.id
                WHERE d.draw_date < NOW()
                AND r.id IS NULL
                ORDER BY d.draw_date DESC
                LIMIT 3";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $draws = [];

        foreach ($rows as $row) {
            $draws[] = new Draw($row);
        }

        return $draws;
    }

    public function getDueDrawsWithoutResults($asOf = null)
    {
        $asOf = $asOf ?: (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s');

        $sql = "SELECT d.*, l.name AS lottery
                FROM draw d
                LEFT JOIN lottery l ON l.id = d.lottery_id
                LEFT JOIN result r ON r.draw_id = d.id
                WHERE d.draw_date <= :as_of
                AND d.status = 'scheduled'
                AND r.id IS NULL
                ORDER BY d.draw_date ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':as_of' => $asOf
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $draws = [];

        foreach ($rows as $row) {
            $draws[] = new Draw($row);
        }

        return $draws;
    }

    public function countVotesByLottery($lotteryId)
    {
        $sql = "SELECT COUNT(*) total
                FROM vote v
                INNER JOIN draw d ON d.id = v.draw_id
                WHERE d.lottery_id = :lottery_id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':lottery_id' => $lotteryId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['total'] ?? 0);
    }

    public function findById($id)
    {
        $sql = "SELECT d.*, l.name AS lottery
                FROM draw d
                LEFT JOIN lottery l ON l.id = d.lottery_id
                WHERE d.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Draw($row) : null;
    }
}
