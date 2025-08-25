@props([
    'type' => 'text',
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'help' => null,
    'error' => null,
    'size' => 'md', // sm, md, lg
    'variant' => 'default' // default, error, success
])

@php
    $inputId = $name ?? uniqid('input_');
    
    $baseClasses = 'block w-full rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 dark:focus:border-green-400 dark:focus:ring-green-400 sm:text-sm transition-colors duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400';
    
    $sizeClasses = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-3 py-2 text-sm',
        'lg' => 'px-4 py-3 text-base'
    ];
    
    $variantClasses = [
        'default' => 'border-2 border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 dark:focus:border-green-400 dark:focus:ring-green-400',
        'error' => 'border-2 border-red-300 dark:border-red-500 focus:border-red-500 focus:ring-red-500 dark:focus:border-red-400 dark:focus:ring-red-400',
        'success' => 'border-2 border-green-300 dark:border-green-500 focus:border-green-500 focus:ring-green-500 dark:focus:border-green-400 dark:focus:ring-green-400'
    ];
    
    $classes = $baseClasses . ' ' . $sizeClasses[$size] . ' ' . $variantClasses[$variant];
    
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
    
    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $attributes->merge(['class' => $classes]) }}
    />
    
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
