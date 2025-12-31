<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Media Library</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-sm flex-shrink-0 z-10 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <h1 class="text-lg font-bold text-gray-900">Media Library</h1>
            <a href="/admin" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Back to Dashboard</a>
        </div>
    </header>

    <main class="flex-grow p-6">
        <div class="max-w-7xl mx-auto">
            
            <?php if(empty($images)): ?>
                <div class="text-center py-20 bg-white rounded-lg border-2 border-dashed border-gray-300">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No images</h3>
                    <p class="mt-1 text-sm text-gray-500">Upload images via the Editor.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    <?php foreach($images as $img): ?>
                    <div class="group relative bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-w-10 aspect-h-7 bg-gray-200 w-full overflow-hidden rounded-t-lg relative h-40">
                            <img src="<?= $img['url'] ?>" alt="" class="object-cover w-full h-full group-hover:opacity-75">
                        </div>
                        <div class="p-4">
                            <p class="block text-sm font-medium text-gray-900 truncate" title="<?= $img['name'] ?>"><?= $img['name'] ?></p>
                            <p class="block text-xs font-medium text-gray-500"><?= $img['size'] ?></p>
                            <button onclick="copyUrl('<?= $img['url'] ?>')" class="mt-2 w-full inline-flex justify-center items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Copy URL
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
    function copyUrl(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert('Copied: ' + url);
        });
    }
    </script>
</body>
</html>
