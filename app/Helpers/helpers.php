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
