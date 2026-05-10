<?php

class ActivityLogDto
{
    public $user_id;
    public $action;
    public $details;
    public $ip_address;

    public function __construct($data = [])
    {
        $this->user_id    = $data['user_id'] ?? null;
        $this->action     = $data['action'] ?? null;
        $this->details    = $data['details'] ?? null;
        $this->ip_address  = $data['ip_address'] ?? ($_SERVER['REMOTE_ADDR'] ?? null);
    }

    public function toArray()
    {
        return [
            'user_id'    => $this->user_id,
            'action'     => $this->action,
            'details'    => $this->details,
            'ip_address' => $this->ip_address
        ];
    }
}