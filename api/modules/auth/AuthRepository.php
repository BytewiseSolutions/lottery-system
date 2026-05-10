<?php

class AuthRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function storeToken($userId, $token)
    {
        $sql = "INSERT INTO user_tokens (user_id, token, created_at)
                VALUES (:user_id, :token, NOW())";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':user_id' => $userId,
            ':token' => $token
        ]);
    }

    public function deleteToken($token)
    {
        $sql = "DELETE FROM user_tokens WHERE token = :token";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':token' => $token
        ]);
    }

    public function findUserByToken($token)
    {
        $sql = "SELECT u.* 
                FROM user u
                JOIN user_tokens t ON u.id = t.user_id
                WHERE t.token = :token
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':token' => $token
        ]);

        $data = $stmt->fetch();

        return $data ? new User($data) : null;
    }
     public function storeResetToken($userId, $token)
    {
        $sql = "INSERT INTO password_resets (user_id, token, created_at)
                VALUES (:user_id, :token, NOW())";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':user_id' => $userId,
            ':token' => $token
        ]);
    }
    public function findResetToken($token)
    {
        $sql = "SELECT * FROM password_resets
                WHERE token = :token
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':token' => $token
        ]);

        return $stmt->fetch();
    }
 public function deleteResetToken($token)
    {
        $sql = "DELETE FROM password_resets WHERE token = :token";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':token' => $token
        ]);
    }
  public function updatePassword($userId, $password)
    {
        $sql = "UPDATE user
                SET password = :password, updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':password' => $password,
            ':id' => $userId
        ]);
    }
}