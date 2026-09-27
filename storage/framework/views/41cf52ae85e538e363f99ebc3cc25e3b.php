<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'headers' => [],
    'title' => null,
    'subtitle' => null,
    'badge' => null,
    'search' => null,
    'filters' => null,
    'actions' => null,
    'pagination' => null,
    'tableClass' => 'data-table'
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
    'headers' => [],
    'title' => null,
    'subtitle' => null,
    'badge' => null,
    'search' => null,
    'filters' => null,
    'actions' => null,
    'pagination' => null,
    'tableClass' => 'data-table'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div <?php echo e($attributes->merge(['class' => 'glass-card overflow-hidden'])); ?>>
    <?php if($title || isset($header) || isset($actions) || isset($search) || isset($filters)): ?>
        <div class="px-5 py-4 flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-200/70 bg-white/70 backdrop-blur-md">
            <!-- Left Side: Title, Subtitle, Badge, and Search Input -->
            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                <?php if($title): ?>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-bold text-slate-900 tracking-tight"><?php echo e($title); ?></h2>
                        <?php if($badge): ?>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100/80 text-slate-600 border border-slate-200/60"><?php echo e($badge); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if($subtitle): ?>
                    <span class="text-[11px] text-slate-400 font-medium"><?php echo e($subtitle); ?></span>
                <?php endif; ?>
                <?php echo e($header ?? ''); ?>


                <?php if(isset($search)): ?>
                    <div class="relative">
                        <?php echo e($search); ?>

                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Side: Button Filters & Actions -->
            <?php if(isset($filters) || isset($actions)): ?>
                <div class="flex flex-wrap items-center gap-2 justify-end">
                    <?php echo e($filters ?? ''); ?>

                    <?php echo e($actions ?? ''); ?>

                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="<?php echo e($tableClass); ?> w-full">
            <thead>
                <tr>
                    <?php if(empty($headers) && isset($head)): ?>
                        <?php echo e($head); ?>

                    <?php else: ?>
                        <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th><?php echo e($header); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100/80">
                <?php echo e($slot); ?>

            </tbody>
        </table>
    </div>

    <?php if(isset($pagination) && $pagination): ?>
        <?php if(is_object($pagination) && method_exists($pagination, 'total')): ?>
            <?php if($pagination->total() > 0): ?>
                <div class="px-5 py-3 border-t border-slate-200/70 bg-white/60 backdrop-blur-sm flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                    <div>
                        <?php echo e(app()->getLocale() == 'km' ? 'បង្ហាញ' : 'Showing'); ?> 
                        <span class="font-bold text-slate-700"><?php echo e($pagination->firstItem() ?? 1); ?></span>
                        <?php echo e(app()->getLocale() == 'km' ? 'ដល់' : 'to'); ?>

                        <span class="font-bold text-slate-700"><?php echo e($pagination->lastItem() ?? $pagination->count()); ?></span>
                        <?php echo e(app()->getLocale() == 'km' ? 'នៃ' : 'of'); ?>

                        <span class="font-bold text-slate-700"><?php echo e($pagination->total()); ?></span>
                        <?php echo e(app()->getLocale() == 'km' ? 'ទិន្នន័យ' : 'entries'); ?>

                    </div>
                    <?php if($pagination->hasPages()): ?>
                        <div>
                            <?php echo e($pagination->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php elseif(!empty($pagination)): ?>
            <div class="px-5 py-3 border-t border-slate-200/70 bg-white/60 backdrop-blur-sm">
                <?php echo e($pagination); ?>

            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php /**PATH D:\invertory_managament_system\resources\views/components/table.blade.php ENDPATH**/ ?>