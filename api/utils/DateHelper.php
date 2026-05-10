<?php

class DateHelper
{
    public static function now()
    {
        return date('Y-m-d H:i:s');
    }

    public static function format($date, $format = 'd M Y H:i')
    {
        return date($format, strtotime($date));
    }

    public static function addMinutes($date, $minutes)
    {
        return date('Y-m-d H:i:s', strtotime($date . " +{$minutes} minutes"));
    }

    public static function addDays($date, $days)
    {
        return date('Y-m-d H:i:s', strtotime($date . " +{$days} days"));
    }

    public static function isExpired($date, $minutes = 10)
    {
        $expiry = strtotime($date . " +{$minutes} minutes");
        return time() > $expiry;
    }

    public static function diffInMinutes($start, $end)
    {
        return (strtotime($end) - strtotime($start)) / 60;
    }
}