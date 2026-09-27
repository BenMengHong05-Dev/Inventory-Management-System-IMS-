<?php $__env->startSection('title', t('categories') . ' - IMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    
    <!-- MAIN COMPONENT CARD (Single Unified, Modern, Clear & Small Header) -->
    <div class="card overflow-hidden">
        
        <!-- COMPONENT HEADER (Search on Left, Filter on Right, Flat with No Shadow) -->
        <form method="GET" action="<?php echo e(route('categories.index')); ?>" class="px-5 py-3.5 flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-100 bg-white">
            
            <!-- Left: Title & Subtitle + Search Input on Left -->
            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#ff5a36] to-amber-500 text-white flex items-center justify-center flex-shrink-0">
                        <i data-lucide="folder-tree" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-900 leading-tight"><?php echo e(t('categories')); ?></h2>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                <?php echo e(method_exists($categories, 'total') ? $categories->total() : count($categories)); ?> Total
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-medium leading-tight mt-0.5"><?php echo e(t('manage')); ?> <?php echo e(t('categories')); ?></p>
                    </div>
                </div>

                <!-- Search Input Pill on the LEFT -->
                <div class="relative">
                    <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="<?php echo e(t('search')); ?>..." 
                           class="pl-8 pr-3 py-1.5 rounded-full bg-slate-100 border border-slate-200/80 text-xs text-slate-800 outline-none focus:bg-white focus:border-[#ff5a36] w-48 sm:w-56 transition">
                </div>
            </div>

            <!-- Right: Filter Dropdown & Button Filter on the RIGHT + Add Button -->
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <!-- Status Filter Pill -->
                <select name="status" class="py-1.5 pl-3 pr-8 rounded-full bg-slate-100 border border-slate-200/80 text-xs font-semibold text-slate-700 outline-none cursor-pointer">
                    <option value=""><?php echo e(t('all')); ?> <?php echo e(t('status')); ?></option>
                    <option value="ACTIVE" <?php echo e(($status ?? '') === 'ACTIVE' ? 'selected' : ''); ?>><?php echo e(t('active')); ?></option>
                    <option value="INACTIVE" <?php echo e(($status ?? '') === 'INACTIVE' ? 'selected' : ''); ?>><?php echo e(t('inactive')); ?></option>
                </select>

                <!-- Button Filter on the RIGHT -->
                <button type="submit" class="px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 border border-slate-200/80 text-xs font-semibold text-slate-700 flex items-center gap-1.5 transition">
                    <i data-lucide="filter" class="w-3 h-3 text-slate-500"></i>
                    <span><?php echo e(t('filter')); ?></span>
                </button>

                <?php if(!empty($search) || !empty($status)): ?>
                <a href="<?php echo e(route('categories.index')); ?>" class="text-xs text-rose-500 hover:underline px-1">
                    <?php echo e(t('clear')); ?>

                </a>
                <?php endif; ?>

                <!-- Add Button Pill (Coral Accent) -->
                <button type="button" onclick="openModal('create_modal')" class="btn-primary text-xs py-1.5 px-3.5 rounded-full flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span><?php echo e(t('add_new')); ?></span>
                </button>
            </div>
        </form>

        <!-- CATEGORIES DATA TABLE (Compact, Clean & Modern) -->
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-10 text-center">
                            <input type="checkbox" class="rounded text-[#ff5a36] focus:ring-[#ff5a36] border-slate-300">
                        </th>
                        <th><?php echo e(t('code')); ?></th>
                        <th><?php echo e(t('name')); ?></th>
                        <th><?php echo e(t('parent_category') ?? 'Parent Category'); ?></th>
                        <th><?php echo e(t('items')); ?></th>
                        <th><?php echo e(t('status')); ?></th>
                        <th class="text-right"><?php echo e(t('actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="rounded text-[#ff5a36] focus:ring-[#ff5a36] border-slate-300">
                        </td>
                        <td>
                            <span class="font-mono text-xs font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md">
                                <?php echo e($c->code); ?>

                            </span>
                        </td>
                        <td>
                            <div class="font-bold text-slate-900 text-xs"><?php echo e(localized_name($c, 'name')); ?></div>
                            <div class="text-[10px] text-slate-400">
                                <?php echo e($c->name_en ?? ''); ?> <?php echo e(!empty($c->name_km) && $c->name_km !== $c->name_en ? ' • ' . $c->name_km : ''); ?>

                            </div>
                            <?php if($c->description): ?>
                                <div class="text-[10px] text-slate-400 truncate max-w-xs mt-0.5"><?php echo e($c->description); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-xs text-slate-600">
                            <?php echo e(localized_name($c, 'parent_name') ?: '-'); ?>

                        </td>
                        <td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                <i data-lucide="package" class="w-3 h-3 mr-1 text-slate-400"></i> <?php echo e($c->item_count ?? 0); ?>

                            </span>
                        </td>
                        <td>
                            <?php if(strtoupper($c->status ?? '') === 'ACTIVE'): ?>
                                <span class="status-dot-completed"><?php echo e(t('active')); ?></span>
                            <?php else: ?>
                                <span class="status-dot-pending"><?php echo e(t('inactive')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button onclick="editCategory(<?php echo e(json_encode($c)); ?>)" class="btn-icon text-slate-400 hover:text-slate-900 hover:bg-slate-100" title="<?php echo e(t('edit')); ?>">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
                                <form method="POST" action="<?php echo e(route('master.categories.destroy', $c->id)); ?>" onsubmit="return confirm('<?php echo e(t('confirm_delete')); ?>');" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-icon text-slate-400 hover:text-rose-600 hover:bg-rose-50" title="<?php echo e(t('delete')); ?>">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-12 text-slate-400">
                            <div class="w-12 h-12 rounded-2xl bg-orange-50 text-[#ff5a36] flex items-center justify-center mx-auto mb-2">
                                <i data-lucide="folder-tree" class="w-6 h-6"></i>
                            </div>
                            <p class="font-bold text-slate-700 text-xs mb-0.5"><?php echo e(t('no_data_available')); ?></p>
                            <p class="text-[11px] text-slate-400 max-w-sm mx-auto mb-3">
                                <?php echo e(app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យទេ - អ្នកអាចបន្ថែមប្រភេទទំនិញថ្មីឥឡូវនេះ' : 'No records found. You can start creating your own categories now.'); ?>

                            </p>
                            <button onclick="openModal('create_modal')" class="btn-primary text-xs py-1.5 px-3 rounded-full">
                                <i data-lucide="plus" class="w-3.5 h-3.5 mr-1"></i> <?php echo e(t('add_new')); ?> <?php echo e(t('categories')); ?>

                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(method_exists($categories, 'links')): ?>
        <div class="px-5 py-3 border-t border-slate-100">
            <?php echo e($categories->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Modal (Smooth Animation & Rounded) -->
<div id="create_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-md hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg p-6 space-y-4 border border-slate-100">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-[#ff5a36]"></i> <?php echo e(t('add_new')); ?> <?php echo e(t('categories')); ?>

            </h3>
            <button onclick="closeModal('create_modal')" class="p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form method="POST" action="<?php echo e(route('master.categories.store')); ?>" class="space-y-3.5">
            <?php echo csrf_field(); ?>
            <div>
                <label class="form-label required"><?php echo e(t('code')); ?></label>
                <input type="text" name="code" class="form-input uppercase font-mono" placeholder="e.g. CAT-001" required>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label required"><?php echo e(t('name')); ?> (Khmer)</label>
                    <input type="text" name="name_km" class="form-input" placeholder="ឧ. គ្រឿងអេឡិចត្រូនិច" required>
                </div>
                <div>
                    <label class="form-label required"><?php echo e(t('name')); ?> (English)</label>
                    <input type="text" name="name_en" class="form-input" placeholder="e.g. Electronics" required>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label"><?php echo e(t('parent_category') ?? 'Parent Category'); ?></label>
                    <select name="parent_id" class="form-input">
                        <option value="">-- <?php echo e(t('none') ?? 'None'); ?> --</option>
                        <?php $__currentLoopData = $parentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id); ?>"><?php echo e(localized_name($p, 'name')); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="form-label required"><?php echo e(t('status')); ?></label>
                    <select name="status" class="form-input" required>
                        <option value="ACTIVE"><?php echo e(t('active')); ?></option>
                        <option value="INACTIVE"><?php echo e(t('inactive')); ?></option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label"><?php echo e(t('description')); ?></label>
                <textarea name="description" rows="2" class="form-input" placeholder="<?php echo e(t('notes')); ?>..."></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('create_modal')" class="btn-secondary text-xs"><?php echo e(t('cancel')); ?></button>
                <button type="submit" class="btn-primary text-xs"><?php echo e(t('save')); ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal (Smooth Animation & Rounded) -->
<div id="edit_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-md hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg p-6 space-y-4 border border-slate-100">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="pencil" class="w-4 h-4 text-[#ff5a36]"></i> <?php echo e(t('edit')); ?> <?php echo e(t('categories')); ?>

            </h3>
            <button onclick="closeModal('edit_modal')" class="p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="edit_form" method="POST" action="" class="space-y-3.5">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div>
                <label class="form-label required"><?php echo e(t('code')); ?></label>
                <input type="text" name="code" id="edit_code" class="form-input uppercase font-mono" required>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label required"><?php echo e(t('name')); ?> (Khmer)</label>
                    <input type="text" name="name_km" id="edit_name_km" class="form-input" required>
                </div>
                <div>
                    <label class="form-label required"><?php echo e(t('name')); ?> (English)</label>
                    <input type="text" name="name_en" id="edit_name_en" class="form-input" required>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label"><?php echo e(t('parent_category') ?? 'Parent Category'); ?></label>
                    <select name="parent_id" id="edit_parent_id" class="form-input">
                        <option value="">-- <?php echo e(t('none') ?? 'None'); ?> --</option>
                        <?php $__currentLoopData = $parentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id); ?>"><?php echo e(localized_name($p, 'name')); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="form-label required"><?php echo e(t('status')); ?></label>
                    <select name="status" id="edit_status" class="form-input" required>
                        <option value="ACTIVE"><?php echo e(t('active')); ?></option>
                        <option value="INACTIVE"><?php echo e(t('inactive')); ?></option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label"><?php echo e(t('description')); ?></label>
                <textarea name="description" id="edit_description" rows="2" class="form-input"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('edit_modal')" class="btn-secondary text-xs"><?php echo e(t('cancel')); ?></button>
                <button type="submit" class="btn-primary text-xs"><?php echo e(t('save')); ?></button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function editCategory(c) {
    document.getElementById('edit_form').action = `/master/categories/${c.id}`;
    document.getElementById('edit_code').value = c.code || '';
    document.getElementById('edit_name_km').value = c.name_km || c.name_en || '';
    document.getElementById('edit_name_en').value = c.name_en || c.name_km || '';
    document.getElementById('edit_parent_id').value = c.parent_id || '';
    document.getElementById('edit_description').value = c.description || '';
    document.getElementById('edit_status').value = (c.status || 'ACTIVE').toUpperCase();
    openModal('edit_modal');
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/master/categories/index.blade.php ENDPATH**/ ?>