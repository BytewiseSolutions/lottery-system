<?php

class VoteRepository
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

    public function findDrawByDate($lotteryId, $drawDate)
    {
        $sql = "SELECT d.*, l.name AS lottery
                FROM draw d
                INNER JOIN lottery l ON l.id = d.lottery_id
                WHERE d.lottery_id = :lottery_id
                AND DATE(d.draw_date) = :draw_date
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':lottery_id' => $lotteryId,
            ':draw_date' => $drawDate
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Draw($row) : null;
    }

    public function findDrawById($drawId)
    {
        $sql = "SELECT d.*, l.name AS lottery
                FROM draw d
                INNER JOIN lottery l ON l.id = d.lottery_id
                WHERE d.id = :draw_id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':draw_id' => $drawId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Draw($row) : null;
    }

    public function create(Vote $vote)
    {
        $sql = "INSERT INTO vote (
                    user_id,
                    lottery,
                    draw_id,
                    numbers,
                    bonus_numbers,
                    source,
                    vote_date,
                    allocated_votes,
                    total_votes
                ) VALUES (
                    :user_id,
                    :lottery,
                    :draw_id,
                    :numbers,
                    :bonus_numbers,
                    :source,
                    :vote_date,
                    :allocated_votes,
                    :total_votes
                )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $vote->user_id,
            ':lottery' => $vote->lottery,
            ':draw_id' => $vote->draw_id,
            ':numbers' => json_encode($vote->numbers),
            ':bonus_numbers' => json_encode($vote->bonus_numbers),
            ':source' => $vote->source,
            ':vote_date' => $vote->vote_date,
            ':allocated_votes' => $vote->allocated_votes,
            ':total_votes' => $vote->total_votes
        ]);

        $vote->id = $this->pdo->lastInsertId();

        return $vote;
    }

    public function createEntry($userId, $drawId)
    {
        $sql = "INSERT INTO entry (user_id, draw_id)
                VALUES (:user_id, :draw_id)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':draw_id' => $drawId
        ]);

        return (int)$this->pdo->lastInsertId();
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

    public function getVoteHistory($userId)
    {
        $sql = "SELECT v.*, d.draw_date
                FROM vote v
                LEFT JOIN draw d ON d.id = v.draw_id
                WHERE v.user_id = :user_id
                ORDER BY v.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId
        ]);

        $votes = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $votes[] = new Vote($row);
        }

        return $votes;
    }

    public function getVotesForDraw($lotteryId, $drawDate)
    {
        $sql = "SELECT v.numbers, v.bonus_numbers
                FROM vote v
                INNER JOIN draw d ON d.id = v.draw_id
                WHERE d.lottery_id = :lottery_id
                AND DATE(d.draw_date) = :draw_date";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':lottery_id' => $lotteryId,
            ':draw_date' => $drawDate
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function upsertHighestVote($lottery, $drawId, array $mainNumbers, array $bonusNumbers, $totalMainVotes, $totalBonusVotes)
    {
        $mainNumbers = $this->padNumbers($mainNumbers, REQUIRED_MAIN_NUMBERS);
        $bonusNumbers = $this->padNumbers($bonusNumbers, REQUIRED_BONUS_NUMBERS);

        $sql = "INSERT INTO highest_vote (
                    lottery,
                    draw_id,
                    main_1,
                    main_2,
                    main_3,
                    main_4,
                    main_5,
                    bonus_1,
                    bonus_2,
                    total_main_votes,
                    total_bonus_votes
                ) VALUES (
                    :lottery,
                    :draw_id,
                    :main_1,
                    :main_2,
                    :main_3,
                    :main_4,
                    :main_5,
                    :bonus_1,
                    :bonus_2,
                    :total_main_votes,
                    :total_bonus_votes
                )
                ON DUPLICATE KEY UPDATE
                    lottery = VALUES(lottery),
                    main_1 = VALUES(main_1),
                    main_2 = VALUES(main_2),
                    main_3 = VALUES(main_3),
                    main_4 = VALUES(main_4),
                    main_5 = VALUES(main_5),
                    bonus_1 = VALUES(bonus_1),
                    bonus_2 = VALUES(bonus_2),
                    total_main_votes = VALUES(total_main_votes),
                    total_bonus_votes = VALUES(total_bonus_votes)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':lottery' => $lottery,
            ':draw_id' => $drawId,
            ':main_1' => $mainNumbers[0],
            ':main_2' => $mainNumbers[1],
            ':main_3' => $mainNumbers[2],
            ':main_4' => $mainNumbers[3],
            ':main_5' => $mainNumbers[4],
            ':bonus_1' => $bonusNumbers[0],
            ':bonus_2' => $bonusNumbers[1],
            ':total_main_votes' => (int)$totalMainVotes,
            ':total_bonus_votes' => (int)$totalBonusVotes
        ]);
    }

    public function getHighestVoteByDrawId($drawId)
    {
        $sql = "SELECT *
                FROM highest_vote
                WHERE draw_id = :draw_id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':draw_id' => $drawId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function padNumbers(array $numbers, $requiredCount)
    {
        $numbers = array_values(array_map('intval', $numbers));

        while (count($numbers) < $requiredCount) {
            $numbers[] = 0;
        }

        return array_slice($numbers, 0, $requiredCount);
    }
}
