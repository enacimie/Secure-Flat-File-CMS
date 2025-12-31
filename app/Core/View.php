<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], array $config = [])
    {
        // Inject global helpers
        $data['blocks'] = new BlockManager();
        $data['hook'] = Hook::class; 
        
        $currentTheme = $config['theme'] ?? 'default';
        $defaultTheme = 'default';
        
        $themesDir = __DIR__ . '/../../themes';
        
        // 1. Try Current Theme
        $viewPath = $themesDir . '/' . $currentTheme . '/' . $view . '.php';
        
        // 2. Fallback to Default Theme
        if (!file_exists($viewPath)) {
            $viewPath = $themesDir . '/' . $defaultTheme . '/' . $view . '.php';
        }

        extract($data);
        
        // Hook to override view file
        $viewFile = Hook::call('view_file', $viewPath, $view);
        
        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            
            // Simple HTML Minification (Remove space between tags)
            echo preg_replace('/>\s+</', '><', $content);
        } else {
            die("Critical Error: View '$view' not found. Checked theme '$currentTheme' and '$defaultTheme'.");
        }
    }
}
