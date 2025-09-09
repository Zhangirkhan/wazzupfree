@props(['class' => ''])

<style>
    /* Стили для переключателя темы */
    .theme-toggle-button {
        position: relative;
        overflow: hidden;
    }
    
    .theme-toggle-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, #3b82f6, #1d4ed8);
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 9999px;
    }
    
    .theme-toggle-button:hover::before {
        opacity: 0.1;
    }
    
    .theme-toggle-button:active {
        transform: scale(0.95);
    }
    
    /* Анимация для иконок */
    .theme-icon {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .theme-icon.sun {
        color: #f59e0b;
    }
    
    .theme-icon.moon {
        color: #e5e7eb;
    }
    
    .dark .theme-icon.moon {
        color: #93c5fd;
    }
</style>

<div x-data="{ 
    darkMode: false,
    init() {
        // Проверяем localStorage и текущее состояние DOM
        const savedTheme = localStorage.getItem('darkMode');
        const isDarkInDOM = document.documentElement.classList.contains('dark');
        
        // Приоритет: localStorage > DOM состояние
        if (savedTheme !== null) {
            this.darkMode = savedTheme === 'true';
        } else {
            this.darkMode = isDarkInDOM;
        }
        
        console.log('Theme toggle initialized, darkMode:', this.darkMode, 'savedTheme:', savedTheme, 'isDarkInDOM:', isDarkInDOM);
        
        // Применяем тему
        this.updateTheme();
        
        // Слушаем изменения темы в других вкладках
        window.addEventListener('storage', (e) => {
            if (e.key === 'darkMode') {
                this.darkMode = e.newValue === 'true';
                console.log('Theme changed from storage, darkMode:', this.darkMode);
                this.updateTheme();
            }
        });
    },
    toggleTheme() {
        this.darkMode = !this.darkMode;
        console.log('Theme toggled, darkMode:', this.darkMode);
        this.updateTheme();
    },
    updateTheme() {
        const htmlElement = document.documentElement;
        if (this.darkMode) {
            htmlElement.classList.add('dark');
            console.log('Dark mode enabled');
        } else {
            htmlElement.classList.remove('dark');
            console.log('Dark mode disabled');
        }
        // Обновляем localStorage для синхронизации между вкладками
        localStorage.setItem('darkMode', this.darkMode);
    }
}" class="{{ $class }}">
    <button 
        @click="toggleTheme()"
        type="button" 
        class="theme-toggle-button relative inline-flex h-6 w-11 items-center rounded-full transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 hover:scale-105"
        :class="darkMode ? 'bg-blue-600 shadow-lg' : 'bg-gray-300 shadow-md'"
        :title="darkMode ? 'Переключить на светлую тему' : 'Переключить на темную тему'"
    >
        <span class="sr-only">Переключить тему</span>
        <span 
            class="inline-block h-4 w-4 transform rounded-full bg-white shadow-md transition-all duration-300 ease-in-out"
            :class="darkMode ? 'translate-x-6' : 'translate-x-1'"
        ></span>
        
        <!-- Иконка солнца (светлая тема) -->
        <svg 
            class="theme-icon sun absolute left-1 h-3 w-3 transition-all duration-300 ease-in-out" 
            :class="darkMode ? 'opacity-0 scale-75' : 'opacity-100 scale-100'"
            fill="currentColor" 
            viewBox="0 0 20 20"
        >
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
        </svg>
        
        <!-- Иконка луны (темная тема) -->
        <svg 
            class="theme-icon moon absolute right-1 h-3 w-3 transition-all duration-300 ease-in-out" 
            :class="darkMode ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
            fill="currentColor" 
            viewBox="0 0 20 20"
        >
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
        </svg>
    </button>
</div>
