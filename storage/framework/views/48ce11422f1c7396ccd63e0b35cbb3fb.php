<?php $__env->startSection('title', t('brands') . ' - IMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    
    <!-- MAIN COMPONENT CARD (Single Unified, Modern, Clear & Small Header) -->
    <div class="card overflow-hidden">
        
        <!-- COMPONENT HEADER (Search on Left, Filter on Right, Flat with No Shadow) -->
        <form method="GET" action="<?php echo e(route('master.brands.index')); ?>" class="px-5 py-3.5 flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-100 bg-white">
            
            <!-- Left: Title & Subtitle + Search Input on Left -->
            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#ff5a36] to-amber-500 text-white flex items-center justify-center flex-shrink-0">
                        <i data-lucide="tag" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-900 leading-tight"><?php echo e(t('brands')); ?></h2>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                <?php echo e(method_exists($brands, 'total') ? $brands->total() : count($brands)); ?> Total
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-medium leading-tight mt-0.5"><?php echo e(t('manage')); ?> <?php echo e(t('brands')); ?> &amp; <?php echo e(t('manufacturers') ?? 'Manufacturers'); ?></p>
                    </div>
                </div>

                <!-- Search Input Pill on the LEFT -->
                <div class="relative">
                    <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="<?php echo e(t('search')); ?>..." 
                           class="pl-8 pr-3 py-1.5 rounded-full bg-slate-100 border border-slate-200/80 text-xs text-slate-800 outline-none focus:bg-white focus:border-[#ff5a36] w-48 sm:w-56 transition">
                </div>
            </div>

            <!-- Right: Status Filter & Button Filter on the RIGHT + Add Button -->
            <div class="flex flex-wrap items-center gap-2 justify-end">
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
                <a href="<?php echo e(route('master.brands.index')); ?>" class="text-xs text-rose-500 hover:underline px-1">
                    <?php echo e(t('clear')); ?>

                </a>
                <?php endif; ?>

                <!-- Add Button Pill -->
                <button type="button" onclick="openModal('create_modal')" class="btn-primary text-xs py-1.5 px-3.5 rounded-full flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span><?php echo e(t('add_new')); ?></span>
                </button>
            </div>
        </form>

        <!-- BRANDS DATA TABLE -->
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-10 text-center">
                            <input type="checkbox" class="rounded text-[#ff5a36] focus:ring-[#ff5a36] border-slate-300">
                        </th>
                        <th><?php echo e(t('code')); ?></th>
                        <th><?php echo e(t('name')); ?></th>
                        <th><?php echo e(t('description')); ?></th>
                        <th><?php echo e(t('items')); ?></th>
                        <th><?php echo e(t('status')); ?></th>
                        <th class="text-right"><?php echo e(t('actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="rounded text-[#ff5a36] focus:ring-[#ff5a36] border-slate-300">
                        </td>
                        <td>
                            <span class="font-mono text-xs font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md">
                                <?php echo e($b->code); ?>

                            </span>
                        </td>
                        <td class="font-bold text-slate-900 text-xs"><?php echo e($b->name); ?></td>
                        <td class="text-xs text-slate-500 max-w-xs truncate"><?php echo e($b->description ?: '-'); ?></td>
                        <td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                <i data-lucide="package" class="w-3 h-3 mr-1 text-slate-400"></i> <?php echo e($b->items_count ?? $b->item_count ?? 0); ?>

                            </span>
                        </td>
                        <td>
                            <?php if(strtoupper($b->status ?? '') === 'ACTIVE'): ?>
                                <span class="status-dot-completed"><?php echo e(t('active')); ?></span>
                            <?php else: ?>
                                <span class="status-dot-pending"><?php echo e(t('inactive')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button onclick="editBrand(<?php echo e(json_encode($b)); ?>)" class="btn-icon text-slate-400 hover:text-slate-900 hover:bg-slate-100" title="<?php echo e(t('edit')); ?>">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
                                <form method="POST" action="<?php echo e(route('master.brands.destroy', $b->id)); ?>" onsubmit="return confirm('<?php echo e(t('confirm_delete')); ?>');" class="inline">
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
                        <td colspan="7" class="text-center py-10 text-slate-400">
                            <i data-lucide="tag" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                            <p class="text-xs"><?php echo e(t('no_data_available')); ?></p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(method_exists($brands, 'links')): ?>
        <div class="px-5 py-3 border-t border-slate-100">
            <?php echo e($brands->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Modal (Smooth Animation & Rounded) -->
<div id="create_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-md hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 space-y-4 border border-slate-100">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-[#ff5a36]"></i> <?php echo e(t('add_new')); ?> <?php echo e(t('brands')); ?>

            </h3>
            <button onclick="closeModal('create_modal')" class="p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form method="POST" action="<?php echo e(route('master.brands.store')); ?>" class="space-y-3.5">
            <?php echo csrf_field(); ?>
            <div>
                <label class="form-label required"><?php echo e(t('code')); ?></label>
                <input type="text" name="code" class="form-input uppercase font-mono" placeholder="e.g. DELL, HP, APPLE" required>
            </div>
            <div>
                <label class="form-label required"><?php echo e(t('name')); ?></label>
                <input type="text" name="name" class="form-input" placeholder="e.g. Dell Inc." required>
            </div>
            <div>
                <label class="form-label required"><?php echo e(t('status')); ?></label>
                <select name="status" class="form-input" required>
                    <option value="ACTIVE"><?php echo e(t('active')); ?></option>
                    <option value="INACTIVE"><?php echo e(t('inactive')); ?></option>
                </select>
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
    <div class="bg-white rounded-3xl w-full max-w-md p-6 space-y-4 border border-slate-100">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="pencil" class="w-4 h-4 text-[#ff5a36]"></i> <?php echo e(t('edit')); ?> <?php echo e(t('brands')); ?>

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
            <div>
                <label class="form-label required"><?php echo e(t('name')); ?></label>
                <input type="text" name="name" id="edit_name" class="form-input" required>
            </div>
            <div>
                <label class="form-label required"><?php echo e(t('status')); ?></label>
                <select name="status" id="edit_status" class="form-input" required>
                    <option value="ACTIVE"><?php echo e(t('active')); ?></option>
                    <option value="INACTIVE"><?php echo e(t('inactive')); ?></option>
                </select>
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
function editBrand(b) {
    document.getElementById('edit_form').action = `/master/brands/${b.id}`;
    document.getElementById('edit_code').value = b.code || '';
    document.getElementById('edit_name').value = b.name || '';
    document.getElementById('edit_description').value = b.description || '';
    document.getElementById('edit_status').value = (b.status || 'ACTIVE').toUpperCase();
    openModal('edit_modal');
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/master/brands/index.blade.php ENDPATH**/ ?>