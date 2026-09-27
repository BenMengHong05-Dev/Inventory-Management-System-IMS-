<?php $__env->startSection('title', t('messages.items')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900"><?php echo e(t('messages.items')); ?></h1>
            <p class="text-xs text-gray-500"><?php echo e(app()->getLocale() == 'km' ? 'គ្រប់គ្រងទំនិញ ស្តុក បាកូដ និងតម្លៃឯកតា' : 'Manage product SKUs, stock levels, barcodes, and pricing'); ?></p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="<?php echo e(route('items.create')); ?>" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                <?php echo e(t('messages.create')); ?>

            </a>
        </div>
    </div>

    <!-- Search & Filter Bar (No white card background, no shadow, search on left, filters on right) -->
    <form action="<?php echo e(route('items.index')); ?>" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
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
            <div class="w-full sm:w-44">
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
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e($categoryId == $cat->id ? 'selected' : ''); ?>><?php echo e(localized_name($cat, 'name')); ?></option>
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
            <div class="w-full sm:w-44">
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'brand_id','wrapperClass' => '!mb-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'brand_id','wrapperClass' => '!mb-0']); ?>
                    <option value=""><?php echo e(t('messages.all')); ?> <?php echo e(t('messages.brands')); ?></option>
                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($brd->id); ?>" <?php echo e($brandId == $brd->id ? 'selected' : ''); ?>><?php echo e($brd->name); ?></option>
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
            <div class="w-full sm:w-40">
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'item_type','wrapperClass' => '!mb-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'item_type','wrapperClass' => '!mb-0']); ?>
                    <option value=""><?php echo e(t('messages.all')); ?> <?php echo e(t('messages.type')); ?></option>
                    <option value="CONSUMABLE" <?php echo e($itemType === 'CONSUMABLE' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'ប្រើប្រាស់អស់' : 'Consumable'); ?></option>
                    <option value="NON_CONSUMABLE" <?php echo e($itemType === 'NON_CONSUMABLE' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'មិនប្រើប្រាស់អស់' : 'Non-Consumable'); ?></option>
                    <option value="ASSET" <?php echo e($itemType === 'ASSET' ? 'selected' : ''); ?>><?php echo e(app()->getLocale() == 'km' ? 'ទ្រព្យសកម្ម' : 'Fixed Asset'); ?></option>
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
            <a href="<?php echo e(route('items.index')); ?>" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table Card (Modern Clear Glass) -->
    <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['pagination' => $items]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($items)]); ?>
         <?php $__env->slot('head', null, []); ?> 
            <th><?php echo e(t('messages.items')); ?> & SKU</th>
            <th><?php echo e(t('messages.categories')); ?> / <?php echo e(t('messages.brands')); ?></th>
            <th><?php echo e(t('messages.type')); ?></th>
            <th><?php echo e(t('messages.stock')); ?></th>
            <th><?php echo e(t('messages.unit_cost')); ?></th>
            <th><?php echo e(t('messages.total_value')); ?></th>
            <th><?php echo e(t('messages.status')); ?></th>
            <th class="text-right"><?php echo e(t('messages.actions')); ?></th>
         <?php $__env->endSlot(); ?>

        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-white/90 border border-slate-200/70 flex items-center justify-center flex-shrink-0 overflow-hidden text-slate-400">
                            <?php if($item->image): ?>
                                <img src="<?php echo e(asset('storage/' . $item->image)); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i data-lucide="package" class="w-5 h-5 text-slate-400"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <a href="<?php echo e(route('items.show', $item->id)); ?>" class="font-bold text-slate-900 hover:text-sky-600 transition block">
                                <?php echo e(localized_name($item, 'name')); ?>

                            </a>
                            <span class="text-[11px] text-slate-400 font-mono">Code: <?php echo e($item->code); ?> <?php if($item->barcode): ?> • Barcode: <?php echo e($item->barcode); ?> <?php endif; ?></span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="font-semibold text-slate-800 text-xs block"><?php echo e(localized_name($item, 'category_name')); ?></span>
                    <span class="text-[11px] text-slate-400"><?php echo e($item->brand_name ?? '-'); ?></span>
                </td>
                <td>
                    <?php if($item->item_type === 'CONSUMABLE'): ?>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200/60">
                            <?php echo e(app()->getLocale() == 'km' ? 'ប្រើប្រាស់អស់' : 'Consumable'); ?>

                        </span>
                    <?php elseif($item->item_type === 'ASSET'): ?>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200/60">
                            <?php echo e(app()->getLocale() == 'km' ? 'ទ្រព្យសកម្ម' : 'Asset'); ?>

                        </span>
                    <?php else: ?>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                            <?php echo e(app()->getLocale() == 'km' ? 'មិនប្រើប្រាស់អស់' : 'Non-Consumable'); ?>

                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="flex items-baseline gap-1">
                        <span class="font-bold text-sm <?php echo e($item->total_stock <= $item->reorder_level ? 'text-amber-600' : 'text-slate-900'); ?>">
                            <?php echo e(number_format($item->total_stock, 0)); ?>

                        </span>
                        <span class="text-[11px] text-slate-400">/ <?php echo e(localized_name($item, 'unit_name')); ?></span>
                    </div>
                    <?php if($item->total_stock <= $item->reorder_level): ?>
                        <span class="inline-flex items-center gap-1 text-[10px] text-amber-600 font-semibold mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <?php echo e(app()->getLocale() == 'km' ? 'ជិតអស់ពីស្តុក' : 'Low Stock'); ?>

                        </span>
                    <?php endif; ?>
                </td>
                <td class="font-medium text-slate-600"><?php echo e(format_currency($item->default_unit_cost)); ?></td>
                <td class="font-bold text-slate-900"><?php echo e(format_currency($item->total_value)); ?></td>
                <td>
                    <?php if(strtoupper($item->status) === 'ACTIVE'): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <?php echo e(app()->getLocale() == 'km' ? 'សកម្ម' : 'Active'); ?>

                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            <?php echo e(app()->getLocale() == 'km' ? 'អសកម្ម' : 'Inactive'); ?>

                        </span>
                    <?php endif; ?>
                </td>
                <td class="text-right space-x-1.5 whitespace-nowrap">
                    <a href="<?php echo e(route('items.show', $item->id)); ?>" class="btn-action btn-action-view" title="<?php echo e(t('messages.view') ?? 'View'); ?>">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="<?php echo e(route('items.edit', $item->id)); ?>" class="btn-action btn-action-edit" title="<?php echo e(t('messages.edit') ?? 'Edit'); ?>">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="<?php echo e(route('items.destroy', $item->id)); ?>" method="POST" class="inline-block" onsubmit="return confirm('<?php echo e(app()->getLocale() == 'km' ? 'តើអ្នកប្រាកដជាចង់លុបទំនិញនេះ?' : 'Are you sure you want to delete this item?'); ?>');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn-action btn-action-delete" title="<?php echo e(t('messages.delete') ?? 'Delete'); ?>">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if (isset($component)) { $__componentOriginald1264eb3994796452a71a252a3bad0a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald1264eb3994796452a71a252a3bad0a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-empty','data' => ['colspan' => 8,'icon' => 'package-x','title' => app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យទំនិញនៅឡើយទេ' : 'No Catalog Items Found','description' => app()->getLocale() == 'km' ? 'អ្នកអាចចាប់ផ្តើមបង្កើតទំនិញថ្មីដំបូងរបស់អ្នកដោយចុចប៊ូតុងខាងក្រោម' : 'Get started by creating your first product item in the inventory catalog.','actionUrl' => route('items.create'),'actionLabel' => t('messages.create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['colspan' => 8,'icon' => 'package-x','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យទំនិញនៅឡើយទេ' : 'No Catalog Items Found'),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app()->getLocale() == 'km' ? 'អ្នកអាចចាប់ផ្តើមបង្កើតទំនិញថ្មីដំបូងរបស់អ្នកដោយចុចប៊ូតុងខាងក្រោម' : 'Get started by creating your first product item in the inventory catalog.'),'actionUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('items.create')),'actionLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(t('messages.create'))]); ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/items/index.blade.php ENDPATH**/ ?>