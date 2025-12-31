<?php

namespace App\Core;

use Parsedown;
use DateTime;

class Controller
{
    private $config;

    public function __construct()
    {
        $json = Store::load('site.json', 'config');
        $this->config = $json ? json_decode($json, true) : [];
    }

    public function index()
    {
        $this->page('home');
    }

    public function page($slug)
    {
        if (preg_match('/\.\./', $slug)) die('Invalid path');
        
        // --- Cache Layer ---
        $cacheDir = __DIR__ . '/../../storage/cache/public';
        $cacheFile = $cacheDir . '/' . md5($slug) . '.html';
        $sourceTime = Store::getMTime($slug . '.md', 'content');
        
        // Don't cache for admin or if query params exist
        $shouldCache = !Auth::check() && empty($_GET);

        if ($shouldCache && file_exists($cacheFile) && $sourceTime > 0 && filemtime($cacheFile) >= $sourceTime) {
            readfile($cacheFile);
            echo "<!-- Cached Strategy -->";
            return;
        }
        // -------------------

        $raw = Store::load($slug . '.md', 'content');
        
        if (!$raw) {
            $this->render404();
            return;
        }

        $parsed = Parser::parse($raw);
        $meta = $parsed['meta'];
        $mdContent = $parsed['content'];

        $is_admin = Auth::check();
        $status = $meta['status'] ?? 'published'; 

        if ($status === 'draft' && !$is_admin) {
            $this->render404();
            return;
        }

        $mdContent = Hook::call('content_raw', $mdContent);
        $processedContent = Shortcode::parse($mdContent);
        $htmlContent = (new Parsedown())->text($processedContent);
        $finalContent = Hook::call('content_html', $htmlContent);

        // Inject SEO Meta Tags via Hook 'head'
        Hook::add('head', function() use ($meta, $slug) {
            $title = $meta['title'] ?? ucfirst($slug);
            $desc = htmlspecialchars($meta['description'] ?? $this->config['site_description'] ?? '');
            $img = htmlspecialchars($meta['image'] ?? '');
            $siteName = htmlspecialchars($this->config['site_name'] ?? '');
            $tit = htmlspecialchars($title);
            
            echo "\n    <!-- Auto SEO -->";
            echo "\n    <meta name=\"description\" content=\"$desc\">";
            echo "\n    <meta property=\"og:title\" content=\"$tit\">";
            echo "\n    <meta property=\"og:site_name\" content=\"$siteName\">";
            echo "\n    <meta property=\"og:description\" content=\"$desc\">";
            if ($img) echo "\n    <meta property=\"og:image\" content=\"$img\">";
            echo "\n    <meta property=\"og:type\" content=\"article\">\n";
        });

        // Capture Output for Cache
        if ($shouldCache) ob_start();

        View::render('layout', [
            'title' => $meta['title'] ?? ucfirst($slug),
            'meta' => $meta,
            'content' => $finalContent,
            'site' => $this->config,
            'is_admin' => $is_admin
        ], $this->config);

        if ($shouldCache) {
            $html = ob_get_clean();
            if (!is_dir($cacheDir)) mkdir($cacheDir, 0755, true);
            file_put_contents($cacheFile, $html);
            echo $html;
        }
    }

