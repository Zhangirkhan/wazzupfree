@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 shadow rounded-lg']) }}>
    @if($title || $actions)
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    @if($title)
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            {{ $title }}
                        </h3>
                    @endif
                    @if($subtitle)
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                            {{ $subtitle }}
                        </p>
                    @endif
                </div>
                @if($actions)
                    <div class="flex items-center space-x-2">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <div class="px-4 py-5 sm:p-6 dark:text-white">
        {{ $slot }}
    </div>
</div>
