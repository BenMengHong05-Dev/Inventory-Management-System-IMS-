<?php $__env->startSection('title', t('messages.stock_in')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900"><?php echo e(t('messages.stock_in')); ?></h1>
            <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'កត់ត្រាទំនិញចូល ទទួលពីអ្នកផ្គត់ផ្គង់ លេខបាច់/កាលបរិច្ឆេទផុតកំណត់ និងបង្កើនស្តុក' : 'Record incoming shipments, suppliers, batch/expiry numbers, and increase stock'); ?></p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="<?php echo e(route('stock-in.create')); ?>" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                <?php echo e(t('messages.create')); ?>

            </a>
        </div>
    </div>

    <!-- Search & Filter Bar (No white card background, no shadow, search on left, filters on right) -->
    <form action="<?php echo e(route('stock-in.index')); ?>" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['type' => 'text','name' => 'search','value' => ''.e($search).'','placeholder' => ''.e(t('messages.search')).' #...','wrapperClass' => '!mb-0','icon' => 'bi bi-search']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','name' => 'search','value' => ''.e($search).'','placeholder' => ''.e(t('messages.search')).' #...','wrapperClass' => '!mb-0','icon' => 'bi bi-search']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-48">
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'warehouse_id','wrapperClass' => '!mb-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'warehouse_id','wrapperClass' => '!mb-0']); ?>
                    <option value=""><?php echo e(t('messages.all')); ?> <?php echo e(t('messages.warehouses')); ?></option>
                    <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($w->id); ?>" <?php echo e($warehouseId == $w->id ? 'selected' : ''); ?>><?php echo e($w->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
            </div>
            <div class="w-full sm:w-40">
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'status','wrapperClass' => '!mb-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','wrapperClass' => '!mb-0']); ?>
                    <option value=""><?php echo e(t('messages.all')); ?> <?php echo e(t('messages.status')); ?></option>
                    <option value="APPROVED" <?php echo e($status === 'APPROVED' ? 'selected' : ''); ?>><?php echo e(t('messages.approved')); ?></option>
                    <option value="PENDING" <?php echo e($status === 'PENDING' ? 'selected' : ''); ?>><?php echo e(t('messages.pending')); ?></option>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
            </div>
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'dark','icon' => 'bi bi-funnel']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'dark','icon' => 'bi bi-funnel']); ?>
                <?php echo e(t('messages.filter')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <a href="<?php echo e(route('stock-in.index')); ?>" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Stock In Table (Modern Clear Glass) -->
    <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['pagination' => $stockIns]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stockIns)]); ?>
         <?php $__env->slot('head', null, []); ?> 
            <th><?php echo e(t('messages.stock_in')); ?> #</th>
            <th><?php echo e(t('messages.date')); ?></th>
            <th><?php echo e(t('messages.suppliers')); ?></th>
            <th><?php echo e(t('messages.warehouse')); ?></th>
            <th class="text-right"><?php echo e(t('messages.items')); ?></th>
            <th class="text-right"><?php echo e(t('messages.total')); ?></th>
            <th><?php echo e(app()->getLocale() == 'km' ? 'អ្នកទទួល' : 'Received By'); ?></th>
            <th><?php echo e(t('messages.status')); ?></th>
            <th class="text-right"><?php echo e(t('messages.actions')); ?></th>
         <?php $__env->endSlot(); ?>

        <?php $__empty_1 = true; $__currentLoopData = $stockIns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $si): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="font-bold font-mono text-slate-900">
                    <a href="<?php echo e(route('stock-in.show', $si->id)); ?>" class="text-sky-600 hover:text-sky-700 transition">
                        <?php echo e($si->stock_in_number); ?>

                    </a>
                    <?php if($si->reference_number): ?>
                        <span class="block text-[10px] text-slate-400 font-sans">Ref: <?php echo e($si->reference_number); ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-slate-600 whitespace-nowrap"><?php echo e($si->date); ?></td>
                <td class="text-slate-800 font-medium"><?php echo e($si->supplier_name ?? (app()->getLocale() == 'km' ? 'ផ្ទៃក្នុង / ស្តុកដើមគ្រា' : 'Internal / Opening')); ?></td>
                <td class="text-slate-700 font-medium"><?php echo e($si->warehouse_name); ?></td>
                <td class="text-right font-bold text-slate-800"><?php echo e($si->total_items); ?></td>
                <td class="text-right font-bold text-slate-900"><?php echo e(format_currency($si->total_amount)); ?></td>
                <td class="text-slate-600"><?php echo e($si->receiver_name); ?></td>
                <td>
                    <?php if(strtoupper($si->status) === 'APPROVED'): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <?php echo e($si->status); ?>

                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <?php echo e($si->status); ?>

                        </span>
                    <?php endif; ?>
                </td>
                <td class="text-right whitespace-nowrap">
                    <a href="<?php echo e(route('stock-in.show', $si->id)); ?>" class="btn-action btn-action-view" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if (isset($component)) { $__componentOriginald1264eb3994796452a71a252a3bad0a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald1264eb3994796452a71a252a3bad0a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-empty','data' => ['colspan' => 9,'icon' => 'package-plus','title' => app()->getLocale() == 'km' ? 'មិនទាន់មានកំណត់ត្រាទំនិញចូលនៅឡើយទេ' : 'No Stock In Entries Yet','description' => app()->getLocale() == 'km' ? 'កត់ត្រាទំនិញចូលថ្មីពីអ្នកផ្គត់ផ្គង់ ឬការផ្ទេរចូលឃ្លាំង' : 'Create a new stock in transaction to add inventory quantities.','actionUrl' => route('stock-in.create'),'actionLabel' => t('messages.create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 9,'icon' => 'package-plus','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'មិនទាន់មានកំណត់ត្រាទំនិញចូលនៅឡើយទេ' : 'No Stock In Entries Yet'),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'កត់ត្រាទំនិញចូលថ្មីពីអ្នកផ្គត់ផ្គង់ ឬការផ្ទេរចូលឃ្លាំង' : 'Create a new stock in transaction to add inventory quantities.'),'actionUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('stock-in.create')),'actionLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(t('messages.create'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald1264eb3994796452a71a252a3bad0a1)): ?>
<?php $attributes = $__attributesOriginald1264eb3994796452a71a252a3bad0a1; ?>
<?php unset($__attributesOriginald1264eb3994796452a71a252a3bad0a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald1264eb3994796452a71a252a3bad0a1)): ?>
<?php $component = $__componentOriginald1264eb3994796452a71a252a3bad0a1; ?>
<?php unset($__componentOriginald1264eb3994796452a71a252a3bad0a1); ?>
<?php endif; ?>
        <?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal163c8ba6efb795223894d5ffef5034f5)): ?>
<?php $attributes = $__attributesOriginal163c8ba6efb795223894d5ffef5034f5; ?>
<?php unset($__attributesOriginal163c8ba6efb795223894d5ffef5034f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal163c8ba6efb795223894d5ffef5034f5)): ?>
<?php $component = $__componentOriginal163c8ba6efb795223894d5ffef5034f5; ?>
<?php unset($__componentOriginal163c8ba6efb795223894d5ffef5034f5); ?>
<?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/stock_in/index.blade.php ENDPATH**/ ?>