<div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
    <!-- Sidebar toggle button -->
    <x-base.button 
        type="button" 
        variant="ghost"
        class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
        @click="toggleSidebar()"
    >
        <span class="sr-only">Toggle sidebar</span>
        <!-- Icon for open sidebar -->
        <svg 
            x-show="sidebarOpen"
            class="h-6 w-6" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke-width="1.5" 
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        
        <!-- Icon for closed sidebar -->
        <svg 
            x-show="!sidebarOpen"
            class="h-6 w-6" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke-width="1.5" 
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </x-base.button>

    <!-- Separator -->
    <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 lg:hidden" aria-hidden="true"></div>

    <!-- Search -->
    <div class="relative flex flex-1 gap-x-4 self-stretch lg:gap-x-6" x-data="headerSearch()">
        <!-- Search input -->
        <div class="relative flex flex-1">
            <label for="search-field" class="sr-only">Поиск разделов</label>
            <svg class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400 dark:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
            <input 
                id="search-field" 
                x-ref="searchInput"
                x-model="searchQuery"
                @click="openSearch()"
                @keydown="handleKeydown($event)"
                class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-0 sm:text-sm bg-transparent dark:bg-gray-800" 
                placeholder="Поиск разделов..." 
                type="search" 
                name="search"
            >
        </div>

        <!-- Search results dropdown -->
        <div 
            x-show="isSearchOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute top-full left-0 right-0 z-50 mt-1 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-gray-900/5 focus:outline-none max-h-96 overflow-y-auto"
            @click.away="closeSearch()"
            style="display: none;"
        >
            <div class="py-2">
                <template x-if="filteredSections.length === 0 && searchQuery.trim()">
                    <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                        Раздел не найден
                    </div>
                </template>
                
                <template x-if="searchQuery.trim()">
                    <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        Найдено разделов: <span x-text="filteredSections.length"></span>
                    </div>
                </template>
                
                <template x-for="(section, index) in filteredSections" :key="section.name">
                    <x-base.button 
                        @click="selectSection(section)"
                        variant="ghost"
                        :class="index === selectedIndex ? 'bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700'"
                        class="w-full px-4 py-3 text-left transition-colors flex items-center space-x-3"
                    >
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="section.icon"></path>
                            </svg>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="section.name"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="section.description"></p>
                        </div>
                        
                        <!-- Arrow -->
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </x-base.button>
                </template>
            </div>
        </div>
    </div>

    <!-- Right section -->
    <div class="flex items-center gap-x-4 lg:gap-x-6">
        <!-- Version -->
        <div class="relative" x-data="{ open: false }">
            <x-base.button 
                type="button" 
                variant="ghost" 
                class="-m-1.5 p-1.5 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300" 
                @click="open = !open"
                title="Версия приложения"
            >
                <span class="sr-only">Версия приложения</span>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">v1.0.0</span>
            </x-base.button>

            <!-- Version dropdown -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-md bg-white dark:bg-gray-800 py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                 @click.away="open = false">
                
                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Версия приложения</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">v1.0.0</p>
                </div>
                
                <x-base.button 
                    href="/changelog.txt" 
                    target="_blank"
                    variant="ghost" 
                    class="w-full text-left justify-start"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    История изменений
                </x-base.button>
            </div>
        </div>

        <!-- Notifications -->
        <x-notifications.notification-bell />

        <!-- Theme Toggle -->
        <x-settings.theme-toggle />

        <!-- Separator -->
        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200 dark:lg:bg-gray-700" aria-hidden="true"></div>

        <!-- Profile dropdown -->
        <div class="relative" x-data="{ open: false }">
            <x-base.button type="button" variant="ghost" class="-m-1.5 flex items-center p-1.5" @click="open = !open">
                <span class="sr-only">Open user menu</span>
                <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                    <span class="text-sm font-medium text-white">
                        {{ auth()->user()->name[0] }}
                    </span>
                </div>
                <span class="hidden lg:flex lg:items-center">
                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900 dark:text-white" aria-hidden="true">
                        {{ auth()->user()->name }}
                    </span>
                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </span>
            </x-base.button>

            <!-- Dropdown menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-md bg-white dark:bg-gray-800 py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                 @click.away="open = false">
                
                <!-- Профиль -->
                <x-base.button href="{{ route('admin.profile.show') }}" variant="ghost" class="w-full text-left justify-start">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Мой профиль
                </x-base.button>
                
                                <!-- Разделитель -->
                <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                
                <!-- Выход -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-base.button type="submit" variant="ghost" class="w-full text-left justify-start">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Выйти
                    </x-base.button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function headerSearch() {
    return {
        searchQuery: '',
        isSearchOpen: false,
        sections: [
            {
                name: 'Dashboard',
                href: '{{ route('admin.dashboard') }}',
                icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z',
                description: 'Главная панель управления'
            },
            {
                name: 'Пользователи',
                href: '{{ route('admin.users.index') }}',
                icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
                description: 'Управление пользователями системы'
            },
            {
                name: 'Отделы',
                href: '{{ route('admin.departments.index') }}',
                icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                description: 'Управление отделами организации'
            },
            {
                name: 'Чаты',
                href: '{{ route('admin.chats.index') }}',
                icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                description: 'Управление чатами и сообщениями'
            },
            {
                name: 'Организации',
                href: '{{ route('admin.organizations.index') }}',
                icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                description: 'Управление организациями'
            },
            {
                name: 'Должности',
                href: '{{ route('admin.positions.index') }}',
                icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                description: 'Управление должностями сотрудников'
            }
        ],
        selectedIndex: -1,
        init() {
            // Close search when clicking outside
            document.addEventListener('click', (event) => {
                if (!this.$el.contains(event.target)) {
                    this.closeSearch();
                }
            });
        },
        get filteredSections() {
            if (!this.searchQuery.trim()) return this.sections;
            const query = this.searchQuery.toLowerCase();
            return this.sections.filter(section => 
                section.name.toLowerCase().includes(query) ||
                section.description.toLowerCase().includes(query)
            );
        },
        get selectedSection() {
            if (this.selectedIndex >= 0 && this.selectedIndex < this.filteredSections.length) {
                return this.filteredSections[this.selectedIndex];
            }
            return null;
        },
        openSearch() {
            this.isSearchOpen = true;
            this.selectedIndex = -1;
            this.$nextTick(() => {
                this.$refs.searchInput.focus();
            });
        },
        closeSearch() {
            this.isSearchOpen = false;
            this.searchQuery = '';
            this.selectedIndex = -1;
        },
        selectSection(section) {
            window.location.href = section.href;
        },
        handleKeydown(event) {
            if (!this.isSearchOpen) return;
            
            switch(event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    this.selectedIndex = Math.min(this.selectedIndex + 1, this.filteredSections.length - 1);
                    break;
                case 'ArrowUp':
                    event.preventDefault();
                    this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                    break;
                case 'Enter':
                    event.preventDefault();
                    if (this.selectedSection) {
                        this.selectSection(this.selectedSection);
                    } else if (this.filteredSections.length > 0) {
                        this.selectSection(this.filteredSections[0]);
                    }
                    break;
                case 'Escape':
                    this.closeSearch();
                    break;
            }
        }
    }
}
</script>
