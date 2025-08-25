<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Corporate Chat') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        /* Select2 темная тема */
        .dark .select2-container--default .select2-selection--single {
            background-color: #374151;
            border-color: #4B5563;
            color: #F9FAFB;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #F9FAFB;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9CA3AF;
        }
        .dark .select2-dropdown {
            background-color: #374151;
            border-color: #4B5563;
        }
        .dark .select2-container--default .select2-results__option {
            color: #F9FAFB;
        }
        .dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3B82F6;
            color: #F9FAFB;
        }
        .dark .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #4B5563;
        }
        .dark .select2-search__field {
            background-color: #374151;
            color: #F9FAFB;
            border-color: #4B5563;
        }
        
        /* Select2 в модальном окне */
        .select2-container {
            z-index: 9999;
        }
        .select2-dropdown {
            z-index: 9999;
        }
        
        /* Модальное окно стили */
        #addChatModal {
            backdrop-filter: blur(4px);
        }
        #addChatModal > div {
            animation: modalFadeIn 0.3s ease-out;
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>
    
    @livewireStyles
    
    <!-- Prevent FOUC (Flash of Unstyled Content) -->
    <script>
        // Initialize theme immediately to prevent flash
        (function() {
            const darkMode = localStorage.getItem('darkMode') === 'true';
            const htmlElement = document.documentElement;
            if (darkMode) {
                htmlElement.classList.add('dark');
            } else {
                htmlElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900">
    <div class="min-h-full" x-data="appLayout()">
        
        <!-- Sidebar -->
        <div 
            class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-300 ease-in-out"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            style="display: none;"
            x-show="true"
        >
            <x-navigation.sidebar />
        </div>

        <!-- Main content -->
        <div 
            class="transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'lg:pl-72' : 'lg:pl-0'"
            style="display: none;"
            x-show="true"
        >
            <!-- Header -->
            <x-navigation.header />

            <!-- Page content -->
            <main class="py-6">
                <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <x-feedback.alert type="success" :message="session('success')" />
                    @endif

                    @if (session('error'))
                        <x-feedback.alert type="error" :message="session('error')" />
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>

        <!-- Mobile overlay -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
            @click="toggleSidebar()"
        ></div>
    </div>

    @livewireScripts
    
    <script>
    function appLayout() {
        return {
            sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
                localStorage.setItem('sidebarOpen', this.sidebarOpen);
            }
        }
    }
    </script>
</body>
</html>
