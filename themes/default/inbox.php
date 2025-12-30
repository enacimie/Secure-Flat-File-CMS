<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - Messages</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    
    <header class="bg-white shadow-sm flex-shrink-0 z-10 border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <h1 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                Inbox
            </h1>
            <a href="/admin" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Back to Dashboard</a>
        </div>
    </header>

    <main class="flex-grow p-6">
        <div class="max-w-5xl mx-auto space-y-6">
            
            <?php if(empty($messages)): ?>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No messages</h3>
                    <p class="mt-1 text-sm text-gray-500">Your inbox is empty.</p>
                </div>
            <?php else: ?>

                <?php foreach($messages as $msg): ?>
                <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900"><?= htmlspecialchars($msg['name']) ?></h3>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($msg['email']) ?></p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-500 block"><?= date('M j, Y g:i a', strtotime($msg['date'])) ?></span>
                            <form action="/admin/delete" method="post" onsubmit="return confirm('Delete this message?');" class="inline-block mt-1">
                                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                                <input type="hidden" name="file" value="<?= $msg['filename'] ?>">
                                <input type="hidden" name="type" value="messages">
                                <button type="submit" class="text-xs text-red-600 hover:text-red-800 hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-gray-700 whitespace-pre-wrap text-sm"><?= htmlspecialchars($msg['message']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </main>
</body>
</html>
