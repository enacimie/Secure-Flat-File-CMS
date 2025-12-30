<?php

namespace App\Core;

class Security
{
    private static $key;
    private const METHOD = 'AES-256-GCM'; // New Standard
    private const LEGACY_METHOD = 'AES-256-CBC';

    private static function loadKey()
    {
        if (!self::$key) {
            $path = __DIR__ . '/../../key.php';
            if (!file_exists($path)) {
                throw new \Exception("Error Crítico: Clave de encriptación no encontrada en key.php");
            }
            self::$key = require $path;
        }
    }

    public static function encrypt(string $data): string
    {
        self::loadKey();
        $ivLength = openssl_cipher_iv_length(self::METHOD); // 12 bytes for GCM
        $iv = openssl_random_pseudo_bytes($ivLength);
        
        $tag = ""; // Passed by reference
        $encrypted = openssl_encrypt($data, self::METHOD, self::$key, 0, $iv, $tag);
        
        // Format: GCM|IV|TAG|CIPHERTEXT (Base64 encoded parts or combined)
        // Let's combine raw bytes and base64 the whole thing for storage simplicity
        // Structure: IV (12) . Tag (16) . Ciphertext
        return 'GCM|' . base64_encode($iv . $tag . $encrypted);
    }

    public static function decrypt(string $data): ?string
    {
        self::loadKey();

        // Check for GCM Header
        if (strpos($data, 'GCM|') === 0) {
            $payload = base64_decode(substr($data, 4));
            
            $ivLength = openssl_cipher_iv_length(self::METHOD);
            $iv = substr($payload, 0, $ivLength);
            $tag = substr($payload, $ivLength, 16);
            $ciphertext = substr($payload, $ivLength + 16);
            
            return openssl_decrypt($ciphertext, self::METHOD, self::$key, 0, $iv, $tag) ?: null;
        }

        // Fallback to Legacy CBC
        $decoded = base64_decode($data);
        $ivLength = openssl_cipher_iv_length(self::LEGACY_METHOD); // 16 bytes
        
        if (strlen($decoded) < $ivLength) return null;
        
        $iv = substr($decoded, 0, $ivLength);
        $encrypted = substr($decoded, $ivLength);
        
        return openssl_decrypt($encrypted, self::LEGACY_METHOD, self::$key, 0, $iv) ?: null;
    }

    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrfToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token']) || empty($token)) return false;
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function checkRateLimit(string $ip)
    {
        $file = __DIR__ . '/../../storage/cache/rate_' . md5($ip) . '.txt';
        $limit = 5;
        $timeWindow = 900; // 15 minutes

        $data = file_exists($file) ? explode('|', file_get_contents($file)) : [0, 0];
        $attempts = (int)$data[0];
        $lastTime = (int)$data[1];

        // Reset if window passed
        if (time() - $lastTime > $timeWindow) {
            $attempts = 0;
        }

        if ($attempts >= $limit) {
            header("HTTP/1.1 429 Too Many Requests");
            die("Too many login attempts. Please try again in 15 minutes.");
        }

        // Increment (caller must call saveRateLimit if auth fails, but simply incrementing on check is safer/easier for strict limiting)
        // Wait, we only want to increment on FAILURE. But checking happens before.
        // Let's split: check() and registerFail().
    }

    public static function registerLoginFail(string $ip)
    {
        $file = __DIR__ . '/../../storage/cache/rate_' . md5($ip) . '.txt';
        $data = file_exists($file) ? explode('|', file_get_contents($file)) : [0, 0];
        
        // Update
        $attempts = $data[0] + 1;
        file_put_contents($file, "$attempts|" . time());
    }
}
