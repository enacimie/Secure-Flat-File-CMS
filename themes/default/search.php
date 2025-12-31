<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - <?= $site['site_name'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="/" class="text-xl font-bold text-gray-900"><?= $site['site_name'] ?></a>
                <form action="/search" method="get" class="flex">
                    <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Search..." class="border border-gray-300 rounded-l-md px-3 py-1 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-1 rounded-r-md hover:bg-indigo-700">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Results for "<?= htmlspecialchars($query) ?>"</h1>

        <?php if(empty($results)): ?>
            <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
                <p class="text-gray-500">No results found.</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach($results as $item): 
                    $slug = str_replace('.md', '', $item['file']);
                ?>
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-500"><?= $item['date'] ?></span>
                        <?php if(!empty($item['category'])): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800"><?= $item['category'] ?></span>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">
                        <a href="/<?= $slug ?>" class="hover:text-indigo-600"><?= $item['title'] ?></a>
                    </h2>
                    <p class="text-gray-600 mb-4"><?= $item['description'] ?? 'No description available.' ?></p>
                    <a href="/<?= $slug ?>" class="text-indigo-600 font-medium hover:underline">Read more &rarr;</a>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if($pagination['total'] > 1): ?>
            <div class="mt-8 flex justify-center space-x-2">
                <?php for($i=1; $i<=$pagination['total']; $i++): ?>
                    <a href="/search?q=<?= urlencode($query) ?>&page=<?= $i ?>" 
                       class="px-4 py-2 border rounded-md <?= $i === $pagination['current'] ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </main>
    
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-8 px-4 text-center text-sm text-gray-500">
            &copy; <?= date('Y') ?> <?= $site['site_name'] ?>
        </div>
    </footer>
</body>
</html>
