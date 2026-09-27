<?php $__env->startSection('title', app()->getLocale() == 'km' ? 'របាយការណ៍វាយតម្លៃស្តុក' : 'Inventory Valuation Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900"><?php echo e(app()->getLocale() == 'km' ? 'របាយការណ៍វាយតម្លៃស្តុក' : 'Inventory Valuation Report'); ?></h1>
            <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'បរិមាណស្តុកតាមពេលវេលាជាក់ស្តែង និងការវាយតម្លៃហិរញ្ញវត្ថុតាមឃ្លាំង និងប្រភេទទំនិញ' : 'Real-time stock quantities and financial valuation by warehouse and category'); ?></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('reports.inventory.export', request()->query())); ?>" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center transition">
                <i data-lucide="download" class="w-4 h-4 mr-1.5"></i> <?php echo e(t('messages.export')); ?> CSV
            </a>
            <a href="<?php echo e(route('reports.index')); ?>" class="px-3.5 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold shadow-sm flex items-center transition">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5 text-gray-500"></i> <?php echo e(app()->getLocale() == 'km' ? 'មជ្ឈមណ្ឌល' : 'Hub'); ?>

            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="<?php echo e(route('reports.inventory')); ?>" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <select name="category_id" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                    <option value=""><?php echo e(t('messages.all')); ?> <?php echo e(t('messages.categories')); ?></option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php echo e($categoryId == $c->id ? 'selected' : ''); ?>><?php echo e(localized_name($c, 'name')); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <select name="warehouse_id" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                    <option value=""><?php echo e(t('messages.all')); ?> <?php echo e(t('messages.warehouses')); ?></option>
                    <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($w->id); ?>" <?php echo e($warehouseId == $w->id ? 'selected' : ''); ?>><?php echo e($w->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold flex items-center justify-center">
                    <i data-lucide="filter" class="w-3.5 h-3.5 mr-1.5"></i> <?php echo e(t('messages.filter')); ?>

                </button>
                <a href="<?php echo e(route('reports.inventory')); ?>" class="px-3 py-2 border rounded-xl text-gray-500 hover:bg-gray-50 flex items-center justify-center">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Report Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-400 uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="py-3 px-4">SKU & <?php echo e(t('messages.code')); ?></th>
                        <th class="py-3 px-4"><?php echo e(t('messages.items')); ?></th>
                        <th class="py-3 px-4"><?php echo e(t('messages.categories')); ?></th>
                        <th class="py-3 px-4"><?php echo e(t('messages.warehouse')); ?></th>
                        <th class="py-3 px-4 text-right"><?php echo e(app()->getLocale() == 'km' ? 'មានជាក់ស្តែង' : 'Qty On Hand'); ?></th>
                        <th class="py-3 px-4 text-right"><?php echo e(app()->getLocale() == 'km' ? 'បានកក់ទុក' : 'Reserved'); ?></th>
                        <th class="py-3 px-4 text-right"><?php echo e(app()->getLocale() == 'km' ? 'អាចប្រើបាន' : 'Available'); ?></th>
                        <th class="py-3 px-4 text-right"><?php echo e(t('messages.unit_cost')); ?></th>
                        <th class="py-3 px-4 text-right"><?php echo e(t('messages.total_value')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $sumValuation = 0; $sumQty = 0; ?>
                    <?php $__empty_1 = true; $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php 
                        $sumValuation += $r->total_value; 
                        $sumQty += $r->quantity;
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 font-mono font-bold text-gray-900"><?php echo e($r->item_code ?? $r->sku); ?></td>
                        <td class="py-3 px-4 font-semibold text-gray-800"><?php echo e(localized_name($r, 'item_name')); ?></td>
                        <td class="py-3 px-4 text-gray-600"><?php echo e(localized_name($r, 'category_name')); ?></td>
                        <td class="py-3 px-4 text-gray-600"><?php echo e($r->warehouse_name); ?></td>
                        <td class="py-3 px-4 text-right font-mono font-medium text-gray-800"><?php echo e(number_format($r->quantity, 2)); ?></td>
                        <td class="py-3 px-4 text-right font-mono text-amber-600"><?php echo e(number_format($r->reserved_quantity, 2)); ?></td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-emerald-600"><?php echo e(number_format($r->available_quantity ?? $r->available_qty, 2)); ?></td>
                        <td class="py-3 px-4 text-right font-mono text-gray-600"><?php echo e(format_currency($r->unit_cost ?? $r->cost_price)); ?></td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-gray-900"><?php echo e(format_currency($r->total_value)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center py-8 text-gray-400">
                            <?php echo e(app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យស្តុកត្រូវនឹងលក្ខខណ្ឌត្រងទេ' : 'No inventory items found matching filter criteria.'); ?>

                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <?php if(count($report) > 0): ?>
                <tfoot class="bg-gray-50 font-semibold border-t-2 border-gray-100">
                    <tr>
                        <td colspan="4" class="py-3 px-4 text-right font-bold text-gray-700"><?php echo e(app()->getLocale() == 'km' ? 'សរុបរួម:' : 'Report Summary:'); ?></td>
                        <td class="py-3 px-4 text-right font-mono text-gray-800 font-bold"><?php echo e(number_format($sumQty, 2)); ?></td>
                        <td colspan="3"></td>
                        <td class="py-3 px-4 text-right font-mono text-emerald-700 font-bold text-sm"><?php echo e(format_currency($sumValuation)); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
        <?php if(method_exists($report, 'hasPages') && $report->hasPages()): ?>
            <div class="p-4 border-t border-gray-100 flex justify-end">
                <?php echo e($report->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/reports/inventory.blade.php ENDPATH**/ ?>