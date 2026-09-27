<?php $__env->startSection('title', t('messages.reports')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900"><?php echo e(t('messages.reports')); ?></h1>
            <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'ការវាយតម្លៃស្តុកយ៉ាងទូលំទូលាយ ការសវនកម្មទ្រព្យសកម្ម និងវិភាគការបញ្ជាទិញ' : 'Comprehensive inventory valuation, asset audits, and procurement analytics'); ?></p>
        </div>
    </div>

    <!-- Overview Financial Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm border-l-4 border-l-blue-500">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider"><?php echo e(t('messages.inventory_value')); ?></span>
            <div class="text-2xl font-bold font-mono text-gray-900 mt-2"><?php echo e(format_currency($inventoryValue->total_inventory_cost ?? 0)); ?></div>
            <div class="text-xs text-gray-500 mt-1"><?php echo e(number_format($inventoryValue->total_items_count ?? 0)); ?> <?php echo e(app()->getLocale() == 'km' ? 'មុខទំនិញក្នុងស្តុក' : 'stocked SKU items'); ?></div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm border-l-4 border-l-emerald-500">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider"><?php echo e(app()->getLocale() == 'km' ? 'តម្លៃទ្រព្យសកម្មសរុប' : 'Total Asset Value'); ?></span>
            <div class="text-2xl font-bold font-mono text-gray-900 mt-2"><?php echo e(format_currency($assetValue->total_cost ?? 0)); ?></div>
            <div class="text-xs text-gray-500 mt-1"><?php echo e(number_format($assetValue->asset_count ?? 0)); ?> <?php echo e(app()->getLocale() == 'km' ? 'ទ្រព្យសកម្មបានចុះបញ្ជី' : 'registered equipment/assets'); ?></div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm border-l-4 border-l-amber-500">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider"><?php echo e(app()->getLocale() == 'km' ? 'ការចំណាយបញ្ជាទិញសរុប' : 'Total PO Spend'); ?></span>
            <div class="text-2xl font-bold font-mono text-gray-900 mt-2"><?php echo e(format_currency($poTotal->total_spend ?? 0)); ?></div>
            <div class="text-xs text-gray-500 mt-1"><?php echo e(number_format($poTotal->order_count ?? 0)); ?> <?php echo e(app()->getLocale() == 'km' ? 'ប័ណ្ណបញ្ជាទិញបានចេញ' : 'purchase orders issued'); ?></div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm border-l-4 border-l-purple-500">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider"><?php echo e(app()->getLocale() == 'km' ? 'លំហូរតម្លៃស្តុក' : 'Stock Valuation Flow'); ?></span>
            <div class="text-xs text-gray-600 mt-2 space-y-1">
                <div><?php echo e(app()->getLocale() == 'km' ? 'ទំនិញចូល:' : 'Inflow:'); ?> <span class="font-bold font-mono text-emerald-600"><?php echo e(format_currency($stockInVal->total_in_value ?? 0)); ?></span></div>
                <div><?php echo e(app()->getLocale() == 'km' ? 'ទំនិញចេញ:' : 'Outflow:'); ?> <span class="font-bold font-mono text-rose-600"><?php echo e(format_currency($stockOutVal->total_out_value ?? 0)); ?></span></div>
            </div>
        </div>
    </div>

    <!-- Report Modules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Inventory Report Card -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between space-y-4 hover:shadow-md transition">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="package" class="w-6 h-6"></i>
                </div>
                <h2 class="text-base font-bold text-gray-900"><?php echo e(app()->getLocale() == 'km' ? 'របាយការណ៍វាយតម្លៃស្តុក' : 'Inventory Valuation Report'); ?></h2>
                <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'ការបែងចែកបរិមាណជាក់ស្តែង តម្លៃដើម តម្លៃលក់ និងការជូនដំណឹងអំពីកម្រិតស្តុកតាមឃ្លាំង។' : 'Breakdown of on-hand quantities, cost prices, selling valuations, and safety reorder alerts across warehouses.'); ?></p>
            </div>
            <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                <a href="<?php echo e(route('reports.inventory')); ?>" class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center justify-center transition">
                    <i data-lucide="bar-chart-2" class="w-4 h-4 mr-1.5"></i>
                    <?php echo e(app()->getLocale() == 'km' ? 'មើលរបាយការណ៍' : 'View Report'); ?>

                </a>
                <a href="<?php echo e(route('reports.inventory.export')); ?>" class="px-3 py-2 border rounded-xl text-gray-600 hover:bg-gray-50 transition" title="Export CSV">
                    <i data-lucide="download" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        <!-- Asset Report Card -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between space-y-4 hover:shadow-md transition">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="laptop" class="w-6 h-6"></i>
                </div>
                <h2 class="text-base font-bold text-gray-900"><?php echo e(app()->getLocale() == 'km' ? 'របាយការណ៍ទ្រព្យសកម្ម និងការកាន់កាប់' : 'Asset & Custody Report'); ?></h2>
                <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'បញ្ជីឈ្មោះទ្រព្យសកម្មតាមស្ថានភាព ការដាក់ពង្រាយតាមនាយកដ្ឋាន ការប្រគល់ជូនបុគ្គលិក ការផុតកំណត់ធានា និងការចំណាយលើការថែទាំ។' : 'Asset register by status, department deployment, custodian assignment, warranty expiration, and maintenance costs.'); ?></p>
            </div>
            <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                <a href="<?php echo e(route('reports.assets')); ?>" class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center justify-center transition">
                    <i data-lucide="bar-chart-2" class="w-4 h-4 mr-1.5"></i>
                    <?php echo e(app()->getLocale() == 'km' ? 'មើលរបាយការណ៍' : 'View Report'); ?>

                </a>
                <a href="<?php echo e(route('reports.assets.export')); ?>" class="px-3 py-2 border rounded-xl text-gray-600 hover:bg-gray-50 transition" title="Export CSV">
                    <i data-lucide="download" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        <!-- Purchasing Report Card -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between space-y-4 hover:shadow-md transition">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                </div>
                <h2 class="text-base font-bold text-gray-900"><?php echo e(app()->getLocale() == 'km' ? 'របាយការណ៍បញ្ជាទិញ និងអ្នកផ្គត់ផ្គង់' : 'Purchasing & Vendor Spend'); ?></h2>
                <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'អត្រាបំពេញការបញ្ជាទិញ រយៈពេលដឹកនាំរបស់អ្នកផ្គត់ផ្គង់ ការទទួលទំនិញខ្លះៗ និន្នាការចំណាយ និងសមតុល្យអ្នកផ្គត់ផ្គង់។' : 'PO fulfillment rates, supplier lead times, partial receipts, spending trends, and outstanding vendor balances.'); ?></p>
            </div>
            <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                <a href="<?php echo e(route('reports.purchasing')); ?>" class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center justify-center transition">
                    <i data-lucide="bar-chart-2" class="w-4 h-4 mr-1.5"></i>
                    <?php echo e(app()->getLocale() == 'km' ? 'មើលរបាយការណ៍' : 'View Report'); ?>

                </a>
                <a href="<?php echo e(route('reports.purchasing.export')); ?>" class="px-3 py-2 border rounded-xl text-gray-600 hover:bg-gray-50 transition" title="Export CSV">
                    <i data-lucide="download" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/reports/index.blade.php ENDPATH**/ ?>