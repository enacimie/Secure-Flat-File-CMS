<?php

namespace App\Core;

class PluginLoader
{
    public static function load()
    {
        $pluginDir = __DIR__ . '/../../plugins';
        if (!is_dir($pluginDir)) return;

        // Load configuration to check active plugins
        // We use Store directly here because Controller isn't ready yet
        $configRaw = Store::load('site.json', 'config');
        $config = $configRaw ? json_decode($configRaw, true) : [];
        $activePlugins = $config['active_plugins'] ?? [];

        $plugins = array_diff(scandir($pluginDir), ['.', '..']);

        foreach ($plugins as $plugin) {
            // Only load if in active_plugins array
            if (in_array($plugin, $activePlugins)) {
                $entryPoint = $pluginDir . '/' . $plugin . '/index.php';
                if (file_exists($entryPoint)) {
                    require_once $entryPoint;
                }
            }
        }
    }
}
