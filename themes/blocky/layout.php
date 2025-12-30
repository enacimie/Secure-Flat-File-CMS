<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Home' ?> - <?= $site['site_name'] ?? 'CMS' ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
    <?php \App\Core\Hook::call('head'); ?>
    <style>
        body { background-color: #f0f0f0; }
        .editor-canvas { background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 4rem; min-height: 100vh; }
        @media(max-width: 768px) { .editor-canvas { padding: 1.5rem; } }
    </style>
</head>
<body class="text-gray-900">

<!-- Top Bar (Minimal) -->
<header class="bg-white border-b border-gray-200 sticky top-0 z-10 h-14 flex items-center px-4 justify-between">
    <div class="flex items-center gap-4">
        <a href="/" class="font-bold text-gray-900 bg-black text-white px-2 py-1 rounded-sm">W</a>
        <span class="font-medium text-sm"><?= $site['site_name'] ?? 'Site' ?></span>
    </div>
    <nav class="flex items-center gap-4 text-sm">
        <?= $blocks->render('header') ?>
        <?php if($is_admin): ?>
            <a href="/admin" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Update</a>
        <?php endif; ?>
    </nav>
</header>

<main class="max-w-4xl mx-auto my-8 editor-canvas">
    
    <?php if(isset($meta['title'])): ?>
        <h1 class="text-5xl font-serif font-bold mb-8 text-gray-900 leading-tight">
            <?= htmlspecialchars($meta['title']) ?>
        </h1>
    <?php endif; ?>
    
    <div class="prose prose-xl max-w-none prose-headings:font-serif prose-p:font-sans prose-img:rounded-md">
        <?= $content ?>
    </div>

    <!-- Widgets inside the canvas flow, separated by HR -->
    <?php if($blocks->has('footer')): ?>
        <hr class="my-12 border-gray-200">
        <div class="text-center text-sm text-gray-500">
            <?= $blocks->render('footer') ?>
        </div>
    <?php endif; ?>

</main>

<?php \App\Core\Hook::call('footer'); ?>
</body>
</html>
