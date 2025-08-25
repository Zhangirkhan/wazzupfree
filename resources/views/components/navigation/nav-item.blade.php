@props(['active' => false, 'icon' => null])

@php
$classes = $active
    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-700'
    : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 border-transparent';
@endphp

<li>
    <a href="{{ $attributes->get('href') }}" 
       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold border {{ $classes }}">
        @if($icon)
            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
            </svg>
        @endif
        {{ $slot }}
    </a>
</li>
