<?php
// Start session globally if not started
if (session_status() === PHP_SESSION_NONE) session_start();

// Installer Check
if (!file_exists(__DIR__ . '/../key.php')) {
    require __DIR__ . '/install.php';
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Controller;
use App\Core\PluginLoader;

// Cargar Plugins
PluginLoader::load();

// Instanciar Aplicación
$app = new Controller();
$router = new Router();

// Definir Rutas
$router->get('/', [$app, 'index']);

// Admin Routes
$router->get('/admin', [$app, 'admin']);
$router->post('/admin', [$app, 'admin']); // Para el login
$router->get('/admin/logout', [$app, 'logout']);
$router->get('/admin/inbox', [$app, 'inbox']); // New Inbox Route
$router->get('/admin/extensions', [$app, 'extensions']); // Extensions Manager
$router->post('/admin/extensions/toggle', [$app, 'togglePlugin']);
$router->post('/admin/extensions/theme', [$app, 'setTheme']);
$router->post('/admin/upload', [$app, 'upload']); // Media Upload
$router->get('/media', [$app, 'media']); // Media Serving
$router->get('/admin/edit', [$app, 'edit']);
$router->post('/admin/create', [$app, 'create']);
$router->post('/admin/save', [$app, 'save']);
$router->post('/admin/delete', [$app, 'delete']);
// History
$router->get('/admin/history', [$app, 'history']);
$router->post('/admin/restore', [$app, 'restore']);

// Public Form Submissions
$router->post('/contact', [$app, 'submitContact']);

// SEO
$router->get('/sitemap.xml', [$app, 'sitemap']);

// API (Headless)
$router->get('/api/content', [$app, 'api']);

// Ruta comodín para páginas MD (ej: /contacto -> contacto.md)
$router->setFallback([$app, 'page']);

// Ejecutar
$router->resolve();
