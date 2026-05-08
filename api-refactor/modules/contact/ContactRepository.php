<?php

class ContactRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function create($name, $email, $message)
    {
        $sql = "INSERT INTO contact_message (name, email, message)
                VALUES (:name, :email, :message)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':message' => $message
        ]);
    }
}
