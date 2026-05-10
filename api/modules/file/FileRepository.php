<?php

class FileRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function create($userId, $fileName, $fileType, $fileSize, $filePath, $fileCategory = FILE_PROFILE_PICTURE)
    {
        $sql = "INSERT INTO data_file (
                    user_id,
                    file_name,
                    file_type,
                    file_size,
                    file_path,
                    file_category,
                    created_at
                ) VALUES (
                    :user_id,
                    :file_name,
                    :file_type,
                    :file_size,
                    :file_path,
                    :file_category,
                    NOW()
                )";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':user_id' => $userId,
            ':file_name' => $fileName,
            ':file_type' => $fileType,
            ':file_size' => $fileSize,
            ':file_path' => $filePath,
            ':file_category' => $fileCategory
        ]);

        return $result ? (int)$this->pdo->lastInsertId() : 0;
    }

    public function findById($fileId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM data_file WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $fileId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
