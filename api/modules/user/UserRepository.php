<?php

class UserRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function create(User $user)
    {
        $sql = "INSERT INTO user (first_name, last_name, email, phone, country, password, role, is_active, created_at) 
                VALUES (:first_name, :last_name, :email, :phone, :country, :password, :role, :is_active, NOW())";

        $stmt = $this->pdo->prepare($sql);
        
        $result = $stmt->execute([
            ':first_name' => $user->first_name,
            ':last_name' => $user->last_name,
            ':email' => $user->email,
            ':phone' => $user->phone,
            ':country' => $user->country,
            ':password' => $user->password,
            ':role' => $user->role,
            ':is_active' => $user->is_active
        ]);

        if ($result) {
            $user->id = $this->pdo->lastInsertId();
            // Fetch the created user to get the timestamp
            return $this->findById($user->id);
        }

        return false;
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM user WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        $userData = $stmt->fetch();
        
        if ($userData) {
            return new User($userData);
        }
        
        return null;
    }

    public function findByPhone($phone)
    {
        $sql = "SELECT * FROM user WHERE phone = :phone LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':phone' => $phone]);
        
        $userData = $stmt->fetch();
        
        if ($userData) {
            return new User($userData);
        }
        
        return null;
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM user WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $userData = $stmt->fetch();
        
        if ($userData) {
            return new User($userData);
        }
        
        return null;
    }

    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM user WHERE email = :email";
        $params = [':email' => $email];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }

    public function phoneExists($phone, $excludeId = null)
    {
        if (empty($phone)) {
            return false;
        }

        $sql = "SELECT COUNT(*) FROM user WHERE phone = :phone";
        $params = [':phone' => $phone];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }

    public function update(User $user)
    {
        $sql = "UPDATE user SET 
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                phone = :phone,
                country = :country,
                role = :role,
                is_active = :is_active,
                updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':first_name' => $user->first_name,
            ':last_name' => $user->last_name,
            ':email' => $user->email,
            ':phone' => $user->phone,
            ':country' => $user->country,
            ':role' => $user->role,
            ':is_active' => $user->is_active,
            ':id' => $user->id
        ]);
    }

    public function updatePassword($userId, $hashedPassword)
    {
        $sql = "UPDATE user SET password = :password, updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $userId
        ]);
    }

    public function updateStatus($userId, $isActive)
    {
        $sql = "UPDATE user SET is_active = :is_active, updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':is_active' => $isActive,
            ':id' => $userId
        ]);
    }

    public function deleteById($userId)
    {
        $this->pdo->prepare('DELETE FROM notification WHERE user_id = :id')->execute([':id' => $userId]);
        $this->pdo->prepare('DELETE FROM payment WHERE user_id = :id')->execute([':id' => $userId]);
        $this->pdo->prepare('DELETE FROM winner WHERE user_id = :id')->execute([':id' => $userId]);

        $stmt = $this->pdo->prepare('DELETE FROM user WHERE id = :id');

        return $stmt->execute([':id' => $userId]);
    }

    public function getUserStats($userId)
    {
        $sql = "SELECT
                    u.created_at,
                    (
                        SELECT COUNT(*)
                        FROM entry e
                        WHERE e.user_id = u.id
                    ) AS total_entries,
                    (
                        SELECT COALESCE(SUM(w.prize_amount), 0)
                        FROM winner w
                        WHERE w.user_id = u.id
                    ) AS total_winnings
                FROM user u
                WHERE u.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getProfilePictureId($userId)
    {
        $sql = "SELECT id
                FROM data_file
                WHERE user_id = :user_id
                  AND file_category = :file_category
                ORDER BY id DESC
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':file_category' => FILE_PROFILE_PICTURE
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['id'] : null;
    }

    public function getAll($page = 1, $limit = 20, $filters = [])
    {
        $offset = ($page - 1) * $limit;

        [$whereSql, $params] = $this->buildListFilters($filters);
        $sortDirection = (($filters['sort_order'] ?? 'newest') === 'oldest') ? 'ASC' : 'DESC';

        $sql = "SELECT * FROM user {$whereSql} ORDER BY created_at {$sortDirection}, id {$sortDirection} LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $users = [];
        while ($userData = $stmt->fetch()) {
            $users[] = new User($userData);
        }
        
        return $users;
    }

    public function getTotalCount($filters = [])
    {
        [$whereSql, $params] = $this->buildListFilters($filters);
        $sql = "SELECT COUNT(*) FROM user {$whereSql}";
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        
        return $stmt->fetchColumn();
    }

    private function buildListFilters($filters = [])
    {
        $conditions = [];
        $params = [];

        $search = trim((string)($filters['search'] ?? ''));
        if ($search !== '') {
            $conditions[] = '(CAST(id AS CHAR) LIKE :search OR first_name LIKE :search OR last_name LIKE :search OR CONCAT_WS(" ", first_name, last_name) LIKE :search OR email LIKE :search OR phone LIKE :search OR country LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        $role = trim((string)($filters['role'] ?? 'all'));
        if (in_array($role, [ROLE_USER, ROLE_ADMIN], true)) {
            $conditions[] = 'role = :role';
            $params[':role'] = $role;
        }

        $status = trim((string)($filters['status'] ?? 'all'));
        if ($status === 'active') {
            $conditions[] = 'is_active = 1';
        } elseif ($status === 'inactive') {
            $conditions[] = 'is_active = 0';
        }

        $whereSql = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return [$whereSql, $params];
    }
}
