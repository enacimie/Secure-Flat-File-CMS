<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
    <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            <?= isset($step) && $step === '2fa' ? 'Two-Factor Authentication' : 'Sign in to Admin Panel' ?>
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">Secure Encrypted CMS</p>
    </div>
    
    <?php if(isset($error)): ?>
        <div class="bg-red-50 border-l-4 border-red-400 p-4">
            <p class="text-sm text-red-700"><?= $error ?></p>
        </div>
    <?php endif; ?>

    <form class="mt-8 space-y-6" method="post">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">
        
        <?php if (isset($step) && $step === '2fa'): ?>
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="2fa_code" class="sr-only">Authentication Code</label>
                    <input id="2fa_code" name="2fa_code" type="text" pattern="[0-9]*" inputmode="numeric" required class="text-center tracking-[1em] font-mono text-lg appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="000000" autofocus>
                </div>
            </div>
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Verify Code
                </button>
            </div>
        <?php else: ?>
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="user" class="sr-only">Username</label>
                    <input id="user" name="user" type="text" required class="appearance-none rounded-none rounded-t-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Username">
                </div>
                <div>
                    <label for="pass" class="sr-only">Password</label>
                    <input id="pass" name="pass" type="password" required class="appearance-none rounded-none rounded-b-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password">
                </div>
            </div>
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Sign in
                </button>
            </div>
        <?php endif; ?>
    </form>
    </div>
</body>
</html>