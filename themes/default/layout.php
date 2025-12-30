<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Home' ?> - <?= $site['site_name'] ?? 'CMS' ?></title>
    
    <!-- SEO & Open Graph -->
    <meta name="description" content="<?= htmlspecialchars($meta['description'] ?? $site['site_description'] ?? '') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($title ?? '') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta['description'] ?? $site['site_description'] ?? '') ?>">
    <?php if(!empty($meta['image'])): ?>
        <meta property="og:image" content="<?= htmlspecialchars($meta['image']) ?>">
    <?php endif; ?>
    <meta property="og:type" content="article">

    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb', // Blue-600
                        secondary: '#475569', // Slate-600
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom tweaks if needed */
        body { font-family: system-ui, -apple-system, sans-serif; }
    </style>
    <?php \App\Core\Hook::call('head'); ?>
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">

<?php if(isset($meta['status']) && $meta['status'] === 'draft'): ?>
    <div class="bg-yellow-300 text-yellow-900 text-center font-bold py-2 text-sm shadow-sm">
        ⚠️ PREVIEW MODE: This page is a DRAFT
    </div>
<?php endif; ?>

<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-4">
                <a href="/" class="text-xl font-bold text-gray-900 hover:text-primary transition">
                    <?= $site['site_name'] ?? 'Secure CMS' ?>
                </a>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    🔒 Encrypted
                </span>
            </div>
            
            <nav class="flex items-center gap-6 text-sm font-medium text-gray-600">
                <!-- Dynamic Menu -->
                <?= $blocks->render('header') ?>
                
                <?php if($is_admin): ?>
                    <div class="border-l pl-6 flex gap-4">
                        <a href="/admin" class="hover:text-primary">Dashboard</a>
                        <a href="/admin/logout" class="text-red-600 hover:text-red-800">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="/admin" class="hover:text-primary">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>

<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    <div class="flex flex-col md:flex-row gap-8 items-start">
        
        <!-- Left Sidebar -->
        <?php if($blocks->has('sidebar_left')): ?>
            <aside class="w-full md:w-64 flex-shrink-0 space-y-8">
                <?= $blocks->render('sidebar_left') ?>
            </aside>
        <?php endif; ?>

        <!-- Main Content -->
        <article class="flex-1 w-full min-w-0">
            <?php if(isset($meta['title'])): ?>
                <div class="mb-8 pb-6 border-b border-gray-100">
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl mb-2">
                        <?= htmlspecialchars($meta['title']) ?>
                    </h1>
                    <?php if(isset($meta['date'])): ?>
                        <time class="text-sm text-gray-500">Published: <?= htmlspecialchars($meta['date']) ?></time>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Markdown Content Rendered with Tailwind Typography -->
            <div class="prose prose-lg prose-blue max-w-none">
                <?= $content ?>
            </div>
        </article>

        <!-- Right Sidebar -->
        <?php if($blocks->has('sidebar_right')): ?>
            <aside class="w-full md:w-64 flex-shrink-0 space-y-6">
                <?= $blocks->render('sidebar_right') ?>
            </aside>
        <?php endif; ?>

    </div>
</main>

<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto py-8 px-4 text-center text-sm text-gray-500">
        <?= $blocks->render('footer') ?>
    </div>
</footer>

<?php \App\Core\Hook::call('footer'); ?>
</body>
</html>
