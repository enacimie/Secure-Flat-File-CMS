<?php

namespace App\Core;

class Auth
{
    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return !empty($_SESSION['logged_in']);
    }

    public static function login(string $user, string $pass): bool
    {
        $json = Store::load('site.json', 'config');
        $config = $json ? json_decode($json, true) : [];
        
        $validUser = $config['admin_user'] ?? 'admin';
        // Default hash for 'admin' if not set
        $validPassHash = $config['admin_pass'] ?? password_hash('admin', PASSWORD_DEFAULT);

        if ($user === $validUser && password_verify($pass, $validPassHash)) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['logged_in'] = true;
            return true;
        }
        return false;
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
    }
    
    public static function require()
    {
        if (!self::check()) {
            // Render login form via Controller logic or redirect?
            // To keep it clean, we redirect to a login route or handle it in the caller.
            // For now, let's just return false, caller handles redirection.
            return false;
        }
        return true;
    }
}
