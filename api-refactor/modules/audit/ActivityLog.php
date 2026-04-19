<?php

class ActivityLog
{
    public $id;
    public $user_id;
    public $action;
    public $details;
    public $ip_address;
    public $created_at;

    public function __construct($data = [])
    {
        $this->id         = $data['id'] ?? null;
        $this->user_id    = $data['user_id'] ?? null;
        $this->action     = $data['action'] ?? null;
        $this->details    = $data['details'] ?? null;
        $this->ip_address = $data['ip_address'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
    }

    public function toArray()
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'action'     => $this->action,
            'details'    => $this->details,
            'ip_address' => $this->ip_address,
            'created_at' => $this->created_at
        ];
    }
}