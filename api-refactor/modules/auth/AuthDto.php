<?php

class AuthDto
{
    public $identifier; 
    public $password;

    public function __construct($data = [])
    {
        $this->identifier = $data['identifier'] ?? null;
        $this->password = $data['password'] ?? null;
    }

    public function validate()
    {
        $errors = [];

        if (empty($this->identifier)) {
            $errors['identifier'] = 'Email or phone is required';
        }

        if (empty($this->password)) {
            $errors['password'] = 'Password is required';
        }

        return $errors;
    }
}