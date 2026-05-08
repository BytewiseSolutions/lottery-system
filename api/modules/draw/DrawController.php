<?php

class DrawController
{
    private $drawService;

    public function __construct()
    {
        $this->drawService = new DrawService();
    }

    public function getCurrentDraw()
    {
        try {
            $result = $this->drawService->getCurrentDraw();

            if ($result['success']) {
                Response::json(true, 'Current draw fetched successfully', $result['data'], HTTP_OK);
            } else {
                Response::json(false, $result['message'], null, HTTP_NOT_FOUND);
            }

        } catch (Exception $e) {
            Logger::error('DrawController current draw error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch current draw', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getUpcomingDraws()
    {
        try {
            $result = $this->drawService->getUpcomingDraws();

            if ($result['success']) {
                Response::json(true, 'Upcoming draws fetched successfully', $result['data'], HTTP_OK);
            } else {
                Response::json(false, $result['message'], null, HTTP_NOT_FOUND);
            }

        } catch (Exception $e) {
            Logger::error('DrawController upcoming draws error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch upcoming draws', null, HTTP_INTERNAL_ERROR);
        }
    }

    public function getPastDrawsWithoutResults()
    {
        try {
            $result = $this->drawService->getPastDrawsWithoutResults();

            if ($result['success']) {
                Response::json(true, 'Past draws fetched successfully', $result['data'], HTTP_OK);
            } else {
                Response::json(false, $result['message'], null, HTTP_NOT_FOUND);
            }

        } catch (Exception $e) {
            Logger::error('DrawController past draws error', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to fetch past draws', null, HTTP_INTERNAL_ERROR);
        }
    }
}
