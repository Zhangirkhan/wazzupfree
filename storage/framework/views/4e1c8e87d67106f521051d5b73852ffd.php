<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['class' => '']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['class' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="{ 
    darkMode: localStorage.getItem('darkMode') === 'true',
    init() {
        this.updateTheme();
    },
    toggleTheme() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        this.updateTheme();
    },
    updateTheme() {
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}" class="<?php echo e($class); ?>">
    <button 
        @click="toggleTheme()"
        type="button" 
        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
        :class="darkMode ? 'bg-green-600' : 'bg-gray-200'"
    >
        <span class="sr-only">Переключить тему</span>
        <span 
            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
            :class="darkMode ? 'translate-x-6' : 'translate-x-1'"
        ></span>
        
        <!-- Иконки солнца и луны -->
        <svg 
            class="absolute left-1 h-3 w-3 text-yellow-500 transition-opacity" 
            :class="darkMode ? 'opacity-0' : 'opacity-100'"
            fill="currentColor" 
            viewBox="0 0 20 20"
        >
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
        </svg>
        
        <svg 
            class="absolute right-1 h-3 w-3 text-gray-400 transition-opacity" 
            :class="darkMode ? 'opacity-100' : 'opacity-0'"
            fill="currentColor" 
            viewBox="0 0 20 20"
        >
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
        </svg>
    </button>
</div>
<?php /**PATH /home/zendarol/akzholpharm/corporate-chat/resources/views/components/settings/theme-toggle.blade.php ENDPATH**/ ?>