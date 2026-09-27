<?php $__env->startSection('title', app()->getLocale() == 'km' ? 'កាលវិភាគថែទាំទ្រព្យសម្បត្តិ - IMS' : 'Schedule Asset Maintenance - IMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-600 font-mono">
                    <?php echo e(app()->getLocale() == 'km' ? 'ការថែទាំ និងជួសជុល' : 'Asset Maintenance'); ?>

                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                <?php echo e(app()->getLocale() == 'km' ? 'កត់ត្រាកាលវិភាគថែទាំទ្រព្យសម្បត្តិ' : 'Schedule Asset Maintenance'); ?>

            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                <?php echo e(app()->getLocale() == 'km' ? 'បង្កើតសំបុត្រជួសជុល កែលម្អ ឬត្រួតពិនិត្យគុណភាពទ្រព្យសម្បត្តិ' : 'Initiate repair, calibration, or servicing for an asset'); ?>

            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="<?php echo e(route('assets.maintenance.index')); ?>" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                <?php echo e(app()->getLocale() == 'km' ? 'ត្រឡប់ក្រោយ' : 'Back to Maintenance'); ?>

            </a>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('assets.maintenance.store')); ?>" class="glass-card p-6 md:p-8 space-y-6">
        <?php echo csrf_field(); ?>

        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-wrench-adjustable"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900"><?php echo e(app()->getLocale() == 'km' ? 'ព័ត៌មានលម្អិតអំពីការថែទាំ' : 'Maintenance Details'); ?></h2>
                    <p class="text-[11px] text-slate-400"><?php echo e(app()->getLocale() == 'km' ? 'ជ្រើសរើសទ្រព្យសម្បត្តិ បញ្ហាដែលបានរាយការណ៍ អ្នកបច្ចេកទេស និងក្រុមហ៊ុនផ្តល់សេវា' : 'Select asset, problem summary, technician, and service provider'); ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label required"><?php echo e(app()->getLocale() == 'km' ? 'លេខកូដសំបុត្រថែទាំ' : 'Maintenance Number'); ?></label>
                    <input type="text" name="maintenance_number" value="<?php echo e(old('maintenance_number', $nextNumber ?? 'MNT-0001')); ?>" 
                           class="form-input font-mono uppercase font-bold text-slate-700 bg-slate-50/50" required>
                </div>

                <div>
                    <label class="form-label required"><?php echo e(app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទស្នើសុំ' : 'Request Date'); ?></label>
                    <input type="date" name="request_date" value="<?php echo e(old('request_date', date('Y-m-d'))); ?>" class="form-input font-mono" required>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label required"><?php echo e(app()->getLocale() == 'km' ? 'ជ្រើសរើសទ្រព្យសម្បត្តិ' : 'Select Asset'); ?></label>
                    <select name="asset_id" class="form-input font-medium" required>
                        <option value=""><?php echo e(app()->getLocale() == 'km' ? '-- ជ្រើសរើសទ្រព្យសម្បត្តិដែលត្រូវការសេវាកម្ម --' : '-- Choose Asset Requiring Service --'); ?></option>
                        <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($asset->id); ?>" <?php echo e((old('asset_id', $assetId ?? '') == $asset->id) ? 'selected' : ''); ?>>
                                [<?php echo e($asset->asset_code); ?>] <?php echo e($asset->asset_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label required"><?php echo e(app()->getLocale() == 'km' ? 'បញ្ហា / អាការៈដែលបានជួបប្រទះ' : 'Problem / Issue Reported'); ?></label>
                    <input type="text" name="problem" value="<?php echo e(old('problem')); ?>" class="form-input font-medium" 
                           placeholder="<?php echo e(app()->getLocale() == 'km' ? 'ឧ. បើកមិនចេញ, ខូចអេក្រង់, ថ្មមិនកាន់ភ្លើង...' : 'e.g. System not powering on, cracked screen, battery degradation'); ?>" required>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label"><?php echo e(app()->getLocale() == 'km' ? 'ការពិពណ៌នាលម្អិតបន្ថែម' : 'Detailed Description'); ?></label>
                    <textarea name="description" rows="3" class="form-input" 
                              placeholder="<?php echo e(app()->getLocale() == 'km' ? 'រៀបរាប់អំពីអាការៈ ឬពិធីការជួសជុលដែលត្រូវធ្វើ...' : 'Describe symptoms, diagnosis, or scheduled maintenance protocol...'); ?>"><?php echo e(old('description')); ?></textarea>
                </div>

                <div>
                    <label class="form-label"><?php echo e(app()->getLocale() == 'km' ? 'អ្នកបច្ចេកទេសទទួលបន្ទុក' : 'Technician'); ?></label>
                    <input type="text" name="technician" value="<?php echo e(old('technician')); ?>" class="form-input font-medium" 
                           placeholder="<?php echo e(app()->getLocale() == 'km' ? 'ឈ្មោះជាង / អ្នកបច្ចេកទេស' : 'e.g. John Smith'); ?>">
                </div>

                <div>
                    <label class="form-label"><?php echo e(app()->getLocale() == 'km' ? 'ក្រុមហ៊ុន / មជ្ឈមណ្ឌលផ្តល់សេវា' : 'Service Provider'); ?></label>
                    <input type="text" name="service_provider" value="<?php echo e(old('service_provider')); ?>" class="form-input font-medium" 
                           placeholder="<?php echo e(app()->getLocale() == 'km' ? 'ឈ្មោះមជ្ឈមណ្ឌលសេវាកម្មខាងក្រៅ' : 'e.g. Official Service Center'); ?>">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 hover:bg-slate-100/70 transition">
                        <input type="checkbox" name="warranty_coverage" value="1" <?php echo e(old('warranty_coverage') ? 'checked' : ''); ?> class="rounded text-amber-600 focus:ring-amber-500">
                        <span class="text-xs font-bold text-slate-700">
                            <?php echo e(app()->getLocale() == 'km' ? 'ស្ថិតក្រោមការធានារ៉ាប់រង (Under Warranty Coverage)' : 'Covered Under Warranty'); ?>

                        </span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200/70">
            <a href="<?php echo e(route('assets.maintenance.index')); ?>" class="btn-secondary">
                <?php echo e(app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel'); ?>

            </a>
            <button type="submit" class="btn-primary !bg-amber-600 hover:!bg-amber-700 shadow-md shadow-amber-500/20">
                <i class="bi bi-wrench mr-1.5 text-sm"></i>
                <?php echo e(app()->getLocale() == 'km' ? 'បង្កើតសំបុត្រថែទាំ' : 'Create Maintenance Ticket'); ?>

            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/assets/maintenance/create.blade.php ENDPATH**/ ?>