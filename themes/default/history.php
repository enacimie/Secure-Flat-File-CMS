<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History: <?= $targetFile ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    
    <header class="bg-white shadow-sm flex-shrink-0 z-10 border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <h1 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Version History
            </h1>
            <a href="/admin/edit?file=<?= $targetFile ?>&type=<?= $type ?>" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Back to Editor</a>
        </div>
    </header>

    <main class="flex-grow p-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        <?= $targetFile ?>
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Select a version to restore. This will overwrite the current content.
                    </p>
                </div>
                <ul class="divide-y divide-gray-200">
                    <?php if(empty($backups)): ?>
                        <li class="px-4 py-4 sm:px-6 text-center text-gray-500">No backups found for this file.</li>
                    <?php else: ?>
                        <?php foreach($backups as $backup): ?>
                        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-indigo-600 truncate">
                                        <?= $backup['date'] ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?= $backup['file'] ?>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <form action="/admin/restore" method="post" onsubmit="return confirm('Are you sure you want to restore this version? Current changes will be moved to history.');">
                                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                                    <input type="hidden" name="version_file" value="<?= $backup['file'] ?>">
                                    <input type="hidden" name="target_file" value="<?= $targetFile ?>">
                                    <input type="hidden" name="type" value="<?= $type ?>">
                                    <input type="hidden" name="date" value="<?= $backup['date'] ?>">
                                    <button type="submit" class="font-medium text-indigo-600 hover:text-indigo-500 border border-indigo-200 bg-indigo-50 px-3 py-1 rounded">
                                        Restore
                                    </button>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </main>
</body>
</html>
