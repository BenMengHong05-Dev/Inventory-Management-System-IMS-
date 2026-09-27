<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'colspan' => 10,
    'icon' => 'package-x',
    'title' => null,
    'description' => null,
    'action' => null,
    'actionUrl' => null,
    'actionLabel' => null,
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
    'colspan' => 10,
    'icon' => 'package-x',
    'title' => null,
    'description' => null,
    'action' => null,
    'actionUrl' => null,
    'actionLabel' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<tr>
    <td colspan="<?php echo e($colspan); ?>" class="text-center py-12">
        <div class="w-14 h-14 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <?php if(str_starts_with($icon, 'bi ') || str_starts_with($icon, 'bi-')): ?>
                <i class="<?php echo e(str_starts_with($icon, 'bi-') ? 'bi ' . $icon : $icon); ?> text-2xl"></i>
            <?php else: ?>
                <i data-lucide="<?php echo e($icon); ?>" class="w-7 h-7"></i>
            <?php endif; ?>
        </div>
        <h4 class="text-sm font-bold text-slate-800">
            <?php echo e($title ?? (app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យនៅឡើយទេ' : 'No Data Available')); ?>

        </h4>
        <?php if($description): ?>
            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 mb-4"><?php echo e($description); ?></p>
        <?php endif; ?>
        <?php if($action): ?>
            <div class="mt-3">
                <?php echo e($action); ?>

            </div>
        <?php elseif($actionUrl && $actionLabel): ?>
            <div class="mt-3">
                <a href="<?php echo e($actionUrl); ?>" class="btn-primary">
                    <i class="bi bi-plus-lg mr-1.5"></i>
                    <?php echo e($actionLabel); ?>

                </a>
            </div>
        <?php endif; ?>
    </td>
</tr>
<?php /**PATH D:\invertory_managament_system\resources\views/components/table-empty.blade.php ENDPATH**/ ?>