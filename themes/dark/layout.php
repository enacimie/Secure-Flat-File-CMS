<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Home' ?> - <?= $site['site_name'] ?? 'CMS' ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#22d3ee', // Cyan-400
                        secondary: '#94a3b8', // Slate-400
                        darkbg: '#0f172a', // Slate-900
                        darkcard: '#1e293b', // Slate-800
                    }
                }
            }
        }
    </script>
    <?php \App\Core\Hook::call('head'); ?>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .prose { color: #cbd5e1; }
        .prose h1, .prose h2, .prose h3, .prose h4 { color: #f1f5f9; }
        .prose strong { color: #22d3ee; }
        .prose a { color: #22d3ee; }
        .prose blockquote { border-left-color: #22d3ee; color: #94a3b8; }
    </style>
</head>
<body class="bg-darkbg text-gray-100 flex flex-col min-h-screen">

<header class="bg-darkcard border-b border-gray-700 shadow-lg sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-4">
                <a href="/" class="text-xl font-bold text-white tracking-wider hover:text-primary transition shadow-cyan-500/50">
                    <?= $site['site_name'] ?? 'Secure CMS' ?>
                </a>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-gray-800 text-cyan-400 border border-cyan-900">
                    CYBER.SEC
                </span>
            </div>
            
            <nav class="flex items-center gap-6 text-sm font-medium text-gray-400">
                <?= $blocks->render('header') ?>
                <?php if($is_admin): ?>
                    <a href="/admin" class="text-white hover:text-primary">Console</a>
                <?php else: ?>
                    <a href="/admin" class="hover:text-white">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>

<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <div class="flex flex-col md:flex-row gap-8 items-start">
        
        <?php if($blocks->has('sidebar_left')): ?>
            <aside class="w-full md:w-64 flex-shrink-0 space-y-8 text-gray-400">
                <?= $blocks->render('sidebar_left') ?>
            </aside>
        <?php endif; ?>

        <article class="flex-1 w-full min-w-0">
            <?php if(isset($meta['title'])): ?>
                <div class="mb-8 pb-6 border-b border-gray-800">
                    <h1 class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500 mb-4">
                        <?= htmlspecialchars($meta['title']) ?>
                    </h1>
                    <?php if(isset($meta['date'])): ?>
                        <time class="text-sm text-gray-500 font-mono">:: LOGGED <?= htmlspecialchars($meta['date']) ?></time>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="prose prose-lg prose-invert max-w-none">
                <?= $content ?>
            </div>
        </article>

        <?php if($blocks->has('sidebar_right')): ?>
            <aside class="w-full md:w-64 flex-shrink-0 space-y-6 text-gray-400">
                <?= $blocks->render('sidebar_right') ?>
            </aside>
        <?php endif; ?>

    </div>
</main>

<footer class="bg-darkcard border-t border-gray-800 mt-auto py-8">
    <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-600 font-mono">
        <?= $blocks->render('footer') ?>
    </div>
</footer>

<?php \App\Core\Hook::call('footer'); ?>
</body>
</html>
