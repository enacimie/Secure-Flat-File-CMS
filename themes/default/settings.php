<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    
    <header class="bg-white shadow-sm flex-shrink-0 z-10 border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <h1 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Site Configuration
            </h1>
            <a href="/admin" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Cancel / Back</a>
        </div>
    </header>

    <main class="flex-grow p-6">
        <div class="max-w-3xl mx-auto space-y-6">
            
            <form action="/admin/save" method="post" class="space-y-6">
                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                <input type="hidden" name="file" value="site.json">
                <input type="hidden" name="type" value="config_gui"> <!-- Special type for GUI handling -->

                <!-- General Settings Card -->
                <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">General Information</h3>
                            <p class="mt-1 text-sm text-gray-500">Basic details about your website.</p>
                        </div>
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6">
                                    <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                                    <input type="text" name="site_name" id="site_name" value="<?= htmlspecialchars($config['site_name'] ?? '') ?>" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6">
                                    <label for="site_description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <input type="text" name="site_description" id="site_description" value="<?= htmlspecialchars($config['site_description'] ?? '') ?>" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <p class="mt-2 text-sm text-gray-500">Used for SEO meta description.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Settings Card -->
                <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Security & Access</h3>
                            <p class="mt-1 text-sm text-gray-500">Manage admin credentials.</p>
                        </div>
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-4">
                                    <label for="admin_user" class="block text-sm font-medium text-gray-700">Admin Username</label>
                                    <input type="text" name="admin_user" id="admin_user" value="<?= htmlspecialchars($config['admin_user'] ?? 'admin') ?>" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <label for="admin_pass" class="block text-sm font-medium text-gray-700">Change Password</label>
                                    <input type="password" name="admin_pass" id="admin_pass" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Leave empty to keep current password">
                                    <p class="mt-2 text-sm text-gray-500">Only enter a value if you want to change it.</p>
                                </div>
                                
                                <div class="col-span-6 border-t border-gray-100 pt-6 mt-2">
                                    <label for="admin_2fa_secret" class="block text-sm font-medium text-gray-700">Two-Factor Authentication (2FA)</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <input type="text" name="admin_2fa_secret" id="admin_2fa_secret" value="<?= htmlspecialchars($config['admin_2fa_secret'] ?? '') ?>" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300 font-mono tracking-widest bg-gray-50" placeholder="Empty to disable">
                                        <button type="button" onclick="generate2FA()" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                            <span>Generate</span>
                                        </button>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Base32 Secret. Enter this into your Authenticator App (Google/Microsoft/Authy).
                                        <br>
                                        <span class="text-red-500 font-bold">Important:</span> Setup your app BEFORE saving, or you will be locked out.
                                    </p>
                                    <script src="/assets/js/qrcode.min.js"></script>
                                    <script>
                                    function generate2FA() {
                                        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
                                        let secret = '';
                                        const array = new Uint32Array(16);
                                        crypto.getRandomValues(array);
                                        for(let i = 0; i < 16; i++) {
                                            secret += chars[array[i] % 32];
                                        }
                                        document.getElementById('admin_2fa_secret').value = secret;
                                        
                                        // Generate QR Locally
                                        const user = document.getElementById('admin_user').value || 'Admin';
                                        const site = document.getElementById('site_name').value || 'SecureCMS';
                                        const uri = `otpauth://totp/${encodeURIComponent(site)}:${encodeURIComponent(user)}?secret=${secret}&issuer=${encodeURIComponent(site)}`;
                                        
                                        const container = document.getElementById('qr_canvas');
                                        container.innerHTML = ''; // Clear previous
                                        new QRCode(container, {
                                            text: uri,
                                            width: 192,
                                            height: 192,
                                            correctLevel: QRCode.CorrectLevel.M
                                        });
                                        
                                        document.getElementById('qr_box').classList.remove('hidden');
                                    }
                                    </script>
                                    
                                    <div id="qr_box" class="hidden mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg text-center flex flex-col items-center">
                                        <p class="text-sm font-medium text-gray-700 mb-2">Scan this with your App:</p>
                                        <div id="qr_canvas" class="border p-2 bg-white rounded shadow-sm inline-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <a href="/admin" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
