<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layout Manager</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <style>
        .block-card { transition: all 0.2s; }
        .block-card:hover { border-color: #cbd5e1; }
    </style>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    
    <header class="bg-white shadow-sm flex-shrink-0 z-10 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <h1 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                Layout & Widgets
            </h1>
            <div class="flex gap-4">
                <a href="/admin/edit?file=blocks.json&type=config&mode=raw" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium border border-indigo-200 bg-indigo-50 px-3 py-1 rounded">
                    &lt;&gt; Advanced JSON
                </a>
                <a href="/admin" class="text-gray-600 hover:text-gray-900 text-sm font-medium py-1">Cancel</a>
            </div>
        </div>
    </header>

    <main class="flex-grow p-6">
        <form action="/admin/save" method="post" class="max-w-7xl mx-auto">
            <input type="hidden" name="csrf" value="<?= $csrf ?>">
            <input type="hidden" name="file" value="blocks.json">
            <input type="hidden" name="type" value="blocks_gui"> <!-- Special handler -->

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <?php 
                $zones = ['header', 'sidebar_left', 'sidebar_right', 'footer'];
                foreach ($zones as $zone): 
                ?>
                <div class="bg-white shadow rounded-lg border border-gray-200 flex flex-col">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide"><?= str_replace('_', ' ', $zone) ?></h3>
                        <button type="button" onclick="addBlock('<?= $zone ?>')" class="text-xs bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-2 py-1 rounded shadow-sm">
                            + Add Widget
                        </button>
                    </div>
                    
                    <div class="p-4 space-y-4" id="container-<?= $zone ?>">
                        <?php 
                        $blocks = $config[$zone] ?? [];
                        foreach ($blocks as $index => $block): 
                            $bType = $block['type'] ?? 'content';
                        ?>
                            <div class="block-card bg-white border border-gray-200 rounded p-3 relative group">
                                <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity" title="Remove">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                
                                <div class="grid grid-cols-1 gap-3">
                                    <!-- Type Selector -->
                                    <div>
                                        <select name="blocks[<?= $zone ?>][][type]" onchange="toggleFields(this)" class="block w-full py-1 px-2 border border-gray-300 bg-gray-50 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="link" <?= $bType === 'link' ? 'selected' : '' ?>>Link (Menu Item)</option>
                                            <option value="content" <?= $bType === 'content' ? 'selected' : '' ?>>Content / Widget (Markdown)</option>
                                            <option value="raw" <?= $bType === 'raw' ? 'selected' : '' ?>>Raw HTML</option>
                                        </select>
                                    </div>

                                    <!-- Fields for Link -->
                                    <div class="field-link space-y-2 <?= $bType !== 'link' ? 'hidden' : '' ?>">
                                        <input type="text" name="blocks[<?= $zone ?>][<?= $index ?>][text]" value="<?= htmlspecialchars($block['text'] ?? '') ?>" placeholder="Link Text (e.g. Home)" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <input type="text" name="blocks[<?= $zone ?>][<?= $index ?>][url]" value="<?= htmlspecialchars($block['url'] ?? '') ?>" placeholder="URL (e.g. /about)" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <!-- Fields for Content -->
                                    <div class="field-content <?= ($bType !== 'content' && $bType !== 'raw') ? 'hidden' : '' ?>">
                                        <textarea name="blocks[<?= $zone ?>][<?= $index ?>][body]" rows="3" placeholder="Content (Markdown or HTML)" class="shadow-sm block w-full sm:text-sm border border-gray-300 rounded-md font-mono text-xs"><?= htmlspecialchars($block['body'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if(empty($blocks)): ?>
                            <p class="text-xs text-gray-400 text-center italic empty-msg">No widgets in this zone.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Layout
                </button>
            </div>
        </form>
    </main>

    <!-- JS Logic -->
    <script>
        function toggleFields(select) {
            const card = select.closest('.block-card');
            const val = select.value;
            
            if (val === 'link') {
                card.querySelector('.field-link').classList.remove('hidden');
                card.querySelector('.field-content').classList.add('hidden');
            } else {
                card.querySelector('.field-link').classList.add('hidden');
                card.querySelector('.field-content').classList.remove('hidden');
            }
        }

        function addBlock(zone) {
            const container = document.getElementById('container-' + zone);
            const emptyMsg = container.querySelector('.empty-msg');
            if (emptyMsg) emptyMsg.remove();

            // Generate a somewhat random index for name grouping (doesn't need to be perfect sequence for PHP)
            const index = 'new_' + Date.now(); 

            const html = `
                <div class="block-card bg-white border border-gray-200 rounded p-3 relative group animate-fade-in">
                    <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 group-hover:opacity-100 transition-opacity" title="Remove">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <select name="blocks[${zone}][${index}][type]" onchange="toggleFields(this)" class="block w-full py-1 px-2 border border-gray-300 bg-gray-50 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="link" selected>Link (Menu Item)</option>
                                <option value="content">Content / Widget (Markdown)</option>
                                <option value="raw">Raw HTML</option>
                            </select>
                        </div>
                        <div class="field-link space-y-2">
                            <input type="text" name="blocks[${zone}][${index}][text]" placeholder="Link Text (e.g. Home)" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <input type="text" name="blocks[${zone}][${index}][url]" placeholder="URL (e.g. /about)" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div class="field-content hidden">
                            <textarea name="blocks[${zone}][${index}][body]" rows="3" placeholder="Content (Markdown or HTML)" class="shadow-sm block w-full sm:text-sm border border-gray-300 rounded-md font-mono text-xs"></textarea>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
</body>
</html>
