<?php

namespace App\Core;

class Indexer
{
    private static $indexFile = 'index.json';

    public static function all(): array
    {
        $raw = Store::load(self::$indexFile, 'cache'); // Use cache store type
        if (!$raw) {
            return self::rebuild();
        }
        return json_decode($raw, true) ?? [];
    }

    public static function update(string $filename, array $meta)
    {
        $index = self::all();
        
        // Prepare simplified meta for index
        $entry = [
            'file' => $filename,
            'title' => $meta['title'] ?? 'Untitled',
            'date' => $meta['date'] ?? date('Y-m-d'),
            'status' => $meta['status'] ?? 'draft',
            'tags' => $meta['tags'] ?? [], // New Taxonomy
            'category' => $meta['category'] ?? 'Uncategorized', // New Taxonomy
            'image' => $meta['image'] ?? null, // For OpenGraph later
            'description' => $meta['description'] ?? null // For SEO
        ];

        // Update or Add
        $found = false;
        foreach ($index as &$item) {
            if ($item['file'] === $filename) {
                $item = $entry;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $index[] = $entry;
        }

        // Sort by date desc
        usort($index, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));

        self::save($index);
    }

    public static function delete(string $filename)
    {
        $index = self::all();
        $index = array_filter($index, fn($i) => $i['file'] !== $filename);
        self::save(array_values($index));
    }

    public static function rebuild(): array
    {
        $files = Store::list('content');
        $index = [];

        foreach ($files as $file) {
            $raw = Store::load($file, 'content');
            $parsed = Parser::parse($raw);
            $meta = $parsed['meta'];
            
            $index[] = [
                'file' => $file,
                'title' => $meta['title'] ?? 'Untitled',
                'date' => $meta['date'] ?? date('Y-m-d'),
                'status' => $meta['status'] ?? 'draft',
                'tags' => $meta['tags'] ?? [],
                'category' => $meta['category'] ?? 'Uncategorized',
                'image' => $meta['image'] ?? null,
                'description' => $meta['description'] ?? null
            ];
        }

        usort($index, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
        self::save($index);
        
        return $index;
    }

    private static function save(array $index)
    {
        // Store in storage/cache/index.json (encrypted)
        // Ensure cache dir exists (created in install)
        Store::save(self::$indexFile, json_encode($index), 'cache');
    }
}
