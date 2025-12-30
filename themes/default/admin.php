<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    
    <!--Navbar -->
    <header class="bg-white shadow-sm sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <h1 class="text-xl font-bold text-gray-800">CMS Dashboard</h1>
                <nav class="flex space-x-4">
                    <a href="/" target="_blank" class="text-gray-600 hover:text-blue-600 font-medium">View Site</a>
                    <a href="/admin/logout" class="text-red-600 hover:text-red-800 font-medium">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <?php if($flash): ?>
            <?php 
                $bg = $flash['type'] === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800';
            ?>
            <div class="<?= $bg ?> border px-4 py-3 rounded-lg mb-6 shadow-sm flex items-center justify-between" role="alert">
                <span class="block sm:inline"><?= $flash['msg'] ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Main Content Column -->
            <section class="lg:col-span-2 space-y-8">
                
                <!-- Content Management -->
                <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50">
                        <h2 class="text-lg font-medium text-gray-900">Pages (Markdown)</h2>
                        
                        <form method="get" class="w-full sm:w-auto relative">
                            <input type="text" name="q" placeholder="Search pages..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="block w-full sm:w-64 pl-10 sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </form>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach($files as $f): 
                                $slug = str_replace('.md', '', $f['filename']);
                                $statusClass = $f['status'] === 'published' 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-yellow-100 text-yellow-800';
                            ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                            <?= ucfirst($f['status']) ?> 
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($f['title']) ?></div>
                                        <div class="text-xs text-gray-500"><?= $f['filename'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <a href="/<?= $slug === 'home' ? '' : $slug ?>" target="_blank" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-2 py-1 rounded">View</a>
                                        <a href="/admin/edit?file=<?= $f['filename'] ?>&type=content" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-2 py-1 rounded">Edit</a>
                                        <?php if($f['filename'] !== 'home.md'): ?>
                                        <form action="/admin/delete" method="post" onsubmit="return confirm('Delete <?= $f['filename'] ?>?');" class="inline">
                                            <input type="hidden" name="csrf" value="<?= $csrf ?>">
                                            <input type="hidden" name="file" value="<?= $f['filename'] ?>">
                                            <input type="hidden" name="type" value="content">
                                            <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-2 py-1 rounded">Delete</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Create New Page Form -->
                <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Page</h3>
                    <form action="/admin/create" method="post" class="space-y-4">
                        <input type="hidden" name="csrf" value="<?= $csrf ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="page_title" class="block text-sm font-medium text-gray-700">Page Title</label>
                                <div class="mt-1">
                                    <input type="text" name="title" id="page_title" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="e.g. My New Service" required onkeyup="generateSlug()">
                                </div>
                            </div>
                            
                            <div>
                                <label for="page_slug" class="block text-sm font-medium text-gray-700">URL Slug (Auto-generated)</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                        /
                                    </span>
                                    <input type="text" name="slug" id="page_slug" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full sm:text-sm border-gray-300 rounded-r-md" placeholder="my-new-service">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Create & Edit
                            </button>
                        </div>
                    </form>
                </div>

                <script>
                    function generateSlug() {
                        const title = document.getElementById('page_title').value;
                        const slug = title.toLowerCase()
                            .replace(/[^a-z0-9\s-]/g, '') // Remove non-alphanumeric chars
                            .trim()
                            .replace(/\s+/g, '-')         // Replace spaces with hyphens
                            .replace(/-+/g, '-');         // Remove multiple hyphens
                        
                        document.getElementById('page_slug').value = slug;
                    }
                </script>

            </section>
            
            <!-- Sidebar: Config & Tools -->
            <aside class="space-y-8">
                
                <!-- Inbox Widget -->
                <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                     <a href="/admin/inbox" class="block px-6 py-4 hover:bg-gray-50 transition flex items-center justify-between group">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="ml-3 text-lg font-medium text-gray-900 group-hover:text-indigo-600">Inbox</span>
                        </div>
                        <?php if(isset($msgCount) && $msgCount > 0): ?>
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full"><?= $msgCount ?></span>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm">0</span>
                        <?php endif; ?>
                     </a>
                </div>

                <!-- Extensions Manager -->
                <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                     <a href="/admin/extensions" class="block px-6 py-4 hover:bg-gray-50 transition flex items-center justify-between group">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            <span class="ml-3 text-lg font-medium text-gray-900 group-hover:text-purple-600">Extensions</span>
                        </div>
                     </a>
                </div>

                <!-- System Settings -->
                <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-medium text-gray-900">System Settings</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                    <?php foreach($configs as $f): 
                        // Map filenames to friendly UI elements
                        if ($f === 'site.json') {
                            $ui = [
                                'title' => 'General Settings',
                                'desc' => 'Site name, description & admin access',
                                'icon' => '<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>'
                            ];
                        } elseif ($f === 'blocks.json') {
                            $ui = [
                                'title' => 'Layout & Widgets',
                                'desc' => 'Manage menus, sidebars and footers',
                                'icon' => '<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>'
                            ];
                        } else {
                            $ui = [
                                'title' => $f,
                                'desc' => 'Raw Configuration File',
                                'icon' => '<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
                            ];
                        }
                    ?>
                        <a href="/admin/edit?file=<?= $f ?>&type=config" class="block hover:bg-gray-50 transition p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 pt-0.5">
                                    <?= $ui['icon'] ?>
                                </div>
                                <div class="ml-3 w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900"><?= $ui['title'] ?></p>
                                    <p class="text-xs text-gray-500 mt-1"><?= $ui['desc'] ?></p>
                                </div>
                                <div class="ml-auto pl-3 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1 md:flex md:justify-between">
                            <p class="text-sm text-blue-700">
                                System is running securely. All files are encrypted at rest.
                            </p>
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    </main>
</body>
</html>