    public function admin()
    {
        // Handle 2FA Verification
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['2fa_code'])) {
            if (!Security::validateCsrfToken($_POST['csrf'] ?? '')) die("CSRF Error");
            
            if (empty($_SESSION['2fa_pending_user'])) {
                header('Location: /admin');
                exit;
            }

            $secret = $this->config['admin_2fa_secret'] ?? '';
            if (TwoFactor::verify($secret, $_POST['2fa_code'])) {
                // Success
                $_SESSION['user'] = $_SESSION['2fa_pending_user'];
                unset($_SESSION['2fa_pending_user']);
                session_regenerate_id(true);
                header('Location: /admin');
                exit;
            } else {
                Security::registerLoginFail($_SERVER['REMOTE_ADDR']);
                View::render('login', [
                    'error' => 'Invalid Code', 
                    'step' => '2fa',
                    'csrf' => Security::generateCsrfToken()
                ], $this->config);
                return;
            }
        }

        // Handle Login POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user'])) {
            if (!Security::validateCsrfToken($_POST['csrf'] ?? '')) die("CSRF Error");
            
            Security::checkRateLimit($_SERVER['REMOTE_ADDR']);

            // Verify Password First
            $passHash = $this->config['admin_pass'];
            // Simple check for now (Auth::login logic inline-ish to support partial state)
            // But let's use Auth::validateCredentials if we had it, or replicate:
            if ($_POST['user'] === $this->config['admin_user'] && password_verify($_POST['pass'], $passHash)) {
                
                // Check if 2FA is enabled
                if (!empty($this->config['admin_2fa_secret'])) {
                    $_SESSION['2fa_pending_user'] = $_POST['user'];
                    View::render('login', [
                        'step' => '2fa',
                        'csrf' => Security::generateCsrfToken()
                    ], $this->config);
                    return;
                }

                // No 2FA -> Login Direct
                Auth::login($_POST['user'], $_POST['pass']); // We know it's valid
                header('Location: /admin');
                exit;
            } else {
                Security::registerLoginFail($_SERVER['REMOTE_ADDR']);
                View::render('login', [
                    'error' => 'Invalid credentials', 
                    'csrf' => Security::generateCsrfToken()
                ], $this->config);
                return;
            }
        }

        if (!Auth::check()) {
            View::render('login', ['csrf' => Security::generateCsrfToken()], $this->config);
            return;
        }

        // Dashboard Logic using Indexer
        $enrichedFiles = Indexer::all();
        $configs = Store::list('config');
        $msgCount = count(Store::list('messages'));

        // Search Filter
        if (!empty($_GET['q'])) {
            $q = strtolower($_GET['q']);
            $enrichedFiles = array_filter($enrichedFiles, function($f) use ($q) {
                return strpos(strtolower($f['title']), $q) !== false || strpos(strtolower($f['file']), $q) !== false;
            });
        }

        // Map 'file' to 'filename' for view compatibility
        $finalFiles = array_map(function($i) { $i['filename'] = $i['file']; return $i; }, $enrichedFiles);

        View::render('admin', [
            'title' => 'Dashboard',
            'files' => $finalFiles,
            'configs' => $configs,
            'msgCount' => $msgCount,
            'csrf' => Security::generateCsrfToken(),
            'flash' => $this->getFlash()
        ], $this->config);
    }

    public function logout()
    {
        Auth::logout();
        header('Location: /');
    }

    // --- CRUD ---

    public function create()
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Method");
        if (!Security::validateCsrfToken($_POST['csrf'] ?? '')) die("CSRF");

        $title = trim($_POST['title']);
        $slug = trim($_POST['slug'] ?: strtolower(preg_replace('/[^a-z0-9\-_]+/', '-', $title)));
        $slug = $slug ?: 'untitled';
        $filename = $slug . '.md';

        if (Store::load($filename, 'content')) {
            $this->setFlash('error', "Slug '$slug' exists.");
            header('Location: /admin');
            exit;
        }

        $date = date('Y-m-d');
        $content = "---
title: \"$title\"\nstatus: draft\ndate: $date
---

# $title

