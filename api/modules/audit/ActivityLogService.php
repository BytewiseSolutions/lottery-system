<?php

class ActivityLogService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new ActivityLogRepository();
    }

    public function log($userId, $action, $details = null)
    {
        try {
            $dto = new ActivityLogDto([
                'user_id' => $userId,
                'action'  => $action,
                'details' => $details
            ]);

            $this->repository->create($dto);

        } catch (Exception $e) {
            error_log('ActivityLogService Error: ' . $e->getMessage());
        }
    }

    public function getAll($limit = 50)
    {
        try {
            $logs = $this->repository->getAll($limit);

            return [
                'success' => true,
                'data' => array_map(fn($log) => $log->toArray(), $logs)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch logs'
            ];
        }
    }

    public function getByUser($userId)
    {
        try {
            $logs = $this->repository->getByUserId($userId);

            return [
                'success' => true,
                'data' => array_map(fn($log) => $log->toArray(), $logs)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch user logs'
            ];
        }
    }
}