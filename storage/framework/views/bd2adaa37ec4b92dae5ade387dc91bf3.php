<?php $__env->startSection('title', t('departments') . ' - IMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900"><?php echo e(t('departments')); ?></h1>
            <p class="text-xs text-gray-500"><?php echo e(t('manage')); ?> <?php echo e(t('departments')); ?></p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="document.getElementById('create_modal').classList.remove('hidden')" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                <?php echo e(t('add_new')); ?> <?php echo e(t('departments')); ?>

            </button>
        </div>
    </div>

    <!-- Search & Filter Bar (No white card background, no shadow, search on left, filters on right) -->
    <form method="GET" action="<?php echo e(route('master.departments.index')); ?>" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['type' => 'text','name' => 'search','value' => ''.e($search ?? '').'','placeholder' => ''.e(t('search')).'...','wrapperClass' => '!mb-0','icon' => 'bi bi-search']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','name' => 'search','value' => ''.e($search ?? '').'','placeholder' => ''.e(t('search')).'...','wrapperClass' => '!mb-0','icon' => 'bi bi-search']); ?>
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
            <div class="w-full sm:w-36">
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
                    <option value=""><?php echo e(t('all')); ?> <?php echo e(t('status')); ?></option>
                    <option value="ACTIVE" <?php echo e(($status ?? '') === 'ACTIVE' ? 'selected' : ''); ?>><?php echo e(t('active')); ?></option>
                    <option value="INACTIVE" <?php echo e(($status ?? '') === 'INACTIVE' ? 'selected' : ''); ?>><?php echo e(t('inactive')); ?></option>
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
                <?php echo e(t('filter')); ?>

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
            <?php if(!empty($search) || !empty($status)): ?>
            <a href="<?php echo e(route('master.departments.index')); ?>" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Clear">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Departments Table -->
    <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['pagination' => $departments]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($departments)]); ?>
         <?php $__env->slot('head', null, []); ?> 
            <th><?php echo e(t('code')); ?></th>
            <th><?php echo e(t('name')); ?></th>
            <th><?php echo e(t('manager') ?? 'Manager'); ?></th>
            <th><?php echo e(t('employees')); ?></th>
            <th><?php echo e(t('description')); ?></th>
            <th><?php echo e(t('status')); ?></th>
            <th class="text-right"><?php echo e(t('actions')); ?></th>
         <?php $__env->endSlot(); ?>

        <?php $__empty_1 = true; $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td class="font-mono text-xs font-bold text-sky-600"><?php echo e($d->code); ?></td>
            <td>
                <div class="font-bold text-slate-800"><?php echo e(localized_name($d, 'name')); ?></div>
                <div class="text-xs text-slate-400"><?php echo e($d->name_en ?? ''); ?> <?php echo e(!empty($d->name_km) && $d->name_km !== $d->name_en ? ' • ' . $d->name_km : ''); ?></div>
            </td>
            <td class="text-xs font-medium text-slate-700">
                <?php echo e($d->manager_name ?: '-'); ?>

            </td>
            <td>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                    <i class="bi bi-people mr-1"></i> <?php echo e($d->employee_count ?? 0); ?>

                </span>
            </td>
            <td class="text-xs text-slate-500 max-w-xs truncate"><?php echo e($d->description ?: '-'); ?></td>
            <td>
                <?php if(strtoupper($d->status ?? '') === 'ACTIVE'): ?>
                    <span class="badge bg-emerald-100 text-emerald-800"><?php echo e(t('active')); ?></span>
                <?php else: ?>
                    <span class="badge bg-slate-100 text-slate-700"><?php echo e(t('inactive')); ?></span>
                <?php endif; ?>
            </td>
            <td class="text-right space-x-1 whitespace-nowrap">
                <button onclick="editDepartment(<?php echo e(json_encode($d)); ?>)" class="btn-secondary !p-1.5 text-blue-600" title="<?php echo e(t('edit')); ?>">
                    <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" action="<?php echo e(route('master.departments.destroy', $d->id)); ?>" onsubmit="return confirm('<?php echo e(t('confirm_delete')); ?>');" class="inline-block">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn-danger !p-1.5" title="<?php echo e(t('delete')); ?>">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if (isset($component)) { $__componentOriginald1264eb3994796452a71a252a3bad0a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald1264eb3994796452a71a252a3bad0a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-empty','data' => ['colspan' => 7,'icon' => 'bi bi-building','title' => t('no_data_available')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 7,'icon' => 'bi bi-building','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(t('no_data_available'))]); ?>
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
<div id="create_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4 m-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-plus-circle text-sky-600"></i> <?php echo e(t('add_new')); ?> <?php echo e(t('departments')); ?>

            </h3>
            <button onclick="document.getElementById('create_modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form method="POST" action="<?php echo e(route('master.departments.store')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="form-label required"><?php echo e(t('code')); ?></label>
                <input type="text" name="code" class="form-input" placeholder="e.g. IT, HR, ACC" required>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label required"><?php echo e(t('name')); ?> (Khmer)</label>
                    <input type="text" name="name_km" class="form-input" placeholder="ឧ. ព័ត៌មានវិទ្យា" required>
                </div>
                <div>
                    <label class="form-label required"><?php echo e(t('name')); ?> (English)</label>
                    <input type="text" name="name_en" class="form-input" placeholder="e.g. Information Technology" required>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label"><?php echo e(t('manager') ?? 'Manager'); ?></label>
                    <select name="manager_id" class="form-input">
                        <option value="">-- <?php echo e(t('select')); ?> --</option>
                        <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m->id); ?>"><?php echo e($m->name); ?></option>
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
                <button type="button" onclick="document.getElementById('create_modal').classList.add('hidden')" class="btn-secondary"><?php echo e(t('cancel')); ?></button>
                <button type="submit" class="btn-primary"><?php echo e(t('save')); ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4 m-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-pencil-square text-sky-600"></i> <?php echo e(t('edit')); ?> <?php echo e(t('departments')); ?>

            </h3>
            <button onclick="document.getElementById('edit_modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="edit_form" method="POST" action="" class="space-y-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div>
                <label class="form-label required"><?php echo e(t('code')); ?></label>
                <input type="text" name="code" id="edit_code" class="form-input" required>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label required"><?php echo e(t('name')); ?> (Khmer)</label>
                    <input type="text" name="name_km" id="edit_name_km" class="form-input" required>
                </div>
                <div>
                    <label class="form-label required"><?php echo e(t('name')); ?> (English)</label>
                    <input type="text" name="name_en" id="edit_name_en" class="form-input" required>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label"><?php echo e(t('manager') ?? 'Manager'); ?></label>
                    <select name="manager_id" id="edit_manager_id" class="form-input">
                        <option value="">-- <?php echo e(t('select')); ?> --</option>
                        <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m->id); ?>"><?php echo e($m->name); ?></option>
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
                <button type="button" onclick="document.getElementById('edit_modal').classList.add('hidden')" class="btn-secondary"><?php echo e(t('cancel')); ?></button>
                <button type="submit" class="btn-primary"><?php echo e(t('save')); ?></button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function editDepartment(d) {
    document.getElementById('edit_form').action = `/master/departments/${d.id}`;
    document.getElementById('edit_code').value = d.code || '';
    document.getElementById('edit_name_km').value = d.name_km || d.name_en || '';
    document.getElementById('edit_name_en').value = d.name_en || d.name_km || '';
    document.getElementById('edit_manager_id').value = d.manager_id || '';
    document.getElementById('edit_description').value = d.description || '';
    document.getElementById('edit_status').value = (d.status || 'ACTIVE').toUpperCase();
    document.getElementById('edit_modal').classList.remove('hidden');
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/master/departments/index.blade.php ENDPATH**/ ?>