Start writing...";

        if (Store::save($filename, $content, 'content')) {
            // Update Index
            Indexer::update($filename, ['title' => $title, 'status' => 'draft', 'date' => $date]);
            $this->setFlash('success', "Page created.");
            header("Location: /admin/edit?file=$filename&type=content");
        } else {
            $this->setFlash('error', "Error creating.");
            header('Location: /admin');
        }
    }

    public function edit()
    {
        $this->requireAuth();
        $file = $_GET['file'] ?? 'home.md';
        $type = $_GET['type'] ?? 'content';
        $raw = Store::load($file, $type);

        if ($file === 'site.json' && $type === 'config') {
            View::render('settings', ['config' => json_decode($raw, true), 'csrf' => Security::generateCsrfToken()], $this->config);
            return;
        }
        if ($file === 'blocks.json' && $type === 'config' && !isset($_GET['mode'])) {
            View::render('blocks_editor', ['config' => json_decode($raw, true), 'csrf' => Security::generateCsrfToken()], $this->config);
            return;
        }

        $meta = [];
        $body = $raw;
        if ($type === 'content') {
            $parsed = Parser::parse($raw);
            $meta = $parsed['meta'];
            $body = $parsed['content'];
        }

        View::render('editor', [
            'file' => $file, 'type' => $type, 'meta' => $meta, 'content' => $body, 'csrf' => Security::generateCsrfToken()
        ], $this->config);
    }

    public function save()
    {
        $this->requireAuth();
        if (!Security::validateCsrfToken($_POST['csrf'] ?? '')) die("CSRF");

        $file = $_POST['file'];
        $type = $_POST['type'];

        if ($type === 'config_gui') {
            $data = json_decode(Store::load($file, 'config'), true) ?? [];
            $data['site_name'] = $_POST['site_name'];
            $data['site_description'] = $_POST['site_description'];
            $data['admin_user'] = $_POST['admin_user'];
            $data['admin_2fa_secret'] = $_POST['admin_2fa_secret'] ?? ''; // 2FA Secret
            if (!empty($_POST['admin_pass'])) $data['admin_pass'] = password_hash($_POST['admin_pass'], PASSWORD_DEFAULT);
            $content = json_encode($data, JSON_PRETTY_PRINT);
            $type = 'config';
        } elseif ($type === 'blocks_gui') {
            $data = [];
            foreach ($_POST['blocks'] ?? [] as $z => $b) $data[$z] = is_array($b) ? array_values($b) : [];
            $content = json_encode($data, JSON_PRETTY_PRINT);
            $type = 'config';
        } elseif ($type === 'config') {
            $content = $_POST['body'];
            if (json_decode($content) === null) {
                $this->setFlash('error', 'Invalid JSON');
                header("Location: /admin/edit?file=$file&type=$type");
                exit;
            }
        } else {
            $title = str_replace('"', '\"', $_POST['title'] ?? 'Untitled');
            $yaml = "---
title: \"$title\"\nstatus: {$_POST['status']}\ndate: {$_POST['date']}
---

";
            $content = $yaml . $_POST['body'];
            
            // Update Index
            Indexer::update($file, [
                'title' => $_POST['title'],
                'status' => $_POST['status'],
                'date' => $_POST['date']
            ]);
        }

        Store::save($file, $content, $type);
        $this->setFlash('success', "Saved $file");
        header('Location: /admin');
    }

    public function delete()
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Method");
        if (!Security::validateCsrfToken($_POST['csrf'] ?? '')) die("CSRF");
        
        $file = $_POST['file'];
        if (in_array($file, ['home.md', 'site.json'])) die("Protected");
        
        if (Store::delete($file, $_POST['type'])) {
            if ($_POST['type'] === 'content') Indexer::delete($file);
            $this->setFlash('success', "Deleted $file");
        } else {
            $this->setFlash('error', "Error deleting");
        }
        header('Location: /admin');
    }

    // --- Media ---

    public function upload()
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Method");
        if (!isset($_FILES['image'])) { http_response_code(400); die(json_encode(['error' => 'No image'])); }

        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) { http_response_code(400); die(json_encode(['error' => 'Invalid type'])); }

        $filename = uniqid('img_') . '.' . $ext;
        Store::save($filename, file_get_contents($file['tmp_name']), 'media');
        echo json_encode(['imageUrl' => '/media/' . $filename]);
    }

    public function media($filename)
    {
        $filename = basename($filename);
        $content = Store::load($filename, 'media');
        if (!$content) { http_response_code(404); die("Not found"); }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $types = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp'];
        header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
        header('Content-Length: ' . strlen($content));
        header('Cache-Control: public, max-age=86400');
        echo $content;
    }

    // --- History ---

    public function history()
    {
        $this->requireAuth();
        $file = $_GET['file'];
        $type = $_GET['type'] ?? 'content';
        $historyDir = __DIR__ . '/../../storage/history';
        $backups = [];
        if (is_dir($historyDir)) {
            foreach (scandir($historyDir) as $f) {
                if (strpos($f, $file . '.') === 0 && str_ends_with($f, '.bak')) {
                    if (preg_match('/\.($d{8}_$d{6})\.bak$/', $f, $matches)) {
                        $tsStr = $matches[1];
                        $date = DateTime::createFromFormat('Ymd_His', $tsStr);
                        $backups[] = ['file' => $f, 'date' => $date ? $date->format('M j, Y H:i:s') : $tsStr, 'raw_ts' => $tsStr];
                    }
                }
            }
        }
        usort($backups, fn($a, $b) => strcmp($b['raw_ts'], $a['raw_ts']));
        View::render('history', ['targetFile' => $file, 'type' => $type, 'backups' => $backups, 'csrf' => Security::generateCsrfToken()], $this->config);
    }

    public function restore()
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Method");
        if (!Security::validateCsrfToken($_POST['csrf'] ?? '')) die("CSRF");

        $versionFile = $_POST['version_file'];
        $targetFile = $_POST['target_file'];
        $type = $_POST['type'];
        $historyPath = __DIR__ . '/../../storage/history/' . $versionFile;
        if (!file_exists($historyPath) || strpos($targetFile, '..') !== false) die("Error");

        if (copy($historyPath, __DIR__ . '/../../storage/' . $type . '/' . $targetFile)) {
            // Re-index if content
            if ($type === 'content') {
                $raw = Store::load($targetFile, 'content');
                $parsed = Parser::parse($raw);
                Indexer::update($targetFile, [
                    'title' => $parsed['meta']['title'] ?? 'Restored',
                    'status' => $parsed['meta']['status'] ?? 'draft',
                    'date' => $parsed['meta']['date'] ?? date('Y-m-d')
                ]);
            }
            $this->setFlash('success', "Restored version.");
        } else {
            $this->setFlash('error', "Restore failed.");
        }
        header("Location: /admin/edit?file=$targetFile&type=$type");
    }

    // --- Extensions ---

    public function extensions()
    {
        $this->requireAuth();
        $plugins = [];
        foreach (glob(__DIR__ . '/../../plugins/*', GLOB_ONLYDIR) as $dir) {
            $meta = json_decode(file_get_contents($dir . '/plugin.json'), true);
            $meta['id'] = basename($dir);
            // Load README if exists
            if (file_exists($dir . '/README.md')) {
                $meta['readme'] = (new Parsedown())->text(file_get_contents($dir . '/README.md'));
            } else {
                $meta['readme'] = null;
            }
            $plugins[] = $meta;
        }
        $themes = [];
        foreach (glob(__DIR__ . '/../../themes/*', GLOB_ONLYDIR) as $dir) {
            $meta = json_decode(file_get_contents($dir . '/theme.json'), true) ?? ['name' => basename($dir)];
            $meta['id'] = basename($dir);
            $themes[] = $meta;
        }
        View::render('extensions', [
            'plugins' => $plugins, 'themes' => $themes,
            'active_plugins' => $this->config['active_plugins'] ?? [],
            'current_theme' => $this->config['theme'] ?? 'default',
            'csrf' => Security::generateCsrfToken(),
            'flash' => $this->getFlash()
        ], $this->config);
    }

    public function togglePlugin() {
        $this->requireAuth();
        $active = $this->config['active_plugins'] ?? [];
        $id = $_POST['plugin_id'];
        if ($_POST['action'] === 'activate' && !in_array($id, $active)) $active[] = $id;
        if ($_POST['action'] === 'deactivate') $active = array_diff($active, [$id]);
        $this->config['active_plugins'] = array_values($active);
        Store::save('site.json', json_encode($this->config, JSON_PRETTY_PRINT), 'config');
        header('Location: /admin/extensions');
    }

    public function setTheme() {
        $this->requireAuth();
        $this->config['theme'] = $_POST['theme_id'];
        Store::save('site.json', json_encode($this->config, JSON_PRETTY_PRINT), 'config');
        header('Location: /admin/extensions');
    }

    public function submitContact()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Method");
        
        // Anti-Spam: Honeypot
        if (!empty($_POST['website_url'])) {
            header("Location: " . $_SERVER['HTTP_REFERER'] . "?sent=true");
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf'] ?? '')) die("CSRF Error");
        
        // Dynamic Data Capture
        $data = $_POST;
        // Clean system fields
        unset($data['csrf'], $data['website_url']);
        
        $data['submitted_at'] = date('Y-m-d H:i:s');
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'];

        Store::save('msg_' . time() . '.json', json_encode($data, JSON_PRETTY_PRINT), 'messages');
        
        // Redirect back to the same page
        $referer = strtok($_SERVER['HTTP_REFERER'], '?');
        header("Location: $referer?sent=true");
    }
    
    public function inbox()
    {
        $this->requireAuth();
        $msgs = [];
        foreach (Store::list('messages') as $f) {
            $m = json_decode(Store::load($f, 'messages'), true);
            $m['filename'] = $f;
            $msgs[] = $m;
        }
        usort($msgs, fn($a, $b) => $b['date'] <=> $a['date']);
        View::render('inbox', ['messages' => $msgs, 'csrf' => Security::generateCsrfToken()], $this->config);
    }

    public function search()
    {
        $q = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        
        $results = [];
        if ($q) {
            $all = Indexer::all();
            $results = array_filter($all, function($item) use ($q) {
                if (($item['status'] ?? 'published') !== 'published') return false;
                // Search in title, file slug, or tags
                return stripos($item['title'], $q) !== false || 
                       stripos($item['file'], $q) !== false ||
                       in_array(strtolower($q), array_map('strtolower', $item['tags'] ?? []));
            });
        }
        
        // Pagination
        $total = count($results);
        $totalPages = ceil($total / $perPage);
        $results = array_slice($results, ($page - 1) * $perPage, $perPage);

        View::render('search', [
            'title' => "Search: " . htmlspecialchars($q),
            'query' => $q,
            'results' => $results,
            'pagination' => ['current' => $page, 'total' => $totalPages],
            'site' => $this->config,
            'is_admin' => Auth::check()
        ], $this->config);
    }

    public function mediaLibrary()
    {
        $this->requireAuth();
        $files = Store::list('media');
        $images = [];
        foreach($files as $f) {
            if ($f === '.gitkeep') continue;
            // Get size safely
            $path = __DIR__ . '/../../storage/media/' . $f;
            $size = file_exists($path) ? round(filesize($path) / 1024, 1) . ' KB' : '0 KB';
            
            $images[] = [
                'name' => $f,
                'url' => '/media/' . $f,
                'size' => $size
            ];
        }
        View::render('media_library', [
            'images' => $images,
            'csrf' => Security::generateCsrfToken()
        ], $this->config);
    }

    public function sitemap()
    {
        header("Content-Type: application/xml; charset=utf-8");
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domain = $protocol . $_SERVER['HTTP_HOST'];
        foreach (Indexer::all() as $item) {
            if (($item['status'] ?? 'published') === 'published') {
                $slug = str_replace('.md', '', $item['file']);
                $url = $domain . '/' . ($slug === 'home' ? '' : $slug);
                echo "<url><loc>$url</loc><lastmod>{$item['date']}</lastmod></url>";
            }
        }
        echo '</urlset>';
    }

    public function api($slug)
    {
        header('Content-Type: application/json');
        
        // Security: Prevent directory traversal
        if (preg_match('/\.\./', $slug)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid path']);
            return;
        }

        $raw = Store::load($slug . '.md', 'content');
        
        if (!$raw) {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
            return;
        }

        $parsed = Parser::parse($raw);
        $meta = $parsed['meta'];
        
        // Private content check
        if (($meta['status'] ?? 'published') === 'draft' && !Auth::check()) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        $mdContent = $parsed['content'];
        $mdContent = Hook::call('content_raw', $mdContent);
        $processedContent = Shortcode::parse($mdContent);
        $htmlContent = (new Parsedown())->text($processedContent);
        $finalContent = Hook::call('content_html', $htmlContent);

        echo json_encode([
            'meta' => $meta,
            'content' => $finalContent,
            'generated_at' => date('c')
        ]);
    }

    private function requireAuth() { if (!Auth::check()) { header('Location: /admin'); exit; } }
    private function render404() { http_response_code(404); View::render('layout', ['title'=>'404','content'=>'<h1>Not Found</h1>','meta'=>[],'site'=>$this->config,'is_admin'=>Auth::check()], $this->config); }
    private function setFlash($t, $m) { $_SESSION['flash'] = ['type' => $t, 'msg' => $m]; }
    private function getFlash() { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }
}