<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extensions - Plugins & Themes</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    
    <header class="bg-white shadow-sm flex-shrink-0 z-10 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <h1 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                Extensions Manager
            </h1>
            <a href="/admin" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Back to Dashboard</a>
        </div>
    </header>

    <main class="flex-grow p-6">
        <div class="max-w-7xl mx-auto space-y-8">
            
            <?php if($flash): ?>
                <?php $bg = $flash['type'] === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800'; ?>
                <div class="<?= $bg ?> border px-4 py-3 rounded-lg shadow-sm flex items-center justify-between" role="alert">
                    <span class="block sm:inline"><?= $flash['msg'] ?></span>
                </div>
            <?php endif; ?>

            <!-- PLUGINS SECTION -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Plugins</h2>
                    <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs font-semibold"><?= count($plugins) ?> installed</span>
                </div>
                
                <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plugin</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach($plugins as $plugin): 
                                $isActive = in_array($plugin['id'], $active_plugins);
                            ?>
                            <tr class="<?= $isActive ? 'bg-blue-50' : '' ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($plugin['name']) ?></div>
                                    <div class="text-xs text-gray-500">v<?= htmlspecialchars($plugin['version']) ?> | By <?= htmlspecialchars($plugin['author']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700">
                                        <?= htmlspecialchars($plugin['description']) ?>
                                        <?php if(!empty($plugin['readme'])): ?>
                                            <button type="button" onclick="openModal('readme-<?= $plugin['id'] ?>')" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold ml-2 underline decoration-indigo-200">View Details</button>
                                            <div id="readme-<?= $plugin['id'] ?>" class="hidden">
                                                <div class="flex justify-between items-start mb-4 pb-2 border-b">
                                                    <div>
                                                        <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($plugin['name']) ?></h3>
                                                        <p class="text-sm text-gray-500">v<?= $plugin['version'] ?> by <?= $plugin['author'] ?></p>
                                                    </div>
                                                </div>
                                                <div class="prose prose-indigo prose-sm max-w-none">
                                                    <?= $plugin['readme'] ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="/admin/extensions/toggle" method="post">
                                        <input type="hidden" name="csrf" value="<?= $csrf ?>">
                                        <input type="hidden" name="plugin_id" value="<?= $plugin['id'] ?>">
                                        <?php if($isActive): ?>
                                            <input type="hidden" name="action" value="deactivate">
                                            <button type="submit" class="text-red-600 hover:text-red-900 border border-red-200 bg-white hover:bg-red-50 px-3 py-1 rounded">Deactivate</button>
                                        <?php else: ?>
                                            <input type="hidden" name="action" value="activate">
                                            <button type="submit" class="text-blue-600 hover:text-blue-900 border border-blue-200 bg-white hover:bg-blue-50 px-3 py-1 rounded">Activate</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($plugins)): ?>
                                <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500 text-sm">No plugins found in <code>plugins/</code> directory.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- THEMES SECTION -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Themes</h2>
                    <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs font-semibold"><?= count($themes) ?> installed</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($themes as $theme): 
                        $isCurrent = $theme['id'] === $current_theme;
                    ?>
                    <div class="bg-white rounded-lg shadow border <?= $isCurrent ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200' ?> overflow-hidden flex flex-col h-full">
                        <!-- Placeholder Preview -->
                        <div class="h-32 bg-gray-100 flex items-center justify-center border-b border-gray-100 relative group overflow-hidden">
                             <div class="absolute inset-0 bg-gradient-to-tr from-gray-100 to-gray-200 flex items-center justify-center text-4xl font-black text-gray-300 uppercase tracking-widest group-hover:scale-110 transition-transform duration-500">
                                 <?= substr($theme['name'], 0, 2) ?>
                             </div>
                        </div>
                        <div class="p-4 flex-grow">
                            <h3 class="font-bold text-gray-900"><?= htmlspecialchars($theme['name']) ?></h3>
                            <p class="text-xs text-gray-500 mb-2">v<?= htmlspecialchars($theme['version']) ?> by <?= htmlspecialchars($theme['author']) ?></p>
                            <?php if($isCurrent): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-center">
                            <?php if(!$isCurrent): ?>
                                <form action="/admin/extensions/theme" method="post">
                                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                                    <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                                        Activate
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled class="w-full inline-flex justify-center rounded-md border border-transparent px-4 py-2 bg-gray-300 text-base font-medium text-white cursor-default sm:text-sm">
                                    Active
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

        </div>
    </main>

    <!-- Modal -->
    <div id="infoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div id="modalContent"></div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal()">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(contentId) {
            const content = document.getElementById(contentId).innerHTML;
            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('infoModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            document.getElementById('infoModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        // Esc to close
        document.addEventListener('keydown', function(event) {
            if(event.key === "Escape") closeModal();
        });
    </script>
</body>
</html>
