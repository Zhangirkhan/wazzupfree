<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-green-50 dark:bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Админка')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-green-50 dark:bg-gray-900">
    <div class="min-h-full" x-data="layoutData()">
        <!-- Sidebar -->
        <div 
            class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <x-navigation.sidebar />
        </div>

        <!-- Main content -->
        <div 
            class="transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'lg:pl-72' : 'lg:pl-0'"
        >
            <!-- Header -->
            <x-navigation.header />

            <!-- Page content -->
            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    <x-feedback.alert type="success" />
                    <x-feedback.alert type="error" />
                    <x-feedback.alert type="warning" />
                    <x-feedback.alert type="info" />

                    {{ $slot }}
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
    
    <!-- Theme initialization script -->
    <script>
        // Initialize theme immediately to prevent flash
        (function() {
            const darkMode = localStorage.getItem('darkMode') === 'true';
            if (darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
        
        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const darkMode = localStorage.getItem('darkMode') === 'true';
            if (darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
        
        // Debug Alpine.js
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js initialized');
        });
        
        function layoutData() {
            return {
                sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
                init() {
                    // Initialize sidebar state
                    if (typeof localStorage !== 'undefined') {
                        this.sidebarOpen = localStorage.getItem('sidebarOpen') !== 'false';
                    }
                    console.log('Layout initialized, sidebarOpen:', this.sidebarOpen);
                },
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                    localStorage.setItem('sidebarOpen', this.sidebarOpen);
                    console.log('Sidebar toggled:', this.sidebarOpen);
                }
            }
        }
    </script>
</body>
</html>
