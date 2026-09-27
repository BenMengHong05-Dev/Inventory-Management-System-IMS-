<?php $__env->startSection('title', t('messages.warehouses')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900"><?php echo e(t('messages.warehouses')); ?></h1>
            <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'គ្រប់គ្រងទីតាំងស្តុកកណ្តាល សាខា និងមជ្ឈមណ្ឌលចែកចាយ' : 'Manage central storage sites, branches, and distribution centers'); ?></p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="document.getElementById('create_modal').classList.remove('hidden')" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i> <?php echo e(t('messages.create')); ?>

            </button>
        </div>
    </div>

    <!-- Warehouses Table -->
    <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['pagination' => $warehouses]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($warehouses)]); ?>
         <?php $__env->slot('head', null, []); ?> 
            <th><?php echo e(t('messages.code')); ?> & <?php echo e(t('messages.warehouse')); ?></th>
            <th><?php echo e(app()->getLocale() == 'km' ? 'អ្នកគ្រប់គ្រង' : 'Manager'); ?></th>
            <th><?php echo e(app()->getLocale() == 'km' ? 'អាសយដ្ឋាន / ទូរស័ព្ទ' : 'Address / Phone'); ?></th>
            <th class="text-right"><?php echo e(t('messages.locations')); ?></th>
            <th class="text-right"><?php echo e(t('messages.stock')); ?></th>
            <th class="text-right"><?php echo e(t('messages.total_value')); ?></th>
            <th><?php echo e(t('messages.status')); ?></th>
            <th class="text-right"><?php echo e(t('messages.actions')); ?></th>
         <?php $__env->endSlot(); ?>

        <?php $__empty_1 = true; $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td>
                <a href="<?php echo e(route('master.warehouses.show', $w->id)); ?>" class="font-bold text-slate-900 hover:text-sky-600 transition block">
                    <?php echo e($w->name); ?>

                </a>
                <span class="text-[11px] text-slate-400 font-mono"><?php echo e($w->code); ?></span>
            </td>
            <td class="text-slate-700 font-medium">
                <?php echo e($w->manager_name ?: '-'); ?>

            </td>
            <td class="text-slate-600">
                <div><?php echo e($w->address ?: '-'); ?></div>
                <?php if($w->phone): ?>
                    <div class="text-[11px] text-slate-400"><?php echo e($w->phone); ?></div>
                <?php endif; ?>
            </td>
            <td class="text-right font-bold text-slate-800"><?php echo e($w->location_count ?? 0); ?></td>
            <td class="text-right font-bold text-slate-900"><?php echo e(number_format($w->total_stock ?? 0, 0)); ?></td>
            <td class="text-right font-bold text-slate-900"><?php echo e(format_currency($w->total_value ?? 0)); ?></td>
            <td>
                <?php if(strtoupper($w->status) == 'ACTIVE'): ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <?php echo e(t('messages.active')); ?>

                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        <?php echo e(t('messages.inactive')); ?>

                    </span>
                <?php endif; ?>
            </td>
            <td class="text-right space-x-1.5 whitespace-nowrap">
                <a href="<?php echo e(route('master.warehouses.show', $w->id)); ?>" class="btn-action btn-action-view" title="View">
                    <i class="bi bi-eye"></i>
                </a>
                <button onclick="editWarehouse(<?php echo e(json_encode($w)); ?>)" class="btn-action btn-action-edit" title="Edit">
                    <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" action="<?php echo e(route('master.warehouses.destroy', $w->id)); ?>" onsubmit="return confirm('<?php echo e(app()->getLocale() == 'km' ? 'តើអ្នកប្រាកដជាចង់លុបឃ្លាំងនេះ?' : 'Delete this warehouse?'); ?>');" class="inline-block">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn-action btn-action-delete" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if (isset($component)) { $__componentOriginald1264eb3994796452a71a252a3bad0a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald1264eb3994796452a71a252a3bad0a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-empty','data' => ['colspan' => 8,'icon' => 'building','title' => app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យឃ្លាំងទេ' : 'No warehouses found.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 8,'icon' => 'building','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យឃ្លាំងទេ' : 'No warehouses found.')]); ?>
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

<!-- Create Modal -->
<div id="create_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
            <h3 class="text-base font-bold text-gray-900"><?php echo e(app()->getLocale() == 'km' ? 'បន្ថែមឃ្លាំងថ្មី' : 'Add New Warehouse'); ?></h3>
            <button onclick="document.getElementById('create_modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="<?php echo e(route('master.warehouses.store')); ?>" class="space-y-4 text-xs">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1"><?php echo e(t('messages.name')); ?> *</label>
                    <input type="text" name="name" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="e.g. Central Warehouse" required>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1"><?php echo e(t('messages.code')); ?> *</label>
                    <input type="text" name="code" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="e.g. WH-01" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1"><?php echo e(app()->getLocale() == 'km' ? 'អ្នកគ្រប់គ្រង' : 'Manager'); ?></label>
                    <select name="manager_id" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                        <option value=""><?php echo e(app()->getLocale() == 'km' ? '-- ជ្រើសរើសអ្នកគ្រប់គ្រង --' : '-- Select Manager --'); ?></option>
                        <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m->id); ?>"><?php echo e($m->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1"><?php echo e(app()->getLocale() == 'km' ? 'លេខទូរស័ព្ទ' : 'Phone'); ?></label>
                    <input type="text" name="phone" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="+855 ...">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1"><?php echo e(app()->getLocale() == 'km' ? 'អាសយដ្ឋាន' : 'Address'); ?></label>
                <input type="text" name="address" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="Street / Zone">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1"><?php echo e(app()->getLocale() == 'km' ? 'ការពិពណ៌នា' : 'Description'); ?></label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none"></textarea>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1"><?php echo e(t('messages.status')); ?></label>
                <select name="status" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                    <option value="ACTIVE"><?php echo e(t('messages.active')); ?></option>
                    <option value="INACTIVE"><?php echo e(t('messages.inactive')); ?></option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('create_modal').classList.add('hidden')" class="px-4 py-2 border rounded-xl text-gray-600 hover:bg-gray-50"><?php echo e(t('messages.cancel')); ?></button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold"><?php echo e(t('messages.save')); ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
            <h3 class="text-base font-bold text-gray-900"><?php echo e(app()->getLocale() == 'km' ? 'កែប្រែឃ្លាំង' : 'Edit Warehouse'); ?></h3>
            <button onclick="document.getElementById('edit_modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="edit_form" method="POST" action="" class="space-y-4 text-xs">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1"><?php echo e(t('messages.name')); ?> *</label>
                    <input type="text" name="name" id="edit_name" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none" required>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1"><?php echo e(t('messages.code')); ?> *</label>
                    <input type="text" name="code" id="edit_code" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1"><?php echo e(app()->getLocale() == 'km' ? 'អ្នកគ្រប់គ្រង' : 'Manager'); ?></label>
                    <select name="manager_id" id="edit_manager_id" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                        <option value=""><?php echo e(app()->getLocale() == 'km' ? '-- ជ្រើសរើសអ្នកគ្រប់គ្រង --' : '-- Select Manager --'); ?></option>
                        <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m->id); ?>"><?php echo e($m->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1"><?php echo e(app()->getLocale() == 'km' ? 'លេខទូរស័ព្ទ' : 'Phone'); ?></label>
                    <input type="text" name="phone" id="edit_phone" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1"><?php echo e(app()->getLocale() == 'km' ? 'អាសយដ្ឋាន' : 'Address'); ?></label>
                <input type="text" name="address" id="edit_address" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1"><?php echo e(app()->getLocale() == 'km' ? 'ការពិពណ៌នា' : 'Description'); ?></label>
                <textarea name="description" id="edit_description" rows="2" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none"></textarea>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1"><?php echo e(t('messages.status')); ?></label>
                <select name="status" id="edit_status" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                    <option value="ACTIVE"><?php echo e(t('messages.active')); ?></option>
                    <option value="INACTIVE"><?php echo e(t('messages.inactive')); ?></option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('edit_modal').classList.add('hidden')" class="px-4 py-2 border rounded-xl text-gray-600 hover:bg-gray-50"><?php echo e(t('messages.cancel')); ?></button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold"><?php echo e(t('messages.save')); ?></button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function editWarehouse(w) {
    document.getElementById('edit_form').action = `/master/warehouses/${w.id}`;
    document.getElementById('edit_name').value = w.name;
    document.getElementById('edit_code').value = w.code;
    document.getElementById('edit_manager_id').value = w.manager_id || '';
    document.getElementById('edit_address').value = w.address || '';
    document.getElementById('edit_phone').value = w.phone || '';
    document.getElementById('edit_description').value = w.description || '';
    document.getElementById('edit_status').value = (w.status || 'ACTIVE').toUpperCase();
    document.getElementById('edit_modal').classList.remove('hidden');
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/master/warehouses/index.blade.php ENDPATH**/ ?>