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

    public function getAll($page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM user ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $users = [];
        while ($userData = $stmt->fetch()) {
            $users[] = new User($userData);
        }
        
        return $users;
    }

    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) FROM user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
}