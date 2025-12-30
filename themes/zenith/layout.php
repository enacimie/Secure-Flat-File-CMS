<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> &mdash; <?= $site['site_name'] ?></title>
    
    <!-- Fonts: Merriweather (Serif) & Lato (Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        serif: ['Merriweather', 'serif'],
                        sans: ['Lato', 'sans-serif']
                    },
                    colors: {
                        paper: '#fdfbf7',
                        ink: '#1c1917'
                    },
                    typography: (theme) => ({
                        DEFAULT: {
                            css: {
                                color: theme('colors.ink'),
                                fontFamily: theme('fontFamily.serif'),
                                h1: { fontFamily: theme('fontFamily.serif'), fontWeight: '700' },
                                h2: { fontFamily: theme('fontFamily.serif'), fontWeight: '400', fontStyle: 'italic' },
                                a: { color: theme('colors.stone.600'), textDecoration: 'underline', '&:hover': { color: theme('colors.stone.900') } },
                            },
                        },
                    }),
                }
            }
        }
    </script>
    <?php if(isset($hook)) ($hook)::call('head'); ?>
    <style>
        body { background-color: #fdfbf7; color: #1c1917; }
        ::selection { background: #e7e5e4; }
    </style>
</head>
<body class="flex flex-col min-h-full font-serif antialiased">

    <!-- Minimal Header -->
    <header class="py-12 border-b-2 border-stone-100 mb-12">
        <div class="max-w-2xl mx-auto px-6 text-center">
            <a href="/" class="inline-block group">
                <h1 class="text-3xl font-bold tracking-tight text-ink group-hover:text-stone-600 transition-colors">
                    <?= $site['site_name'] ?>
                </h1>
                <p class="text-sm font-sans text-stone-500 mt-2 uppercase tracking-widest"><?= $site['site_description'] ?></p>
            </a>
            
            <nav class="mt-8 flex justify-center space-x-6 font-sans text-sm font-bold text-stone-400 uppercase tracking-wider">
                <?php foreach ($blocks->get('header') as $link): ?>
                    <a href="<?= $link['url'] ?>" class="hover:text-stone-800 transition-colors"><?= $link['text'] ?></a>
                <?php endforeach; ?>
                <?php if(isset($is_admin) && $is_admin): ?>
                    <a href="/admin" class="text-stone-300 hover:text-red-800">● Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow px-6">
        <article class="max-w-2xl mx-auto prose prose-stone prose-lg md:prose-xl">
            
            <!-- Article Header -->
            <?php if($_SERVER['REQUEST_URI'] !== '/'): ?>
                <header class="mb-10 text-center not-prose">
                    <?php if(isset($meta['category'])): ?>
                        <div class="font-sans text-xs font-bold text-stone-400 uppercase tracking-widest mb-3"><?= $meta['category'] ?></div>
                    <?php endif; ?>
                    
                    <h1 class="text-4xl md:text-5xl font-bold text-ink mb-4 leading-tight"><?= $title ?></h1>
                    
                    <div class="font-sans text-stone-500 italic">
                        Published on <time><?= date('F j, Y', strtotime($meta['date'] ?? 'now')) ?></time>
                    </div>
                </header>

                <?php if(isset($meta['image'])): ?>
                    <figure class="mb-10 -mx-6 md:-mx-12">
                        <img src="<?= $meta['image'] ?>" alt="<?= $title ?>" class="w-full h-auto grayscale hover:grayscale-0 transition duration-700 ease-in-out">
                    </figure>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Content -->
            <?= $content ?>
            
            <!-- Tags -->
            <?php if(!empty($meta['tags'])): ?>
                <div class="mt-12 pt-6 border-t border-stone-200 font-sans text-sm">
                    <span class="text-stone-400 mr-2">Tags:</span>
                    <?php foreach($meta['tags'] as $tag): ?>
                        <span class="inline-block bg-stone-100 text-stone-600 px-2 py-1 rounded mr-2 mb-2">#<?= $tag ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </article>
    </main>

    <!-- Footer -->
    <footer class="mt-24 py-12 border-t border-stone-200 bg-white">
        <div class="max-w-2xl mx-auto px-6 text-center font-sans text-stone-400 text-sm">
            <p>&copy; <?= date('Y') ?> <?= $site['site_name'] ?>.</p>
            <div class="mt-4 space-x-4">
                 <?php foreach ($blocks->get('footer') as $item): ?>
                    <span class="hover:text-stone-600 transition-colors"><?= $item['body'] ?? $item['text'] ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </footer>

</body>
</html>
