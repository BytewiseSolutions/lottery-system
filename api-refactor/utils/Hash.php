<?php

class Hash
{
    public static function make($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function check($password, $hash)
    {
        return self::verify($password, $hash);
    }

    public static function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public static function needsRehash($hash)
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }

    public static function random($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    public static function token($length = 64)
    {
        return self::random($length);
    }

    public static function numericCode($length = 6)
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= random_int(0, 9);
        }
        return $code;
    }
}
