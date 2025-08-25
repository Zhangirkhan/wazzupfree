@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'rows' => 4,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'help' => null,
    'error' => null,
    'size' => 'md'
])

@php
    $inputId = $name ?? uniqid('textarea_');
    
    $baseClasses = 'block w-full rounded-md border-2 border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm transition-colors duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white';
    
    $sizeClasses = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-3 py-2 text-sm',
        'lg' => 'px-4 py-3 text-base'
    ];
    
    $classes = $baseClasses . ' ' . $sizeClasses[$size];
    
    if ($disabled) {
        $classes .= ' bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed';
    }
    
    if ($readonly) {
        $classes .= ' bg-gray-50 dark:bg-gray-800';
    }
@endphp

<div class="space-y-1">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <textarea 
        name="{{ $name }}"
        id="{{ $inputId }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $attributes->merge(['class' => $classes]) }}
    >{{ old($name, $value) }}</textarea>
    
    @if($help && !$error)
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
    
    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
    
    @error($name)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
