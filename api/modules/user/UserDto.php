<?php

class UserDto
{
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $country;
    public $password;
    public $confirm_password;
    public $role;

    public function __construct($data = [])
    {
        $this->first_name = $data['first_name'] ?? null;
        $this->last_name = $data['last_name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->country = $data['country'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->confirm_password = $data['confirm_password'] ?? null;
        $this->role = $data['role'] ?? ROLE_USER;
    }

    public function toUser()
    {
        return new User([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'country'    => $this->country,
            'password'   => $this->password,
            'role'       => $this->role,
            'is_active'  => 1
        ]);
    }
}