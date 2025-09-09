<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Выберите опцию',
    'required' => false,
    'disabled' => false,
    'help' => null,
    'error' => null,
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
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Выберите опцию',
    'required' => false,
    'disabled' => false,
    'help' => null,
    'error' => null,
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
    $inputId = $name ?? uniqid('select_');
    
    $baseClasses = 'block w-full rounded-md border-2 border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 focus:ring-green-500 dark:focus:border-green-400 dark:focus:ring-green-400 sm:text-sm transition-colors duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white';
    
    $sizeClasses = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-3 py-2 text-sm',
        'lg' => 'px-4 py-3 text-base'
    ];
    
    $classes = $baseClasses . ' ' . $sizeClasses[$size];
    
    if ($disabled) {
        $classes .= ' bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed';
    }
?>

<div class="space-y-1">
    <?php if($label): ?>
        <label for="<?php echo e($inputId); ?>" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            <?php echo e($label); ?>

            <?php if($required): ?>
                <span class="text-red-500">*</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>
    
    <select 
        name="<?php echo e($name); ?>"
        id="<?php echo e($inputId); ?>"
        <?php echo e($required ? 'required' : ''); ?>

        <?php echo e($disabled ? 'disabled' : ''); ?>

        <?php echo e($attributes->merge(['class' => $classes])); ?>

    >
        <?php if($placeholder): ?>
            <option value=""><?php echo e($placeholder); ?></option>
        <?php endif; ?>
        
        <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($value); ?>" <?php echo e(old($name, $selected) == $value ? 'selected' : ''); ?>>
                <?php echo e($label); ?>

            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    
    <?php if($help && !$error): ?>
        <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($help); ?></p>
    <?php endif; ?>
    
    <?php if($error): ?>
        <p class="text-sm text-red-600 dark:text-red-400"><?php echo e($error); ?></p>
    <?php endif; ?>
    
    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH /home/zendarol/akzholpharm/corporate-chat/resources/views/components/base/select.blade.php ENDPATH**/ ?>