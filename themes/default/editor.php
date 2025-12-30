<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editing <?= $file ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    
    <!-- Toast UI Editor Local -->
    <link rel="stylesheet" href="/assets/toastui/editor.css" />
    <script src="/assets/toastui/editor.js"></script>
    
    <style>
        /* Override generic styles to match Tailwind */
        .toastui-editor-defaultUI { border: none !important; }
        .toastui-editor-toolbar { background-color: #f9fafb !important; border-bottom: 1px solid #e5e7eb !important; }
        /* Ensure tabs are visible */
        .toastui-editor-mode-switch { background-color: #f3f4f6 !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans h-screen flex flex-col">
    
    <header class="bg-white shadow-sm flex-shrink-0 z-10 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <h1 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <span class="text-gray-500 font-normal">Editing:</span> <?= $file ?>
            </h1>
            <div class="flex items-center gap-4">
                 <?php if($type === 'content'): ?>
                    <span class="text-xs text-indigo-600 bg-indigo-50 px-2 py-1 rounded border border-indigo-100 uppercase tracking-wider font-semibold">
                        Full Editor
                    </span>
                 <?php else: ?>
                    <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Code Mode</span>
                 <?php endif; ?>
                
                <a href="/admin/history?file=<?= $file ?>&type=<?= $type ?>" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium border border-indigo-200 bg-indigo-50 px-2 py-1 rounded flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    History
                </a>

                <a href="/admin" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Cancel / Back</a>
            </div>
        </div>
    </header>

    <form action="/admin/save" method="post" id="editor-form" class="flex-grow flex overflow-hidden">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">
        <input type="hidden" name="file" value="<?= $file ?>">
        <input type="hidden" name="type" value="<?= $type ?>">

        <?php if($type === 'content'): ?>
            <!-- Metadata Sidebar (Left) -->
            <aside class="w-80 bg-white border-r border-gray-200 flex flex-col overflow-y-auto z-0 flex-shrink-0">
                <div class="p-6 space-y-6">
                    <div>
                        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Page Settings</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" name="title" value="<?= htmlspecialchars($meta['title'] ?? '') ?>" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="draft" <?= ($meta['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= ($meta['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="date" name="date" value="<?= htmlspecialchars($meta['date'] ?? date('Y-m-d')) ?>" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">SEO & Social</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea name="description" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"><?= htmlspecialchars($meta['description'] ?? '') ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Featured Image URL</label>
                                        <input type="text" name="image" value="<?= htmlspecialchars($meta['image'] ?? '') ?>" placeholder="/media/..." class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-200">
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Changes
                        </button>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded text-xs text-blue-700">
                        <strong>Tip:</strong> Use the tabs at the bottom of the editor to switch between <b>Visual</b> (WYSIWYG) and <b>Markdown</b> modes.
                    </div>
                </div>
            </aside>
            
            <!-- Visual Editor Container -->
            <main class="flex-grow flex flex-col min-w-0 bg-white relative">
                <!-- Toast UI Editor Element -->
                <div id="visual-editor" class="absolute inset-0"></div>
                <!-- Hidden input to store Markdown on submit -->
                <input type="hidden" name="body" id="hidden-body">
            </main>

            <!-- Store initial content in a hidden div to avoid escaping issues in JS strings -->
            <div id="initial-content" style="display:none;"><?= htmlspecialchars($content) ?></div>

            <script>
                const Editor = toastui.Editor;
                const initialContent = document.getElementById('initial-content').textContent;

                const editor = new Editor({
                    el: document.querySelector('#visual-editor'),
                    height: '100%',
                    initialEditType: 'wysiwyg', // Start in Visual mode
                    previewStyle: 'vertical',
                    initialValue: initialContent,
                    language: 'en-US',
                    hideModeSwitch: false, // Explicitly show Markdown/WYSIWYG tabs
                    toolbarItems: [
                        ['heading', 'bold', 'italic', 'strike'],
                        ['hr', 'quote'],
                        ['ul', 'ol', 'task', 'indent', 'outdent'],
                        ['table', 'image', 'link'],
                        ['code', 'codeblock']
                    ],
                    hooks: {
                        addImageBlobHook: (blob, callback) => {
                            const formData = new FormData();
                            formData.append('image', blob);
                            // Need CSRF? Ideally yes, but retrieving it from DOM is easy
                            // Since we are in the admin layout, cookies are sent.
                            
                            fetch('/admin/upload', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.imageUrl) {
                                    callback(data.imageUrl, 'image');
                                } else {
                                    alert('Upload failed: ' + (data.error || 'Unknown error'));
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('Upload failed.');
                            });
                            
                            return false;
                        }
                    }
                });

                // Sync on Submit
                document.getElementById('editor-form').addEventListener('submit', function() {
                    document.getElementById('hidden-body').value = editor.getMarkdown();
                });
            </script>

        <?php else: ?>
            
            <!-- Raw Code Editor for JSON/Config -->
            <main class="flex-grow flex flex-col min-w-0 bg-gray-50">
                 <div class="px-4 py-2 bg-gray-100 border-b border-gray-200 flex justify-end items-center gap-4">
                    <span class="text-xs text-gray-500">Editing raw configuration (JSON)</span>
                    <button type="submit" class="inline-flex justify-center py-1 px-3 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Save Config
                    </button>
                </div>
                <div class="flex-grow relative">
                    <textarea name="body" class="absolute inset-0 w-full h-full p-6 font-mono text-sm border-0 focus:ring-0 resize-none bg-gray-900 text-green-400 focus:bg-gray-900 transition-colors outline-none" spellcheck="false"><?= htmlspecialchars($content) ?></textarea>
                </div>
            </main>

        <?php endif; ?>

    </form>

</body>
</html>
