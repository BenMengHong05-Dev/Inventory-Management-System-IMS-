<?php $__env->startSection('title', t('messages.assets')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900"><?php echo e(t('messages.assets')); ?></h1>
            <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'តាមដានទ្រព្យសកម្មរូបវន្ត លេខស៊េរី ការធានា ការប្រគល់ និងស្ថានភាព' : 'Track individual physical assets, serial numbers, warranty, assignments, and conditions'); ?></p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="<?php echo e(route('asset-assignments.create')); ?>" class="btn-secondary">
                <i class="bi bi-person-check mr-1.5"></i>
                <?php echo e(t('messages.asset_assignments')); ?>

            </a>
            <a href="<?php echo e(route('assets.create')); ?>" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                <?php echo e(t('messages.create')); ?>

            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form action="<?php echo e(route('assets.index')); ?>" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['type' => 'text','name' => 'search','value' => ''.e($search).'','placeholder' => ''.e(t('messages.search')).'...','wrapperClass' => '!mb-0','icon' => 'bi bi-search']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','name' => 'search','value' => ''.e($search).'','placeholder' => ''.e(t('messages.search')).'...','wrapperClass' => '!mb-0','icon' => 'bi bi-search']); ?>
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
                    <option value=""><?php echo e(t('messages.all')); ?> <?php echo e(t('messages.status')); ?></option>
                    <option value="IN_STOCK" <?php echo e($status === 'IN_STOCK' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'ក្នុងស្តុក' : 'In Stock'); ?></option>
                    <option value="ASSIGNED" <?php echo e($status === 'ASSIGNED' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'បានប្រគល់ជូន' : 'Assigned'); ?></option>
                    <option value="IN_USE" <?php echo e($status === 'IN_USE' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'កំពុងប្រើប្រាស់' : 'In Use'); ?></option>
                    <option value="MAINTENANCE" <?php echo e($status === 'MAINTENANCE' ? 'selected' : ''); ?>><?php echo e(t('messages.maintenance')); ?></option>
                    <option value="DISPOSED" <?php echo e($status === 'DISPOSED' ? 'selected' : ''); ?>><?php echo e(t('messages.disposal')); ?></option>
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
            <div class="w-full sm:w-36">
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'condition','wrapperClass' => '!mb-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'condition','wrapperClass' => '!mb-0']); ?>
                    <option value=""><?php echo e(t('messages.all')); ?> <?php echo e(app()->getLocale() == 'km' ? 'ស្ថានភាពរូបវន្ត' : 'Conditions'); ?></option>
                    <option value="NEW" <?php echo e($condition === 'NEW' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'ថ្មី' : 'New'); ?></option>
                    <option value="GOOD" <?php echo e($condition === 'GOOD' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'ល្អ' : 'Good'); ?></option>
                    <option value="FAIR" <?php echo e($condition === 'FAIR' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'មធ្យម' : 'Fair'); ?></option>
                    <option value="DAMAGED" <?php echo e($condition === 'DAMAGED' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'ខូចខាត' : 'Damaged'); ?></option>
                    <option value="CRITICAL" <?php echo e($condition === 'CRITICAL' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'ធ្ងន់ធ្ងរ' : 'Critical'); ?></option>
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
            <div class="w-full sm:w-40">
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'category_id','wrapperClass' => '!mb-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'category_id','wrapperClass' => '!mb-0']); ?>
                    <option value=""><?php echo e(t('messages.all')); ?> <?php echo e(t('messages.categories')); ?></option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php echo e($categoryId == $c->id ? 'selected' : ''); ?>><?php echo e(localized_name($c, 'name')); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
            <a href="<?php echo e(route('assets.index')); ?>" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['pagination' => $assets]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($assets)]); ?>
         <?php $__env->slot('head', null, []); ?> 
            <th><?php echo e(t('messages.assets')); ?> & <?php echo e(t('messages.code')); ?></th>
            <th><?php echo e(app()->getLocale() == 'km' ? 'លេខស៊េរី' : 'Serial / Barcode'); ?></th>
            <th><?php echo e(t('messages.categories')); ?> & <?php echo e(t('messages.brands')); ?></th>
            <th><?php echo e(app()->getLocale() == 'km' ? 'ស្ថានភាព' : 'Condition'); ?></th>
            <th><?php echo e(app()->getLocale() == 'km' ? 'អ្នកកាន់កាប់បច្ចុប្បន្ន' : 'Current Holder'); ?></th>
            <th class="text-right"><?php echo e(t('messages.unit_cost')); ?></th>
            <th><?php echo e(t('messages.status')); ?></th>
            <th class="text-right"><?php echo e(t('messages.actions')); ?></th>
         <?php $__env->endSlot(); ?>

        <?php $__empty_1 = true; $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td>
                    <a href="<?php echo e(route('assets.show', $ast->id)); ?>" class="font-bold text-slate-900 hover:text-sky-600 block transition">
                        <?php echo e($ast->asset_name); ?>

                    </a>
                    <span class="text-[11px] text-slate-400 font-mono"><?php echo e($ast->asset_code); ?></span>
                </td>
                <td class="font-mono text-slate-700 text-xs">
                    <span><?php echo e($ast->serial_number ?? '-'); ?></span>
                </td>
                <td>
                    <span class="font-medium text-slate-800 block text-xs"><?php echo e(localized_name($ast, 'category_name')); ?></span>
                    <span class="text-[11px] text-slate-400"><?php echo e($ast->brand_name ?? '-'); ?> <?php echo e($ast->model ?? ''); ?></span>
                </td>
                <td>
                    <?php if($ast->condition === 'GOOD' || $ast->condition === 'NEW'): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <?php echo e($ast->condition); ?>

                        </span>
                    <?php elseif($ast->condition === 'FAIR'): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <?php echo e($ast->condition); ?>

                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                            <?php echo e($ast->condition); ?>

                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($ast->employee_name): ?>
                        <span class="font-bold text-slate-800 block text-xs"><?php echo e(localized_name($ast, 'employee_name')); ?></span>
                        <span class="text-[11px] text-slate-400"><?php echo e(localized_name($ast, 'department_name')); ?></span>
                    <?php else: ?>
                        <span class="text-slate-400 italic text-xs"><?php echo e(app()->getLocale() == 'km' ? 'ក្នុងឃ្លាំង' : 'In Warehouse'); ?> (<?php echo e($ast->warehouse_name ?? (app()->getLocale() == 'km' ? 'ឃ្លាំងកណ្តាល' : 'Depot')); ?>)</span>
                    <?php endif; ?>
                </td>
                <td class="text-right font-bold text-slate-900"><?php echo e(format_currency($ast->purchase_cost)); ?></td>
                <td>
                    <?php if($ast->status === 'IN_STOCK'): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <?php echo e($ast->status); ?>

                        </span>
                    <?php elseif($ast->status === 'ASSIGNED'): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-700 border border-sky-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                            <?php echo e($ast->status); ?>

                        </span>
                    <?php elseif($ast->status === 'MAINTENANCE'): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <?php echo e($ast->status); ?>

                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            <?php echo e($ast->status); ?>

                        </span>
                    <?php endif; ?>
                </td>
                <td class="text-right whitespace-nowrap space-x-1.5">
                    <a href="<?php echo e(route('assets.show', $ast->id)); ?>" class="btn-action btn-action-view" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="<?php echo e(route('assets.edit', $ast->id)); ?>" class="btn-action btn-action-edit" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if (isset($component)) { $__componentOriginald1264eb3994796452a71a252a3bad0a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald1264eb3994796452a71a252a3bad0a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-empty','data' => ['colspan' => 8,'icon' => 'laptop','title' => app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាទ្រព្យសកម្មនៅឡើយទេ' : 'No Assets Recorded Yet','description' => app()->getLocale() == 'km' ? 'ចាប់ផ្តើមកត់ត្រា និងគ្រប់គ្រងទ្រព្យសម្បត្តិរូបវន្តរបស់ស្ថាប័ន' : 'Start tracking and managing your organization\'s physical assets and equipment.','actionUrl' => route('assets.create'),'actionLabel' => t('messages.create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 8,'icon' => 'laptop','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាទ្រព្យសកម្មនៅឡើយទេ' : 'No Assets Recorded Yet'),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'ចាប់ផ្តើមកត់ត្រា និងគ្រប់គ្រងទ្រព្យសម្បត្តិរូបវន្តរបស់ស្ថាប័ន' : 'Start tracking and managing your organization\'s physical assets and equipment.'),'actionUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('assets.create')),'actionLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(t('messages.create'))]); ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/assets/index.blade.php ENDPATH**/ ?>