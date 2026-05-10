<?php

class PaymentRepository
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

    public function getWinnerById($winnerId)
    {
        $sql = "SELECT *
                FROM winner
                WHERE id = :winner_id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':winner_id' => $winnerId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(Payment $payment)
    {
        $sql = "INSERT INTO payment (
                    winner_id,
                    user_id,
                    amount,
                    payment_method,
                    transaction_id,
                    status,
                    approved_at
                ) VALUES (
                    :winner_id,
                    :user_id,
                    :amount,
                    :payment_method,
                    :transaction_id,
                    :status,
                    :approved_at
                )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':winner_id' => $payment->winner_id,
            ':user_id' => $payment->user_id,
            ':amount' => $payment->amount,
            ':payment_method' => $payment->payment_method,
            ':transaction_id' => $payment->transaction_id,
            ':status' => $payment->status,
            ':approved_at' => $payment->approved_at
        ]);

        $payment->id = (int)$this->pdo->lastInsertId();

        return $payment;
    }

    public function updateWinnerPaymentStatus($winnerId, $status)
    {
        $sql = "UPDATE winner
                SET payment_status = :status
                WHERE id = :winner_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':winner_id' => $winnerId
        ]);
    }
}
