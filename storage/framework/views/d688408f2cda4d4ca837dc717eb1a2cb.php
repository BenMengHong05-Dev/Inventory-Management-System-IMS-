<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['variant' => 'primary', 'type' => 'button', 'icon' => null]));

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

foreach (array_filter((['variant' => 'primary', 'type' => 'button', 'icon' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<?php
    $variants = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'dark' => 'btn-dark',
        'danger' => 'btn-danger',
    ];
    $classes = $variants[$variant] ?? $variants['primary'];
?>
<button type="<?php echo e($type); ?>" <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php if($icon): ?>
        <?php if(str_contains($icon, 'bi-') || str_contains($icon, 'bi ')): ?>
            <i class="<?php echo e($icon); ?> <?php if($slot->isNotEmpty()): ?> mr-1.5 <?php endif; ?>"></i>
        <?php else: ?>
            <i data-lucide="<?php echo e($icon); ?>" class="w-4 h-4 <?php if($slot->isNotEmpty()): ?> mr-1.5 <?php endif; ?>"></i>
        <?php endif; ?>
    <?php endif; ?>
    <?php echo e($slot); ?>

</button>
<?php /**PATH D:\invertory_managament_system\resources\views/components/button.blade.php ENDPATH**/ ?>