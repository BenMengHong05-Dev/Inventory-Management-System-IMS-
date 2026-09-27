<?php $__env->startSection('title', t('messages.low_stock')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                <h1 class="text-xl font-bold text-gray-900"><?php echo e(t('messages.low_stock')); ?></h1>
            </div>
            <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'មុខទំនិញដែលដល់ ឬក្រោមចំណុចកំណត់បញ្ជាទិញឡើងវិញ ជាមួយបរិមាណដែលបានណែនាំ' : 'Items that are at or below their configured reorder points with suggested order quantities'); ?></p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="<?php echo e(route('purchase-requests.create')); ?>" class="btn-primary">
                <i class="bi bi-cart-plus mr-1.5"></i>
                <?php echo e(t('messages.purchase_requests')); ?>

            </a>
        </div>
    </div>

    <!-- Table (Modern Clear Glass) -->
    <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('head', null, []); ?> 
            <th><?php echo e(t('messages.items')); ?> & <?php echo e(t('messages.code')); ?></th>
            <th><?php echo e(t('messages.categories')); ?></th>
            <th class="text-right"><?php echo e(app()->getLocale() == 'km' ? 'ស្តុកបច្ចុប្បន្ន' : 'Current Stock'); ?></th>
            <th class="text-right"><?php echo e(app()->getLocale() == 'km' ? 'កម្រិតបញ្ជាទិញឡើងវិញ' : 'Reorder Level'); ?></th>
            <th class="text-right"><?php echo e(app()->getLocale() == 'km' ? 'កម្រិតអប្បបរមា' : 'Minimum Level'); ?></th>
            <th class="text-right"><?php echo e(app()->getLocale() == 'km' ? 'បរិមាណណែនាំ' : 'Suggested Reorder'); ?></th>
            <th class="text-right"><?php echo e(t('messages.unit_cost')); ?></th>
            <th class="text-right"><?php echo e(app()->getLocale() == 'km' ? 'តម្លៃប៉ាន់ស្មាន' : 'Est. Reorder Cost'); ?></th>
            <th class="text-right"><?php echo e(t('messages.actions')); ?></th>
         <?php $__env->endSlot(); ?>

        <?php $__empty_1 = true; $__currentLoopData = $lowStockItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $reorderCost = $row->suggested_order_qty * $row->default_unit_cost;
            ?>
            <tr>
                <td>
                    <a href="<?php echo e(route('items.show', $row->id)); ?>" class="font-bold text-slate-900 hover:text-sky-600 block transition">
                        <?php echo e(localized_name($row, 'name')); ?>

                    </a>
                    <span class="text-[11px] text-slate-400 font-mono"><?php echo e($row->code); ?></span>
                </td>
                <td class="text-slate-600 font-medium"><?php echo e(localized_name($row, 'category_name')); ?></td>
                <td class="text-right font-bold text-sm <?php echo e($row->current_stock <= 0 ? 'text-rose-600' : 'text-amber-600'); ?>">
                    <?php echo e(number_format($row->current_stock, 0)); ?> <span class="text-[11px] text-slate-400 font-normal">/ <?php echo e(localized_name($row, 'unit_name')); ?></span>
                    <?php if($row->current_stock <= 0): ?>
                        <span class="block text-[10px] text-rose-600 font-bold uppercase tracking-wider"><?php echo e(app()->getLocale() == 'km' ? 'អស់ពីស្តុក' : 'OUT OF STOCK'); ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-right font-bold text-slate-700 font-mono"><?php echo e(number_format($row->reorder_level, 0)); ?></td>
                <td class="text-right text-slate-500 font-mono"><?php echo e(number_format($row->min_stock, 0)); ?></td>
                <td class="text-right font-bold text-sm text-emerald-600 font-mono">
                    +<?php echo e(number_format($row->suggested_order_qty, 0)); ?>

                </td>
                <td class="text-right text-slate-600 font-medium"><?php echo e(format_currency($row->default_unit_cost)); ?></td>
                <td class="text-right font-bold text-slate-900"><?php echo e(format_currency($reorderCost)); ?></td>
                <td class="text-right whitespace-nowrap">
                    <a href="<?php echo e(route('purchase-requests.create', ['item_id' => $row->id, 'qty' => $row->suggested_order_qty])); ?>"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl text-xs font-semibold shadow-sm shadow-emerald-500/20 hover:from-emerald-600 hover:to-teal-700 transition">
                        <i class="bi bi-cart-plus"></i>
                        <span><?php echo e(app()->getLocale() == 'km' ? 'បញ្ជាទិញ' : 'Reorder'); ?></span>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if (isset($component)) { $__componentOriginald1264eb3994796452a71a252a3bad0a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald1264eb3994796452a71a252a3bad0a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-empty','data' => ['colspan' => 9,'icon' => 'check2-circle','title' => app()->getLocale() == 'km' ? 'ស្តុកគ្រប់គ្រាន់ល្អ' : 'Healthy Inventory Levels','description' => app()->getLocale() == 'km' ? 'គ្រប់មុខទំនិញទាំងអស់សុទ្ធតែមានស្តុកគ្រប់គ្រាន់លើសកម្រិតកំណត់។' : 'All items are currently stocked safely above their reorder thresholds.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 9,'icon' => 'check2-circle','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'ស្តុកគ្រប់គ្រាន់ល្អ' : 'Healthy Inventory Levels'),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'គ្រប់មុខទំនិញទាំងអស់សុទ្ធតែមានស្តុកគ្រប់គ្រាន់លើសកម្រិតកំណត់។' : 'All items are currently stocked safely above their reorder thresholds.')]); ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/inventory/low_stock.blade.php ENDPATH**/ ?>