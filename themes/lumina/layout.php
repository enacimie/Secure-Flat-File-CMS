<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { dark: '#0f172a', surface: '#1e293b' },
                    backgroundImage: { 'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))' }
                }
            }
        }
    </script>
    <style>body { background: #0f172a; color: #e2e8f0; }</style>
    <?php if(isset($hook)) ($hook)::call('head'); ?>
</head>
<body class="min-h-screen flex flex-col bg-dark text-slate-300 antialiased selection:bg-purple-500 selection:text-white">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-dark/80 backdrop-blur-md border-b border-white/10">
        <div class="max-w-6xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="/" class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-400 to-cyan-400">
                <?= $site['site_name'] ?>
            </a>
            <div class="hidden md:flex space-x-8 text-sm font-medium">
                <?php foreach ($blocks->get('header') as $link): ?>
                    <a href="<?= $link['url'] ?>" class="hover:text-cyan-400 transition"><?= $link['text'] ?></a>
                <?php endforeach; ?>
                <?php if(isset($is_admin) && $is_admin): ?><a href="/admin" class="px-4 py-2 rounded-full border border-white/20 hover:bg-white/10 transition">Dashboard</a><?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero for Home -->
    <?php if($_SERVER['REQUEST_URI'] === '/'): ?>
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-purple-500/20 rounded-full blur-[120px] -z-10"></div>
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight text-white mb-8">
                Create without <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600">limits</span>.
            </h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto mb-10"><?= $site['site_description'] ?></p>
            <div class="flex justify-center gap-4">
                <a href="#work" class="px-8 py-3 rounded-full bg-white text-dark font-bold hover:bg-gray-100 transition">View Work</a>
                <a href="/contact" class="px-8 py-3 rounded-full bg-white/10 border border-white/10 hover:bg-white/20 transition backdrop-blur">Contact</a>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="pt-32 pb-12 text-center max-w-4xl mx-auto px-6">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4"><?= $title ?></h1>
        <?php if(isset($meta['category'])): ?>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20"><?= $meta['category'] ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-grow max-w-4xl mx-auto px-6 w-full pb-20" id="work">
        <?php if(isset($meta['image'])): ?>
            <img src="<?= $meta['image'] ?>" class="w-full rounded-2xl shadow-2xl shadow-purple-500/10 mb-12 border border-white/10">
        <?php endif; ?>
        
        <article class="prose prose-invert prose-lg max-w-none prose-headings:font-bold prose-a:text-cyan-400">
            <?= $content ?>
        </article>
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/10 bg-surface/50 py-12">
        <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center text-sm text-slate-500">
            <p>&copy; <?= date('Y') ?> <?= $site['site_name'] ?>.</p>
            <div class="flex gap-6 mt-4 md:mt-0">
                <?php foreach ($blocks->get('footer') as $item): ?>
                    <span><?= $item['body'] ?? $item['text'] ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </footer>

</body>
</html>
