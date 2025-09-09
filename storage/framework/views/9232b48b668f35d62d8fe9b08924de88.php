<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'default',
    'size' => 'md'
]));

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

foreach (array_filter(([
    'variant' => 'default',
    'size' => 'md'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$baseClasses = 'inline-flex items-center rounded-full font-medium';

$variants = [
    'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'primary' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-0.5 text-sm',
    'lg' => 'px-3 py-1 text-sm'
];

$classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
?>

<span <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php echo e($slot); ?>

</span>
<?php /**PATH /home/zendarol/akzholpharm/corporate-chat/resources/views/components/base/badge.blade.php ENDPATH**/ ?>