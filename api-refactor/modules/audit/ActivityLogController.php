<?php

class ActivityLogController
{
    private $service;

    public function __construct()
    {
        $this->service = new ActivityLogService();
    }

    public function getLogs()
    {
        try {
            $limit = $_GET['limit'] ?? 50;

            $result = $this->service->getAll($limit);

            Response::json(
                $result['success'],
                $result['message'] ?? 'Logs fetched',
                $result['data'] ?? null,
                200
            );

        } catch (Exception $e) {
            Response::json(false, 'Failed to fetch logs', null, 500);
        }
    }

    public function getUserLogs()
    {
        try {
            $userId = $_GET['user_id'] ?? null;

            if (!$userId) {
                Response::json(false, 'User ID required', null, 400);
                return;
            }

            $result = $this->service->getByUser($userId);

            Response::json(
                $result['success'],
                $result['message'] ?? 'User logs fetched',
                $result['data'] ?? null,
                200
            );

        } catch (Exception $e) {
            Response::json(false, 'Failed to fetch user logs', null, 500);
        }
    }
}