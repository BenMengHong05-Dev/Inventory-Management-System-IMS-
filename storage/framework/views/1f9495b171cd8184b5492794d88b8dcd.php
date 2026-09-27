<?php $__env->startSection('title', 'Edit Item: ' . $item->name_en); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Item: <?php echo e($item->name_en); ?></h1>
            <p class="text-xs text-gray-500">Update item details, thresholds, and categorization</p>
        </div>
        <div class="flex space-x-2">
            <a href="<?php echo e(route('items.show', $item->id)); ?>" class="px-3.5 py-1.5 border rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50">
                View Details
            </a>
            <a href="<?php echo e(route('items.index')); ?>" class="px-3.5 py-1.5 border rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50">
                &larr; Back
            </a>
        </div>
    </div>

    <form action="<?php echo e(route('items.update', $item->id)); ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- General Info -->
        <div>
            <h3 class="text-sm font-bold text-gray-800 border-b pb-2 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Item Code / SKU <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="<?php echo e(old('code', $item->code)); ?>" required
                           class="w-full px-3 py-2 border rounded-xl font-mono uppercase focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Barcode</label>
                    <input type="text" name="barcode" value="<?php echo e(old('barcode', $item->barcode)); ?>"
                           class="w-full px-3 py-2 border rounded-xl font-mono focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Item Type <span class="text-red-500">*</span></label>
                    <select name="item_type" required class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                        <option value="CONSUMABLE" <?php echo e(old('item_type', $item->item_type) === 'CONSUMABLE' ? 'selected' : ''); ?>>Consumable</option>
                        <option value="NON_CONSUMABLE" <?php echo e(old('item_type', $item->item_type) === 'NON_CONSUMABLE' ? 'selected' : ''); ?>>Non-Consumable</option>
                        <option value="ASSET" <?php echo e(old('item_type', $item->item_type) === 'ASSET' ? 'selected' : ''); ?>>Fixed Asset</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block font-semibold text-gray-700 mb-1">English Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name_en" value="<?php echo e(old('name_en', $item->name_en)); ?>" required
                           class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Khmer Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name_km" value="<?php echo e(old('name_km', $item->name_km)); ?>" required
                           class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- Categorization & Unit -->
        <div>
            <h3 class="text-sm font-bold text-gray-800 border-b pb-2 mb-4">Classification & Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                        <option value="">Select Category</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id', $item->category_id) == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name_en); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Sub-Category</label>
                    <select name="sub_category_id" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                        <option value="">Select Sub-Category</option>
                        <?php $__currentLoopData = $subCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($scat->id); ?>" <?php echo e(old('sub_category_id', $item->sub_category_id) == $scat->id ? 'selected' : ''); ?>><?php echo e($scat->name_en); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Brand</label>
                    <select name="brand_id" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                        <option value="">Select Brand</option>
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($brd->id); ?>" <?php echo e(old('brand_id', $item->brand_id) == $brd->id ? 'selected' : ''); ?>><?php echo e($brd->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Unit of Measure</label>
                    <select name="unit_id" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                        <option value="">Select Unit</option>
                        <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>" <?php echo e(old('unit_id', $item->unit_id) == $u->id ? 'selected' : ''); ?>><?php echo e($u->name_en); ?> (<?php echo e($u->code); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block font-semibold text-gray-700 mb-1">Model / Part Number</label>
                    <input type="text" name="model" value="<?php echo e(old('model', $item->model)); ?>"
                           class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Default Unit Cost ($) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="default_unit_cost" value="<?php echo e(old('default_unit_cost', $item->default_unit_cost)); ?>" required
                           class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                        <option value="ACTIVE" <?php echo e(old('status', $item->status) === 'ACTIVE' ? 'selected' : ''); ?>>Active</option>
                        <option value="INACTIVE" <?php echo e(old('status', $item->status) === 'INACTIVE' ? 'selected' : ''); ?>>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Inventory Thresholds -->
        <div>
            <h3 class="text-sm font-bold text-gray-800 border-b pb-2 mb-4">Stock Control Thresholds</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Minimum Stock Level</label>
                    <input type="number" step="1" name="min_stock" value="<?php echo e(old('min_stock', $item->min_stock)); ?>" required
                           class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Reorder Point Level <span class="text-red-500">*</span></label>
                    <input type="number" step="1" name="reorder_level" value="<?php echo e(old('reorder_level', $item->reorder_level)); ?>" required
                           class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Maximum Stock Level</label>
                    <input type="number" step="1" name="max_stock" value="<?php echo e(old('max_stock', $item->max_stock)); ?>" required
                           class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- Media & Notes -->
        <div>
            <h3 class="text-sm font-bold text-gray-800 border-b pb-2 mb-4">Product Image & Description</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Change Photo</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Description / Notes</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none"><?php echo e(old('description', $item->description)); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="pt-4 border-t flex justify-end space-x-3">
            <a href="<?php echo e(route('items.index')); ?>" class="px-5 py-2.5 border rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition">
                Update Item
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/items/edit.blade.php ENDPATH**/ ?>