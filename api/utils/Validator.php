<?php

class Validator
{
    public static function user($dto)
    {
        $errors = [];

        if (empty($dto->first_name)) {
            $errors['first_name'] = 'First name is required';
        } elseif (strlen($dto->first_name) < MIN_NAME_LENGTH) {
            $errors['first_name'] = 'Too short';
        }

        if (empty($dto->last_name)) {
            $errors['last_name'] = 'Last name is required';
        }

        if (empty($dto->email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($dto->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email';
        }

        if (!empty($dto->phone) && strlen($dto->phone) > MAX_PHONE_LENGTH) {
            $errors['phone'] = 'Phone too long';
        }

        if (empty($dto->password)) {
            $errors['password'] = 'Password required';
        }

        if ($dto->password !== $dto->confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        if (!in_array($dto->role, [ROLE_USER, ROLE_ADMIN])) {
            $errors['role'] = 'Invalid role';
        }

        return $errors;
    }
}