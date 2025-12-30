<?php

namespace App\Core;

use Parsedown;

class BlockManager
{
    private $blocks = [];
    private $parsedown;

    public function __construct()
    {
        $json = Store::load('blocks.json', 'config');
        $this->blocks = $json ? json_decode($json, true) : [];
        $this->parsedown = new Parsedown();
    }

    public function has(string $zone): bool
    {
        return !empty($this->blocks[$zone]);
    }

    public function render(string $zone): string
    {
        if (empty($this->blocks[$zone])) return '';

        $html = '';
        foreach ($this->blocks[$zone] as $block) {
            $html .= $this->renderBlock($block);
        }
        return $html;
    }

    private function renderBlock(array $block): string
    {
        $type = $block['type'] ?? 'content';

        switch ($type) {
            case 'link':
                $text = htmlspecialchars($block['text'] ?? '');
                $url = htmlspecialchars($block['url'] ?? '#');
                $target = !empty($block['new_tab']) ? 'target="_blank"' : '';
                return "<a href=\"$url\" $target class=\"hover:text-primary transition-colors\">$text</a>";

            case 'content':
                $body = $block['body'] ?? '';
                return '<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 prose prose-sm max-w-none">' . $this->parsedown->text($body) . '</div>';
            
            case 'raw':
                return $block['body'] ?? '';

            default:
                return '';
        }
    }
    
    // Helper to get raw array for custom iteration in template if needed
    public function get(string $zone): array
    {
        return $this->blocks[$zone] ?? [];
    }
}
