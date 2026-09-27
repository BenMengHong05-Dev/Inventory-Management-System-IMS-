<?php $__env->startSection('title', app()->getLocale() == 'km' ? 'ធុងសំរាម & ការបោះបង់ទ្រព្យ' : 'Trash & Disposals Hub'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900"><?php echo e(app()->getLocale() == 'km' ? 'ធុងសំរាម & ការបោះបង់ទ្រព្យ' : 'Trash & Disposals Hub'); ?></h1>
            <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'គ្រប់គ្រងកំណត់ត្រាទំនិញដែលបានលុប និងការកម្ទេច/បោះបង់ទ្រព្យសម្បត្តិ' : 'Manage disposal records, salvaged values, and decommissioning history'); ?></p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="<?php echo e(route('audit-logs.index')); ?>" class="btn-secondary">
                <i class="bi bi-shield-check mr-1.5 text-amber-500"></i>
                Audit Logs
            </a>
            <a href="<?php echo e(route('assets.index')); ?>" class="btn-secondary">
                <i class="bi bi-laptop mr-1.5"></i>
                <?php echo e(t('messages.assets')); ?>

            </a>
            <a href="<?php echo e(route('assets.disposals.create')); ?>" class="btn-primary">
                <i class="bi bi-trash mr-1.5"></i>
                <?php echo e(app()->getLocale() == 'km' ? 'កត់ត្រាការបោះបង់' : 'New Disposal'); ?>

            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="<?php echo e(route('assets.disposals.index')); ?>" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-80">
            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['type' => 'text','name' => 'search','value' => ''.e(request('search', $search ?? '')).'','placeholder' => ''.e(app()->getLocale() == 'km' ? 'ស្វែងរកតាមលេខកូដបោះបង់, លេខកូដទ្រព្យ, ឈ្មោះ...' : 'Search disposal #, tag, name...').'','wrapperClass' => '!mb-0','icon' => 'bi bi-search']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','name' => 'search','value' => ''.e(request('search', $search ?? '')).'','placeholder' => ''.e(app()->getLocale() == 'km' ? 'ស្វែងរកតាមលេខកូដបោះបង់, លេខកូដទ្រព្យ, ឈ្មោះ...' : 'Search disposal #, tag, name...').'','wrapperClass' => '!mb-0','icon' => 'bi bi-search']); ?>
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
            <div class="w-full sm:w-56">
                <?php $currentReason = request('reason', $reason ?? ''); ?>
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'reason','wrapperClass' => '!mb-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'reason','wrapperClass' => '!mb-0']); ?>
                    <option value="" <?php echo e(empty($currentReason) ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'មូលហេតុទាំងអស់' : 'All Reasons'); ?></option>
                    <option value="DAMAGED" <?php echo e($currentReason == 'DAMAGED' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'ខូចខាត (Damaged)' : 'Damaged'); ?></option>
                    <option value="OLD" <?php echo e($currentReason == 'OLD' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'ចាស់ទ្រុឌទ្រោម (Old / Obsolete)' : 'Old / Obsolete'); ?></option>
                    <option value="BEYOND_REPAIR" <?php echo e($currentReason == 'BEYOND_REPAIR' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'មិនអាចជួសជុលបាន' : 'Beyond Repair'); ?></option>
                    <option value="LOST" <?php echo e($currentReason == 'LOST' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'បាត់បង់ (Lost / Stolen)' : 'Lost / Stolen'); ?></option>
                    <option value="SOLD" <?php echo e($currentReason == 'SOLD' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'លក់ចេញ (Sold)' : 'Sold'); ?></option>
                    <option value="DONATED" <?php echo e($currentReason == 'DONATED' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'បរិច្ចាគ (Donated)' : 'Donated'); ?></option>
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
                <?php echo e(t('messages.filter')); ?>

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
            <?php if(!empty(request('search')) || !empty(request('reason'))): ?>
                <a href="<?php echo e(route('assets.disposals.index')); ?>" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['pagination' => $disposals]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($disposals)]); ?>
         <?php $__env->slot('head', null, []); ?> 
            <th><?php echo e(app()->getLocale() == 'km' ? 'លេខបោះបង់' : 'Disposal #'); ?></th>
            <th><?php echo e(t('messages.assets')); ?></th>
            <th><?php echo e(app()->getLocale() == 'km' ? 'មូលហេតុ' : 'Reason'); ?></th>
            <th><?php echo e(t('messages.date')); ?></th>
            <th><?php echo e(app()->getLocale() == 'km' ? 'តម្លៃសង្គ្រោះ' : 'Salvage Value'); ?></th>
            <th><?php echo e(app()->getLocale() == 'km' ? 'អនុម័តដោយ' : 'Authorized By'); ?></th>
         <?php $__env->endSlot(); ?>

        <?php $__empty_1 = true; $__currentLoopData = $disposals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="font-bold text-xs text-slate-900 font-mono">
                    <?php echo e($d->disposal_number); ?>

                </td>
                <td>
                    <a href="<?php echo e(route('assets.show', $d->asset_id)); ?>" class="font-bold text-sky-600 hover:text-sky-700 text-xs block transition">
                        <?php echo e($d->asset_code ?? $d->asset_tag ?? ('Asset #' . $d->asset_id)); ?>

                    </a>
                    <span class="text-[11px] text-slate-500 font-medium"><?php echo e($d->asset_name ?? ''); ?></span>
                </td>
                <td>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                        <?php echo e($d->reason ?? $d->disposal_type ?? 'DISPOSED'); ?>

                    </span>
                </td>
                <td class="text-xs text-slate-600 whitespace-nowrap">
                    <?php echo e(date('d M Y', strtotime($d->disposal_date))); ?>

                </td>
                <td class="text-xs font-bold text-slate-900 font-mono">
                    <?php echo e($d->disposal_value ? format_currency($d->disposal_value) : '$0.00'); ?>

                </td>
                <td class="text-xs text-slate-600">
                    <?php echo e($d->approver_name ?? $d->authorized_by_name ?? '-'); ?>

                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if (isset($component)) { $__componentOriginald1264eb3994796452a71a252a3bad0a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald1264eb3994796452a71a252a3bad0a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-empty','data' => ['colspan' => 6,'icon' => 'trash2','title' => app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាបោះបង់ទេ' : 'No Disposal Records Found','description' => app()->getLocale() == 'km' ? 'ទ្រព្យសម្បត្តិដែលកម្ទេច ឬបោះបង់ចោលនឹងបង្ហាញនៅទីនេះ' : 'Decommissioned or scrapped assets will be archived here.','actionUrl' => route('assets.disposals.create'),'actionLabel' => app()->getLocale() == 'km' ? 'កត់ត្រាការបោះបង់' : 'New Disposal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 6,'icon' => 'trash2','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាបោះបង់ទេ' : 'No Disposal Records Found'),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'ទ្រព្យសម្បត្តិដែលកម្ទេច ឬបោះបង់ចោលនឹងបង្ហាញនៅទីនេះ' : 'Decommissioned or scrapped assets will be archived here.'),'actionUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('assets.disposals.create')),'actionLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'កត់ត្រាការបោះបង់' : 'New Disposal')]); ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/assets/disposals/index.blade.php ENDPATH**/ ?>