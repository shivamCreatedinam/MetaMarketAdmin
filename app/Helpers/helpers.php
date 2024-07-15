<?php

if (!function_exists("generateOTP")) {
    function generateOTP($digit = 6)
    {
        if ($digit < 1) {
            throw new InvalidArgumentException('The number of digits must be at least 1');
        }

        $otp = '';
        for ($i = 0; $i < $digit; $i++) {
            $otp .= mt_rand(0, 9);
        }

        return $otp;
    }
}

if (!function_exists('getNameLetters')) {
    function getNameLetters($name)
    {
        $initials = preg_replace_callback('/\b\w/u', function ($matches) {
            return strtoupper($matches[0]);
        }, $name);
        return str_replace(' ', '', $initials);
    }
}
