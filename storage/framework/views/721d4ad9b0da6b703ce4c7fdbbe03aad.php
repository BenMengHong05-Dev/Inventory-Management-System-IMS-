<?php $__env->startSection('title', t('messages.stock_out')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900"><?php echo e(t('messages.stock_out')); ?></h1>
            <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'បញ្ចេញទំនិញ និងសម្ភារប្រើប្រាស់ទៅកាន់នាយកដ្ឋាន និងបុគ្គលិកដោយកាត់កងស្តុក' : 'Issue items and consumables to departments and employees with stock deduction'); ?></p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="<?php echo e(route('stock-out.create')); ?>" class="btn-primary">
                <i class="bi bi-dash-lg mr-1.5"></i>
                <?php echo e(t('messages.create')); ?>

            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form action="<?php echo e(route('stock-out.index')); ?>" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
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
            <a href="<?php echo e(route('stock-out.index')); ?>" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Stock Out Table (Modern Clear Glass) -->
    <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['pagination' => $stockOuts]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stockOuts)]); ?>
         <?php $__env->slot('head', null, []); ?> 
            <th><?php echo e(t('messages.stock_out')); ?> #</th>
            <th><?php echo e(t('messages.date')); ?></th>
            <th><?php echo e(t('messages.department')); ?> / <?php echo e(t('messages.employees')); ?></th>
            <th><?php echo e(t('messages.warehouse')); ?></th>
            <th><?php echo e(app()->getLocale() == 'km' ? 'គោលបំណង' : 'Purpose'); ?></th>
            <th class="text-right"><?php echo e(t('messages.items')); ?></th>
            <th class="text-right"><?php echo e(t('messages.total')); ?></th>
            <th><?php echo e(app()->getLocale() == 'km' ? 'អ្នកចេញ' : 'Issued By'); ?></th>
            <th><?php echo e(t('messages.status')); ?></th>
            <th class="text-right"><?php echo e(t('messages.actions')); ?></th>
         <?php $__env->endSlot(); ?>

        <?php $__empty_1 = true; $__currentLoopData = $stockOuts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $so): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="font-bold font-mono text-slate-900">
                    <a href="<?php echo e(route('stock-out.show', $so->id)); ?>" class="text-sky-600 hover:text-sky-700 transition">
                        <?php echo e($so->stock_out_number); ?>

                    </a>
                </td>
                <td class="text-slate-600 whitespace-nowrap"><?php echo e($so->date); ?></td>
                <td>
                    <span class="font-semibold text-slate-800 block"><?php echo e($so->department_name ?? (app()->getLocale() == 'km' ? 'ទូទៅ' : 'General')); ?></span>
                    <span class="text-[11px] text-slate-400"><?php echo e($so->employee_name ?? (app()->getLocale() == 'km' ? 'មិនបានកំណត់អ្នកទទួល' : 'No recipient')); ?></span>
                </td>
                <td class="text-slate-700 font-medium"><?php echo e($so->warehouse_name); ?></td>
                <td class="text-slate-600 truncate max-w-[140px]"><?php echo e($so->purpose ?? '-'); ?></td>
                <td class="text-right font-bold text-slate-800"><?php echo e($so->total_items); ?></td>
                <td class="text-right font-bold text-slate-900"><?php echo e(format_currency($so->total_value)); ?></td>
                <td class="text-slate-600"><?php echo e($so->issuer_name); ?></td>
                <td>
                    <?php if(strtoupper($so->status) === 'APPROVED'): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <?php echo e($so->status); ?>

                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <?php echo e($so->status); ?>

                        </span>
                    <?php endif; ?>
                </td>
                <td class="text-right whitespace-nowrap">
                    <a href="<?php echo e(route('stock-out.show', $so->id)); ?>" class="btn-action btn-action-view" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if (isset($component)) { $__componentOriginald1264eb3994796452a71a252a3bad0a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald1264eb3994796452a71a252a3bad0a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-empty','data' => ['colspan' => 10,'icon' => 'package-minus','title' => app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាទំនិញចេញទេ' : 'No Stock Out Requisitions Recorded','description' => app()->getLocale() == 'km' ? 'ចាប់ផ្តើមកត់ត្រាការបញ្ចេញទំនិញ ឬសម្ភារប្រើប្រាស់ដោយចុចប៊ូតុងខាងក្រោម' : 'Issue items and consumables to departments or employees.','actionUrl' => route('stock-out.create'),'actionLabel' => t('messages.create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 10,'icon' => 'package-minus','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាទំនិញចេញទេ' : 'No Stock Out Requisitions Recorded'),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'ចាប់ផ្តើមកត់ត្រាការបញ្ចេញទំនិញ ឬសម្ភារប្រើប្រាស់ដោយចុចប៊ូតុងខាងក្រោម' : 'Issue items and consumables to departments or employees.'),'actionUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('stock-out.create')),'actionLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(t('messages.create'))]); ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/stock_out/index.blade.php ENDPATH**/ ?>