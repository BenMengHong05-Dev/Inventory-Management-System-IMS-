<?php $__env->startSection('title', t('messages.goods_receiving') . ' (GRN)'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900"><?php echo e(t('messages.goods_receiving')); ?></h1>
            <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'កត់ត្រាការទទួលទំនិញជាក់ស្តែងពីការបញ្ជាទិញ (PO) និងគាំទ្រការទទួលដោយផ្នែក' : 'Record incoming deliveries against Purchase Orders with partial receiving support'); ?></p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="<?php echo e(route('goods-receipts.create')); ?>" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                <?php echo e(t('messages.create') ?? 'New GRN'); ?>

            </a>
        </div>
    </div>

    <!-- Table (Modern Clear Glass) -->
    <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['pagination' => $receipts]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($receipts)]); ?>
         <?php $__env->slot('head', null, []); ?> 
            <th>GRN #</th>
            <th>Receipt Date</th>
            <th>Purchase Order #</th>
            <th><?php echo e(t('messages.suppliers')); ?></th>
            <th><?php echo e(t('messages.warehouse')); ?></th>
            <th class="text-right">Items Received</th>
            <th class="text-right">Total Received Value</th>
            <th>Received By</th>
            <th class="text-right"><?php echo e(t('messages.actions')); ?></th>
         <?php $__env->endSlot(); ?>

        <?php $__empty_1 = true; $__currentLoopData = $receipts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="font-mono font-bold text-slate-900">
                    <a href="<?php echo e(route('goods-receipts.show', $rc->id)); ?>" class="text-sky-600 hover:text-sky-700 transition">
                        <?php echo e($rc->receipt_number); ?>

                    </a>
                </td>
                <td class="text-slate-600 whitespace-nowrap"><?php echo e($rc->receipt_date); ?></td>
                <td class="font-mono text-slate-700 text-xs">
                    <a href="<?php echo e(route('purchase-orders.show', $rc->purchase_order_id)); ?>" class="text-slate-700 hover:text-sky-600 font-semibold transition">
                        <?php echo e($rc->po_number); ?>

                    </a>
                </td>
                <td class="text-slate-800 font-semibold"><?php echo e($rc->supplier_name); ?></td>
                <td class="text-slate-700 font-medium"><?php echo e($rc->warehouse_name); ?></td>
                <td class="text-right font-bold text-slate-800"><?php echo e($rc->total_items); ?></td>
                <td class="text-right font-bold text-slate-900"><?php echo e(format_currency($rc->total_received_value)); ?></td>
                <td class="text-slate-600"><?php echo e($rc->receiver_name); ?></td>
                <td class="text-right whitespace-nowrap">
                    <a href="<?php echo e(route('goods-receipts.show', $rc->id)); ?>" class="btn-action btn-action-view" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if (isset($component)) { $__componentOriginald1264eb3994796452a71a252a3bad0a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald1264eb3994796452a71a252a3bad0a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-empty','data' => ['colspan' => 9,'icon' => 'box-seam','title' => app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាទទួលទំនិញទេ' : 'No Goods Receipts Recorded','description' => app()->getLocale() == 'km' ? 'កត់ត្រាការទទួលទំនិញពីការបញ្ជាទិញរបស់អ្នក' : 'Create a goods receipt when deliveries arrive at the warehouse.','actionUrl' => route('goods-receipts.create'),'actionLabel' => t('messages.create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 9,'icon' => 'box-seam','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាទទួលទំនិញទេ' : 'No Goods Receipts Recorded'),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'កត់ត្រាការទទួលទំនិញពីការបញ្ជាទិញរបស់អ្នក' : 'Create a goods receipt when deliveries arrive at the warehouse.'),'actionUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('goods-receipts.create')),'actionLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(t('messages.create'))]); ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/purchasing/receipts/index.blade.php ENDPATH**/ ?>