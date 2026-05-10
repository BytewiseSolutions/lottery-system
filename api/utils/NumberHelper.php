<?php

class NumberHelper
{
    public static function money($amount, $currency = 'M')
    {
        return $currency . number_format($amount, 2);
    }

    public static function clean($number)
    {
        return preg_replace('/[^0-9]/', '', $number);
    }

    public static function random($length = 6)
    {
        return substr(str_shuffle('0123456789'), 0, $length);
    }

    public static function percentage($part, $total)
    {
        if ($total == 0) return 0;
        return round(($part / $total) * 100, 2);
    }

    public static function format($number)
    {
        return number_format($number);
    }
}