<?php

class User
{
    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $country;
    public $password;
    public $role;
    public $is_active;
    public $created_at;
    public $updated_at;

    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    public function fill($data)
    {
        $this->id = $data['id'] ?? null;
        $this->first_name = $data['first_name'] ?? null;
        $this->last_name = $data['last_name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->country = $data['country'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->role = $data['role'] ?? ROLE_USER;
        $this->is_active = $data['is_active'] ?? 1;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function getFullName()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function isAdmin()
    {
        return $this->role === ROLE_ADMIN;
    }

    public function isActive()
    {
        return $this->is_active == 1;
    }
    
    public function toArray($includePassword = false)
    {
        $data = [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->getFullName(),
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if ($includePassword) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}