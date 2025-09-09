<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full bg-green-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Corporate Chat')); ?> - Вход</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full">
    <div class="min-h-full flex flex-col justify-center py-8 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <div class="flex justify-center">
                <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            </div>
            <h2 class="mt-4 text-center text-2xl font-bold tracking-tight text-gray-900">
                Вход в админ-панель
            </h2>
            <p class="mt-1 text-center text-sm text-gray-600">
                Введите ваши учетные данные для доступа к системе
            </p>
        </div>

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-sm">
            <div class="bg-white py-6 px-4 shadow-lg rounded-lg sm:px-8 border border-green-100">
                <?php if($errors->any()): ?>
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form class="space-y-6" action="<?php echo e(route('login.post')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                   value="<?php echo e(old('email')); ?>"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Пароль
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                            Войти в админ-панель
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Тестовые данные</span>
                        </div>
                    </div>

                    <div class="mt-6 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Доступные аккаунты:</h3>
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex justify-between">
                                <span>👨‍💼 Администратор:</span>
                                <span class="font-mono">admin@testcompany.com</span>
                            </div>
                            <div class="flex justify-between">
                                <span>👨‍💻 Менеджер:</span>
                                <span class="font-mono">manager@testcompany.com</span>
                            </div>
                            <div class="flex justify-between">
                                <span>👷 Сотрудник 1:</span>
                                <span class="font-mono">employee1@testcompany.com</span>
                            </div>
                            <div class="flex justify-between">
                                <span>👷 Сотрудник 2:</span>
                                <span class="font-mono">employee2@testcompany.com</span>
                            </div>
                            <div class="text-center text-gray-500 mt-2">
                                <span class="font-mono">Пароль: password</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="/" class="text-sm text-green-600 hover:text-green-500">
                        ← Вернуться на главную
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <div class="text-sm text-gray-500">
                <p>© <?php echo e(date('Y')); ?> Corporate Chat System</p>
                <p class="mt-1">Административная панель</p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /home/zendarol/akzholpharm/corporate-chat/resources/views/auth/login.blade.php ENDPATH**/ ?>