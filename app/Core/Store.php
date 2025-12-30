<?php

namespace App\Core;

class Store
{
    private const STORAGE_PATH = __DIR__ . '/../../storage';

    public static function save(string $filename, string $content, string $type = 'content'): bool
    {
        $dir = self::STORAGE_PATH . '/' . $type;
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        
        $file = $dir . '/' . $filename;
        
        // Versioning: Si existe, lo movemos a history
        // (Nota: Esto podría mejorarse también con locking, pero por ahora lo mantenemos simple)
        if (file_exists($file)) {
            $historyDir = self::STORAGE_PATH . '/history';
            if (!is_dir($historyDir)) mkdir($historyDir, 0755, true);
            
            $timestamp = date('Ymd_His');
            copy($file, $historyDir . '/' . $filename . '.' . $timestamp . '.bak');
        }

        $encrypted = Security::encrypt($content);
        
        $fp = fopen($file, 'c+');
        if (flock($fp, LOCK_EX)) {
            ftruncate($fp, 0);
            fwrite($fp, $encrypted);
            fflush($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
            return true;
        } else {
            fclose($fp);
            return false;
        }
    }

    public static function load(string $filename, string $type = 'content'): ?string
    {
        $file = self::STORAGE_PATH . '/' . $type . '/' . $filename;
        if (!file_exists($file)) return null;

        $fp = fopen($file, 'rb');
        if (flock($fp, LOCK_SH)) {
            $encrypted = stream_get_contents($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
            return Security::decrypt($encrypted);
        } else {
            fclose($fp);
            return null;
        }
    }

    public static function getMTime(string $filename, string $type = 'content'): int
    {
        $file = self::STORAGE_PATH . '/' . $type . '/' . $filename;
        return file_exists($file) ? filemtime($file) : 0;
    }

    public static function list(string $type = 'content'): array
    {
        $dir = self::STORAGE_PATH . '/' . $type;
        if (!is_dir($dir)) return [];
        
        $files = array_diff(scandir($dir), ['.', '..']);
        return array_values($files); // Reindex
    }

    public static function delete(string $filename, string $type = 'content'): bool
    {
        $file = self::STORAGE_PATH . '/' . $type . '/' . $filename;
        if (file_exists($file)) {
             // Backup before delete is also good practice
            $historyDir = self::STORAGE_PATH . '/history';
            $timestamp = date('Ymd_His');
            copy($file, $historyDir . '/' . $filename . '.DELETED.' . $timestamp . '.bak');
            return unlink($file);
        }
        return false;
    }
}
