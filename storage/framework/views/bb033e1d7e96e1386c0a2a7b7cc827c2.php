<?php $__env->startSection('title', t('asset_maintenance') ?? 'Asset Maintenance'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900"><?php echo e(t('asset_maintenance') ?? 'Asset Maintenance & Repairs'); ?></h1>
            <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'តាមដានការថែទាំបង្ការ ការជួសជុល និងការក្រិតតាមខ្នាតឧបករណ៍' : 'Track preventive servicing, repairs, calibration, and vendor maintenance work'); ?></p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="<?php echo e(route('assets.index')); ?>" class="btn-secondary">
                <i class="bi bi-laptop mr-1.5"></i>
                <?php echo e(t('messages.assets')); ?>

            </a>
            <a href="<?php echo e(route('assets.maintenance.create')); ?>" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                Schedule Maintenance
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="<?php echo e(route('assets.maintenance.index')); ?>" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div></div>
        <div class="flex items-center gap-2.5 justify-end w-full sm:w-auto">
            <label class="text-xs font-medium text-slate-600">Status:</label>
            <div class="w-full sm:w-52">
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'status','onchange' => 'this.form.submit()','wrapperClass' => '!mb-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','onchange' => 'this.form.submit()','wrapperClass' => '!mb-0']); ?>
                    <option value="all" <?php echo e($status == 'all' ? 'selected' : ''); ?>>All Maintenance</option>
                    <option value="pending" <?php echo e($status == 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="in_progress" <?php echo e($status == 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                    <option value="completed" <?php echo e($status == 'completed' ? 'selected' : ''); ?>>Completed</option>
                    <option value="cancelled" <?php echo e($status == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
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
            <a href="<?php echo e(route('assets.maintenance.index')); ?>" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['pagination' => $maintenances]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($maintenances)]); ?>
         <?php $__env->slot('head', null, []); ?> 
            <th>Asset & Tag</th>
            <th>Title / Type</th>
            <th>Service Provider</th>
            <th>Start Date</th>
            <th>Expected End</th>
            <th>Cost</th>
            <th><?php echo e(t('messages.status')); ?></th>
            <th class="text-right"><?php echo e(t('messages.actions')); ?></th>
         <?php $__env->endSlot(); ?>

        <?php $__empty_1 = true; $__currentLoopData = $maintenances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td>
                    <a href="<?php echo e(route('assets.show', $m->asset_id)); ?>" class="font-bold text-slate-900 hover:text-sky-600 block transition">
                        <?php echo e($m->asset_tag); ?>

                    </a>
                    <span class="text-[11px] text-slate-400"><?php echo e($m->asset_name); ?></span>
                </td>
                <td>
                    <div class="font-semibold text-slate-800 text-xs"><?php echo e($m->title); ?></div>
                    <span class="text-[10px] text-slate-400 uppercase font-medium">
                        <?php echo e(str_replace('_', ' ', $m->maintenance_type)); ?>

                    </span>
                    <?php if($m->priority == 'urgent' || $m->priority == 'high'): ?>
                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-rose-500/10 text-rose-700 ml-1 uppercase"><?php echo e($m->priority); ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-xs text-slate-600"><?php echo e($m->supplier_name ?: 'Internal Technician'); ?></td>
                <td class="text-xs text-slate-500 whitespace-nowrap"><?php echo e($m->start_date ? date('M d, Y', strtotime($m->start_date)) : '-'); ?></td>
                <td class="text-xs text-slate-500 whitespace-nowrap"><?php echo e($m->expected_completion_date ? date('M d, Y', strtotime($m->expected_completion_date)) : '-'); ?></td>
                <td class="text-xs font-bold text-slate-900 font-mono">
                    <?php echo e($m->actual_cost ? format_currency($m->actual_cost) : ($m->cost ? format_currency($m->cost) . ' (est)' : '-')); ?>

                </td>
                <td>
                    <?php if($m->status == 'completed'): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Completed
                        </span>
                    <?php elseif($m->status == 'in_progress'): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-700 border border-sky-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                            In Progress
                        </span>
                    <?php elseif($m->status == 'pending'): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Pending
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            <?php echo e(ucfirst($m->status)); ?>

                        </span>
                    <?php endif; ?>
                </td>
                <td class="text-right whitespace-nowrap">
                    <a href="<?php echo e(route('assets.maintenance.show', $m->id)); ?>" class="btn-action btn-action-view" title="View & Update">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if (isset($component)) { $__componentOriginald1264eb3994796452a71a252a3bad0a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald1264eb3994796452a71a252a3bad0a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-empty','data' => ['colspan' => 8,'icon' => 'tools','title' => app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាជួសជុលទេ' : 'No Maintenance Records Found','description' => app()->getLocale() == 'km' ? 'កំណត់កាលវិភាគថែទាំ និងជួសជុលទ្រព្យសកម្ម' : 'Log equipment service schedules and vendor repairs.','actionUrl' => route('assets.maintenance.create'),'actionLabel' => t('messages.create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 8,'icon' => 'tools','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាជួសជុលទេ' : 'No Maintenance Records Found'),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'កំណត់កាលវិភាគថែទាំ និងជួសជុលទ្រព្យសកម្ម' : 'Log equipment service schedules and vendor repairs.'),'actionUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('assets.maintenance.create')),'actionLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(t('messages.create'))]); ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/assets/maintenance/index.blade.php ENDPATH**/ ?>