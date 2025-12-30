<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - <?= $site['site_name'] ?></title>
    
    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 900: '#1e3a8a' }
                    }
                }
            }
        }
    </script>
    <?php if(isset($hook)) ($hook)::call('head'); ?>
</head>
<body class="flex flex-col min-h-full font-sans text-slate-900 antialiased">

    <!-- Navigation -->
    <nav class="bg-slate-900 sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center gap-2">
                        <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center text-white font-bold">N</div>
                        <span class="font-bold text-xl text-white tracking-tight"><?= $site['site_name'] ?></span>
                    </a>
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <?php foreach ($blocks->get('header') as $link): ?>
                            <a href="<?= $link['url'] ?>" class="text-slate-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors"><?= $link['text'] ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex items-center">
                    <?php if(isset($is_admin) && $is_admin): ?>
                        <a href="/admin" class="ml-4 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-slate-900 bg-white hover:bg-slate-50">Admin Panel</a>
                    <?php else: ?>
                        <a href="/admin" class="text-slate-400 hover:text-white text-sm font-medium">Log in</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header / Hero -->
    <?php if($_SERVER['REQUEST_URI'] === '/'): ?>
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                    <span class="block"><?= $site['site_name'] ?></span>
                    <span class="block text-brand-600">Secure & Scalable</span>
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-slate-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    <?= $site['site_description'] ?>
                </p>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-grow bg-slate-50">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 sm:p-12">
                    <article class="prose prose-slate prose-lg max-w-none hover:prose-a:text-brand-600">
                        <?php if(isset($meta['image'])): ?>
                            <img src="<?= $meta['image'] ?>" alt="<?= $title ?>" class="w-full h-64 object-cover rounded-xl shadow-md mb-8">
                        <?php endif; ?>
                        
                        <!-- Title if not Home -->
                        <?php if($_SERVER['REQUEST_URI'] !== '/'): ?>
                            <h1 class="text-3xl font-bold text-slate-900 mb-4"><?= $title ?></h1>
                            <div class="flex items-center gap-4 text-sm text-slate-500 mb-8 border-b border-slate-100 pb-4">
                                <span><?= $meta['date'] ?? date('Y-m-d') ?></span>
                                <?php if(isset($meta['category'])): ?>
                                    <span class="px-2 py-1 bg-slate-100 rounded text-slate-600"><?= $meta['category'] ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?= $content ?>
                    </article>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto py-12 px-4 overflow-hidden sm:px-6 lg:px-8">
            <div class="flex justify-center space-x-6">
                <?php foreach ($blocks->get('footer') as $item): ?>
                    <div class="text-slate-400 hover:text-slate-500">
                        <?= $item['body'] ?? $item['text'] ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="mt-8 text-center text-base text-slate-400">
                &copy; <?= date('Y') ?> <?= $site['site_name'] ?>. All rights reserved. Powered by SecureCMS.
            </p>
        </div>
    </footer>

</body>
</html>
