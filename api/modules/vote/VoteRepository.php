<?php

class VoteRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
        $this->ensureAdminVoteTable();
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
        $sql = "SELECT v.numbers, v.bonus_numbers, v.allocated_votes, v.total_votes, v.source
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

    public function getAdminVotesForDraw($lottery, $drawDate)
    {
        $sql = "SELECT numbers,
                       bonus_numbers,
                       allocated_votes,
                       total_votes,
                       voting_data
                FROM admin_vote
                WHERE lottery = :lottery
                AND DATE(draw_date) = :draw_date";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':lottery' => $lottery,
            ':draw_date' => $drawDate
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findAdminVoteById($voteId)
    {
        $sql = "SELECT av.*,
                       d.status AS draw_status,
                       u.first_name,
                       u.last_name,
                       u.email
                FROM admin_vote av
                LEFT JOIN draw d ON d.id = av.draw_id
                LEFT JOIN user u ON u.id = av.admin_id
                WHERE av.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $voteId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapAdminVoteRow($row) : null;
    }

    public function createAdminVote($adminId, $drawId, $lottery, array $numbers, array $bonusNumbers, $allocatedVotes, $drawDate, array $votingData)
    {
        $sql = "INSERT INTO admin_vote (
                    admin_id,
                    draw_id,
                    lottery,
                    numbers,
                    bonus_numbers,
                    allocated_votes,
                    voting_data,
                    total_votes,
                    vote_date,
                    draw_date
                ) VALUES (
                    :admin_id,
                    :draw_id,
                    :lottery,
                    :numbers,
                    :bonus_numbers,
                    :allocated_votes,
                    :voting_data,
                    :total_votes,
                    :vote_date,
                    :draw_date
                )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':admin_id' => $adminId,
            ':draw_id' => $drawId,
            ':lottery' => $lottery,
            ':numbers' => json_encode(array_values($numbers)),
            ':bonus_numbers' => json_encode(array_values($bonusNumbers)),
            ':allocated_votes' => (int)$allocatedVotes,
            ':voting_data' => json_encode($votingData),
            ':total_votes' => (int)$allocatedVotes,
            ':vote_date' => date('Y-m-d'),
            ':draw_date' => $drawDate
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateAdminVote($voteId, $drawId, $lottery, array $numbers, array $bonusNumbers, $allocatedVotes, $drawDate, array $votingData)
    {
        $sql = "UPDATE admin_vote
                SET draw_id = :draw_id,
                    lottery = :lottery,
                    numbers = :numbers,
                    bonus_numbers = :bonus_numbers,
                    allocated_votes = :allocated_votes,
                    voting_data = :voting_data,
                    total_votes = :total_votes,
                    draw_date = :draw_date,
                    updated_at = NOW()
                WHERE id = :id
                ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':draw_id' => $drawId,
            ':lottery' => $lottery,
            ':numbers' => json_encode(array_values($numbers)),
            ':bonus_numbers' => json_encode(array_values($bonusNumbers)),
            ':allocated_votes' => (int)$allocatedVotes,
            ':voting_data' => json_encode($votingData),
            ':total_votes' => (int)$allocatedVotes,
            ':draw_date' => $drawDate,
            ':id' => $voteId,
        ]);
    }

    public function deleteAdminVote($voteId)
    {
        $sql = "DELETE FROM admin_vote
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $voteId,
        ]);
    }

    public function getAdminVotesPage($page, $limit, array $filters = [])
    {
        $offset = max(0, ($page - 1) * $limit);
        $params = [];
        $where = $this->buildAdminVotesWhereClause($filters, $params);

        $sql = "SELECT av.*,
                       d.status AS draw_status,
                       u.first_name,
                       u.last_name,
                       u.email
                FROM admin_vote av
                LEFT JOIN draw d ON d.id = av.draw_id
                LEFT JOIN user u ON u.id = av.admin_id
                {$where}
                ORDER BY av.created_at " . ($this->isOldestSort($filters) ? 'ASC' : 'DESC') . "
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(function ($row) {
            return $this->mapAdminVoteRow($row);
        }, $rows);
    }

    public function countAdminVotes(array $filters = [])
    {
        $params = [];
        $where = $this->buildAdminVotesWhereClause($filters, $params);

        $sql = "SELECT COUNT(*) AS total
                FROM admin_vote av
                LEFT JOIN draw d ON d.id = av.draw_id
                LEFT JOIN user u ON u.id = av.admin_id
                {$where}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['total'] ?? 0);
    }

    public function getAdminVoteStats(array $filters = [])
    {
        $params = [];
        $where = $this->buildAdminVotesWhereClause($filters, $params);

        $sql = "SELECT COALESCE(SUM(av.total_votes), 0) AS total_allocated_votes,
                       COUNT(*) AS allocations_count,
                       MAX(av.created_at) AS latest_created_at
                FROM admin_vote av
                LEFT JOIN draw d ON d.id = av.draw_id
                LEFT JOIN user u ON u.id = av.admin_id
                {$where}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_allocated_votes' => 0,
            'allocations_count' => 0,
            'latest_created_at' => null
        ];
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

    private function buildAdminVotesWhereClause(array $filters, array &$params)
    {
        $conditions = ['1 = 1'];

        $search = trim((string)($filters['search'] ?? ''));
        if ($search !== '') {
            $conditions[] = "(CAST(av.id AS CHAR) LIKE :search
                OR av.lottery LIKE :search
                OR DATE_FORMAT(av.draw_date, '%Y-%m-%d') LIKE :search
                OR CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) LIKE :search
                OR COALESCE(u.email, '') LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $lottery = trim((string)($filters['lottery'] ?? 'all'));
        if ($lottery !== '' && strtolower($lottery) !== 'all') {
            $conditions[] = 'av.lottery = :lottery';
            $params[':lottery'] = $lottery;
        }

        $drawId = isset($filters['draw_id']) ? (int)$filters['draw_id'] : 0;
        if ($drawId > 0) {
            $conditions[] = 'av.draw_id = :draw_id';
            $params[':draw_id'] = $drawId;
        }

        return 'WHERE ' . implode(' AND ', $conditions);
    }

    private function isOldestSort(array $filters)
    {
        return strtolower((string)($filters['sort_order'] ?? 'newest')) === 'oldest';
    }

    private function mapAdminVoteRow(array $row)
    {
        $numbers = $this->decodeJsonArray($row['numbers'] ?? null);
        $bonusNumbers = $this->decodeJsonArray($row['bonus_numbers'] ?? null);
        $votingData = $this->decodeJsonObject($row['voting_data'] ?? null);
        $firstName = trim((string)($row['first_name'] ?? ''));
        $lastName = trim((string)($row['last_name'] ?? ''));
        $adminName = trim($firstName . ' ' . $lastName);

        return [
            'id' => (int)($row['id'] ?? 0),
            'draw_id' => isset($row['draw_id']) ? (int)$row['draw_id'] : 0,
            'lottery' => $row['lottery'] ?? '',
            'numbers' => $numbers,
            'bonusNumbers' => $bonusNumbers,
            'bonus_numbers' => $bonusNumbers,
            'allocatedVotes' => (int)($row['allocated_votes'] ?? 0),
            'allocated_votes' => (int)($row['allocated_votes'] ?? 0),
            'totalVotes' => (int)($row['total_votes'] ?? 0),
            'total_votes' => (int)($row['total_votes'] ?? 0),
            'voteDate' => $row['vote_date'] ?? null,
            'drawDate' => $row['draw_date'] ?? null,
            'draw_date' => $row['draw_date'] ?? null,
            'createdAt' => $row['created_at'] ?? null,
            'created_at' => $row['created_at'] ?? null,
            'draw_status' => $row['draw_status'] ?? null,
            'admin_name' => $adminName !== '' ? $adminName : 'Admin',
            'admin_email' => $row['email'] ?? null,
            'votingData' => $votingData,
            'voting_data' => $votingData
        ];
    }

    private function ensureAdminVoteTable()
    {
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS admin_vote (
                id INT AUTO_INCREMENT PRIMARY KEY,
                admin_id INT NOT NULL,
                draw_id INT NULL,
                lottery VARCHAR(50) NOT NULL,
                numbers JSON NOT NULL,
                bonus_numbers JSON,
                allocated_votes INT DEFAULT 0,
                voting_data JSON NULL,
                total_votes INT DEFAULT 0,
                vote_date DATE NOT NULL,
                draw_date DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_admin_vote_draw (draw_id),
                INDEX idx_admin_vote_lottery_date (lottery, draw_date),
                FOREIGN KEY (admin_id) REFERENCES user(id) ON DELETE CASCADE,
                FOREIGN KEY (draw_id) REFERENCES draw(id) ON DELETE SET NULL
            )"
        );

        $this->ensureColumnExists('admin_vote', 'draw_id', 'INT NULL AFTER admin_id');
        $this->ensureColumnExists('admin_vote', 'voting_data', 'JSON NULL AFTER allocated_votes');
        $this->ensureColumnExists('admin_vote', 'total_votes', 'INT DEFAULT 0 AFTER voting_data');
        $this->ensureColumnExists('admin_vote', 'updated_at', 'TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at');
    }

    private function ensureColumnExists($table, $column, $definition)
    {
        $stmt = $this->pdo->query("SHOW COLUMNS FROM {$table} LIKE '{$column}'");

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return;
        }

        $this->pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    }

    private function decodeJsonArray($value)
    {
        if (is_array($value)) {
            return array_values(array_map('intval', $value));
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                return array_values(array_map('intval', $decoded));
            }
        }

        return [];
    }

    private function decodeJsonObject($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
