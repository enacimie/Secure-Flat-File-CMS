<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - <?= $site['site_name'] ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="/" class="text-xl font-bold text-gray-900"><?= $site['site_name'] ?></a>
                <a href="/" class="text-gray-500 hover:text-gray-900">Back Home</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow border border-gray-200">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Contact Us</h2>
                <p class="mt-2 text-center text-sm text-gray-600">We'd love to hear from you.</p>
            </div>

            <?php if(isset($_GET['sent'])): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-center">
                    Message sent successfully!
                </div>
            <?php else: ?>
                <form class="mt-8 space-y-6" action="/contact" method="POST">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    
                    <!-- Honeypot (Hidden) -->
                    <div style="display:none;">
                        <label>Website URL</label>
                        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="rounded-md shadow-sm -space-y-px">
                        <div>
                            <label for="name" class="sr-only">Name</label>
                            <input id="name" name="name" type="text" required class="appearance-none rounded-none rounded-t-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Your Name">
                        </div>
                        <div>
                            <label for="email" class="sr-only">Email address</label>
                            <input id="email" name="email" type="email" required class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address">
                        </div>
                        <div>
                            <label for="message" class="sr-only">Message</label>
                            <textarea id="message" name="message" rows="4" required class="appearance-none rounded-none rounded-b-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Your Message..."></textarea>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Send Message
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>
    
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-8 px-4 text-center text-sm text-gray-500">
            &copy; <?= date('Y') ?> <?= $site['site_name'] ?>
        </div>
    </footer>
</body>
</html>
