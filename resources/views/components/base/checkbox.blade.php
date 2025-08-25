@props([
    'name',
    'label' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'help' => null,
    'error' => null
])

@php
    $inputId = $name ?? uniqid('checkbox_');
    $isChecked = old($name, $checked);
@endphp

<div class="space-y-1">
    <label class="flex items-center">
        <input 
            type="checkbox" 
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ $value }}"
            {{ $isChecked ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors duration-200"
            {{ $attributes }}
        >
        @if($label)
            <span class="ml-2 text-sm font-medium text-gray-700">{{ $label }}</span>
        @endif
    </label>
    
    @if($help && !$error)
        <p class="text-sm text-gray-500 ml-6">{{ $help }}</p>
    @endif
    
    @if($error)
        <p class="text-sm text-red-600 ml-6">{{ $error }}</p>
    @endif
    
    @error($name)
        <p class="text-sm text-red-600 ml-6">{{ $message }}</p>
    @enderror
</div>
