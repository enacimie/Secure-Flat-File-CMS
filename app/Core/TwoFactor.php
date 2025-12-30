<?php

namespace App\Core;

class TwoFactor
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generates a 16-character random Base32 secret key.
     */
    public static function generateSecret(): string
    {
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Verifies a QR code (TOTP).
     * Returns true if the code is valid for the given secret.
     */
    public static function verify(string $secret, string $code, int $window = 1): bool
    {
        if (strlen($code) !== 6) return false;
        
        $currentTime = floor(time() / 30); // 30 second slice

        // Check current time, plus/minus window (to allow slight clock drift)
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::getCode($secret, $currentTime + $i), $code)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Calculates the code for a specific time slice.
     */
    private static function getCode(string $secret, int $timeSlice): string
    {
        $binaryKey = self::base32Decode($secret);
        $binaryTime = pack('N*', 0) . pack('N*', $timeSlice);
        
        $hash = hash_hmac('sha1', $binaryTime, $binaryKey, true);
        $offset = ord($hash[19]) & 0xf;
        
        $packed = (
            ((ord($hash[$offset+0]) & 0x7f) << 24) |
            ((ord($hash[$offset+1]) & 0xff) << 16) |
            ((ord($hash[$offset+2]) & 0xff) << 8)  |
            ((ord($hash[$offset+3]) & 0xff))
        );
        
        $otp = $packed % 1000000;
        return str_pad((string)$otp, 6, '0', STR_PAD_LEFT);
    }

    private static function base32Decode(string $secret): string
    {
        $secret = strtoupper($secret);
        $binary = '';
        $buffer = 0;
        $bufferSize = 0;

        for ($i = 0; $i < strlen($secret); $i++) {
            $char = $secret[$i];
            $position = strpos(self::BASE32_CHARS, $char);
            if ($position === false) continue;

            $buffer = ($buffer << 5) | $position;
            $bufferSize += 5;

            if ($bufferSize >= 8) {
                $bufferSize -= 8;
                $binary .= chr(($buffer >> $bufferSize) & 0xFF);
            }
        }
        return $binary;
    }
}
