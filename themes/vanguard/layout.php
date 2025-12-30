<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | <?= $site['site_name'] ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Roboto', 'sans-serif'], display: ['Oswald', 'sans-serif'] },
                    colors: { accent: '#e11d48' } // Red-600
                }
            }
        }
    </script>
    <?php if(isset($hook)) ($hook)::call('head'); ?>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <!-- Top Bar -->
    <div class="bg-black text-white text-xs py-2">
        <div class="max-w-7xl mx-auto px-4 flex justify-between">
            <span><?= date('l, F j, Y') ?></span>
            <div class="space-x-4">
                <a href="#" class="hover:text-accent">Subscribe</a>
                <?php if(isset($is_admin) && $is_admin): ?><a href="/admin" class="text-accent">Admin Panel</a><?php else: ?><a href="/admin">Login</a><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-sm border-b-4 border-accent sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">
            <a href="/" class="text-4xl font-display font-bold uppercase tracking-tighter text-gray-900">
                <?= $site['site_name'] ?><span class="text-accent">.</span>
            </a>
            <nav class="hidden md:flex space-x-8 font-display font-bold uppercase text-sm tracking-wide">
                <?php foreach ($blocks->get('header') as $link): ?>
                    <a href="<?= $link['url'] ?>" class="hover:text-accent transition"><?= $link['text'] ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
    </header>

    <!-- Main -->
    <main class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Content Column -->
        <div class="lg:col-span-8">
            <div class="bg-white p-6 md:p-10 shadow-sm border border-gray-200">
                
                <?php if($_SERVER['REQUEST_URI'] !== '/'): ?>
                    <header class="mb-6 pb-6 border-b border-gray-100">
                        <?php if(isset($meta['category'])): ?>
                            <span class="bg-accent text-white text-xs font-bold px-2 py-1 uppercase mb-3 inline-block"><?= $meta['category'] ?></span>
                        <?php endif; ?>
                        <h1 class="text-3xl md:text-5xl font-display font-bold leading-tight mb-4"><?= $title ?></h1>
                        <div class="text-sm text-gray-500 flex items-center gap-4">
                            <span>By <strong>Admin</strong></span>
                            <span>&bull;</span>
                            <time><?= $meta['date'] ?? date('Y-m-d') ?></time>
                        </div>
                    </header>
                    <?php if(isset($meta['image'])): ?>
                        <img src="<?= $meta['image'] ?>" class="w-full h-auto mb-8 object-cover max-h-[500px]">
                    <?php endif; ?>
                <?php endif; ?>

                <article class="prose prose-lg prose-red max-w-none font-serif">
                    <?= $content ?>
                </article>

                <!-- Share/Tags -->
                <?php if(!empty($meta['tags'])): ?>
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <span class="font-bold text-sm mr-2">Topics:</span>
                        <?php foreach($meta['tags'] as $tag): ?>
                            <a href="#" class="text-accent text-sm font-bold uppercase hover:underline mr-3">#<?= $tag ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="lg:col-span-4 space-y-8">
            <!-- Widget -->
            <div class="bg-white p-6 shadow-sm border border-gray-200">
                <h3 class="font-display font-bold text-lg uppercase border-l-4 border-accent pl-3 mb-4">Latest News</h3>
                <ul class="space-y-4">
                    <!-- Simulamos últimos posts (en real usaríamos Indexer::all) -->
                    <li class="flex gap-4 group cursor-pointer">
                        <div class="w-20 h-20 bg-gray-200 flex-shrink-0"></div>
                        <div>
                            <h4 class="font-bold text-sm leading-snug group-hover:text-accent">Top 10 Security Tips for 2025</h4>
                            <span class="text-xs text-gray-500">2 hours ago</span>
                        </div>
                    </li>
                </ul>
            </div>
            
            <!-- Ad Placeholder -->
            <div class="bg-gray-200 h-64 flex items-center justify-center text-gray-400 font-bold text-sm">
                ADVERTISEMENT
            </div>
        </aside>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 border-t-4 border-accent mt-12">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8 text-sm">
            <div>
                <h4 class="text-white font-display font-bold uppercase mb-4"><?= $site['site_name'] ?></h4>
                <p><?= $site['site_description'] ?></p>
            </div>
            <div>
                <h4 class="text-white font-display font-bold uppercase mb-4">Links</h4>
                <ul class="space-y-2">
                    <?php foreach ($blocks->get('footer') as $item): ?>
                        <li><?= $item['body'] ?? $item['text'] ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div>
                 <p>&copy; <?= date('Y') ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
