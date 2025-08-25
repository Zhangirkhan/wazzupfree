<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'href' => null
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
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'href' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$baseClasses = 'inline-flex items-center justify-center rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors duration-200';
$disabledClasses = 'opacity-50 cursor-not-allowed';

$variants = [
    'primary' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-500',
    'secondary' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-500',
    'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-500',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-500',
    'warning' => 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-500',
    'outline' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-green-500',
    'ghost' => 'text-gray-700 hover:bg-gray-100 focus:ring-green-500 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-green-500'
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base'
];

$classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
if ($disabled) {
    $classes .= ' ' . $disabledClasses;
}
?>

<?php if($href): ?>
    <a 
        href="<?php echo e($href); ?>"
        <?php echo e($attributes->merge(['class' => $classes])); ?>

    >
        <?php echo e($slot); ?>

    </a>
<?php else: ?>
    <button 
        type="<?php echo e($type); ?>" 
        <?php echo e($disabled ? 'disabled' : ''); ?>

        <?php echo e($attributes->merge(['class' => $classes])); ?>

    >
        <?php echo e($slot); ?>

    </button>
<?php endif; ?>
<?php /**PATH /home/zendarol/akzholpharm/corporate-chat/resources/views/components/base/button.blade.php ENDPATH**/ ?>