<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'subtitle' => null,
    'actions' => null
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
    'title' => null,
    'subtitle' => null,
    'actions' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'bg-white dark:bg-gray-800 shadow rounded-lg'])); ?>>
    <?php if($title || $actions): ?>
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <?php if($title): ?>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            <?php echo e($title); ?>

                        </h3>
                    <?php endif; ?>
                    <?php if($subtitle): ?>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                            <?php echo e($subtitle); ?>

                        </p>
                    <?php endif; ?>
                </div>
                <?php if($actions): ?>
                    <div class="flex items-center space-x-2">
                        <?php echo e($actions); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="px-4 py-5 sm:p-6 dark:text-white">
        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH /home/zendarol/akzholpharm/corporate-chat/resources/views/components/base/card.blade.php ENDPATH**/ ?>