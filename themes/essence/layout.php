<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?> | <?= $site['site_name'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Lato', 'sans-serif'], serif: ['Playfair Display', 'serif'] },
                    colors: { gold: '#d4af37', sand: '#f5f5f0' }
                }
            }
        }
    </script>
    <?php if(isset($hook)) ($hook)::call('head'); ?>
</head>
<body class="font-sans text-gray-600 bg-white">

    <!-- Top Bar -->
    <div class="bg-sand py-2 text-center text-xs tracking-widest uppercase font-bold text-gray-500">
        Free Shipping on all digital orders &bull; Secure CMS
    </div>

    <!-- Navigation -->
    <nav class="py-8 px-6 text-center">
        <a href="/" class="block text-4xl font-serif font-bold text-gray-900 mb-6"><?= $site['site_name'] ?></a>
        <div class="flex justify-center space-x-8 text-xs font-bold uppercase tracking-widest text-gray-500">
            <?php foreach ($blocks->get('header') as $link): ?>
                <a href="<?= $link['url'] ?>" class="hover:text-gold transition"><?= $link['text'] ?></a>
            <?php endforeach; ?>
            <?php if(isset($is_admin) && $is_admin): ?><a href="/admin">Account</a><?php endif; ?>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-6 py-12">
        
        <?php if($_SERVER['REQUEST_URI'] !== '/'): ?>
            <header class="text-center mb-12">
                <?php if(isset($meta['category'])): ?>
                    <span class="text-gold text-xs font-bold uppercase tracking-widest mb-4 inline-block"><?= $meta['category'] ?></span>
                <?php endif; ?>
                <h1 class="text-4xl md:text-5xl font-serif text-gray-900 mb-6"><?= $title ?></h1>
                <div class="w-12 h-1 bg-gold mx-auto"></div>
            </header>
            
            <?php if(isset($meta['image'])): ?>
                <img src="<?= $meta['image'] ?>" class="w-full h-auto mb-12 shadow-xl">
            <?php endif; ?>
        <?php endif; ?>

        <article class="prose prose-stone prose-lg mx-auto font-serif prose-headings:font-sans prose-headings:uppercase prose-headings:tracking-widest">
            <?= $content ?>
        </article>

        <!-- Newsletter -->
        <div class="mt-20 bg-sand p-12 text-center">
            <h3 class="font-serif text-2xl text-gray-900 mb-4">Join our Newsletter</h3>
            <p class="mb-6 text-sm">Get the latest updates directly in your inbox.</p>
            <form class="flex justify-center max-w-sm mx-auto">
                <input type="email" placeholder="Your email" class="w-full px-4 py-2 border-none outline-none">
                <button class="bg-gray-900 text-white px-6 py-2 uppercase text-xs font-bold tracking-widest">Sign Up</button>
            </form>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-12 text-center">
        <div class="flex justify-center space-x-6 mb-8">
            <!-- Social Icons Placeholder -->
            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
        </div>
        <p class="text-xs text-gray-400 uppercase tracking-widest">&copy; <?= date('Y') ?> <?= $site['site_name'] ?>. All rights reserved.</p>
    </footer>

</body>
</html>
