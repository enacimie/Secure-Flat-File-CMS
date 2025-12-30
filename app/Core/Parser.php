<?php

namespace App\Core;

class Parser
{
    public static function parse(string $rawContent): array
    {
        $meta = [];
        $content = $rawContent;

        // Regex para buscar bloque entre --- y ---
        // Allow optional whitespace after ---
        if (preg_match('/^---\s*[\r\n]+(.*?)[\r\n]+---\s*[\r\n]+(.*)$/s', $rawContent, $matches)) {
            $rawMeta = $matches[1];
            $content = $matches[2];

            // Parsear lineas "Key: Value"
            $lines = preg_split('/[\r\n]+/', $rawMeta);
            foreach ($lines as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $meta[trim($key)] = trim($value);
                }
            }
        }

        return [
            'meta' => $meta,
            'content' => $content
        ];
    }
}

