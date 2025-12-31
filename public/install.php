<?php
// Installer for Secure CMS

// Security Check: Prevent re-installation
if (file_exists(__DIR__ . '/../key.php')) {
    http_response_code(403);
    die("<h1>System Locked</h1><p>The system is already installed. To reinstall, you must manually delete <code>key.php</code> from the root directory.</p>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = $_POST['site_name'] ?? 'My Secure CMS';
    $user = $_POST['user'] ?? 'admin';
    $pass = $_POST['pass'] ?? 'admin';

    // 1. Generate Key
    try {
        $key = bin2hex(openssl_random_pseudo_bytes(16)); // 32 chars hex = 16 bytes? No, AES-256 needs 32 bytes key.
        // wait, openssl_encrypt with AES-256-CBC needs 32 bytes string?
        // openssl_random_pseudo_bytes(32) gives 32 raw bytes.
        // Our Security class loads the file which returns a string.
        // Let's match the original seed command: openssl rand -hex 16 => 32 chars.
        // Actually AES-256 key length is 32 bytes. 
        // If we use a hex string as key, it depends if we pack it or use it as password.
        // Looking at Security.php: openssl_encrypt($data, 'AES-256-CBC', $key, ...)
        // If $key is shorter, it pads? If longer?
        // Standard is 32 bytes raw. 
        // Let's generate a robust random string.
        $key = bin2hex(random_bytes(16)); // 32 chars
    } catch (Exception $e) {
        die("Error generating key: " . $e->getMessage());
    }

    $keyContent = "<?php return '$key';";
    if (file_put_contents(__DIR__ . '/../key.php', $keyContent) === false) {
        $error = "Could not write key.php. Check permissions.";
    } else {
        // 2. Initialize Directories
        $dirs = [
            '../storage/content',
            '../storage/config',
            '../storage/history',
            '../storage/media',
            '../storage/messages',
            '../storage/cache'
        ];
        foreach ($dirs as $dir) {
            if (!is_dir(__DIR__ . '/' . $dir)) mkdir(__DIR__ . '/' . $dir, 0755, true);
        }

        // 3. Helper for Encryption (Inline to avoid dependencies)
        function install_encrypt($data, $k) {
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
            $encrypted = openssl_encrypt($data, 'AES-256-CBC', $k, 0, $iv);
            return base64_encode($iv . $encrypted);
        }

        // 4. Create Config
        $config = [
            'site_name' => $siteName,
            'admin_user' => $user,
            'admin_pass' => password_hash($pass, PASSWORD_DEFAULT),
            'theme' => 'default',
            'active_plugins' => []
        ];
        file_put_contents(__DIR__ . '/../storage/config/site.json', install_encrypt(json_encode($config), $key));

        // 5. Create Home
        $homeContent = "---\ntitle: Welcome\nstatus: published\ndate: ".date('Y-m-d')."\n---\n\n# Welcome to your new CMS\n\nThis is a secure, encrypted flat-file content management system.\n\nGo to `/admin` to start editing.";
        file_put_contents(__DIR__ . '/../storage/content/home.md', install_encrypt($homeContent, $key));

        // 6. Create Blocks
        $blocks = [
            'header' => [['type'=>'link', 'text'=>'Home', 'url'=>'/']],
            'footer' => [['type'=>'raw', 'body'=>'<small>&copy; '.date('Y').' '.$siteName.'</small>']]
        ];
        file_put_contents(__DIR__ . '/../storage/config/blocks.json', install_encrypt(json_encode($blocks), $key));

        // 7. Cleanup
        @unlink(__FILE__); // Self-destruct for security

        header('Location: /');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Secure Flat-File CMS</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Secure Flat-File CMS Installation</h1>
            <p class="text-gray-500 text-sm mt-2">Setup your encrypted environment</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="post" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Site Name</label>
                <input type="text" name="site_name" value="My Secure Site" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Admin Username</label>
                <input type="text" name="user" value="admin" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Admin Password</label>
                <input type="password" name="pass" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="bg-yellow-50 p-4 rounded text-xs text-yellow-800">
                <strong>Notice:</strong> Upon installation, a <code>key.php</code> file will be generated. Do not lose this file, or your content will be unreadable.
            </div>

            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Install & Encrypt
            </button>
        </form>
    </div>
</body>
</html>
