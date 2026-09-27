<?php $__env->startSection('title', t('audit_logs') . ' - IMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-slate-700 to-slate-900 text-white flex items-center justify-center shadow-lg shadow-slate-900/20">
                <i class="bi bi-clock-history text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800"><?php echo e(t('audit_logs')); ?></h1>
                <p class="text-xs sm:text-sm text-slate-500"><?php echo e(t('manage')); ?> <?php echo e(t('audit_logs')); ?> &amp; <?php echo e(t('system_security') ?? 'Security History'); ?></p>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('audit-logs.clear')); ?>" onsubmit="return confirm('<?php echo e(app()->getLocale() == 'km' ? 'តើអ្នកប្រាកដថាចង់លុបកំណត់ត្រាសកម្មភាពទាំងអស់ចេញពីប្រព័ន្ធមែនទេ?' : 'Are you sure you want to permanently delete all audit log records?'); ?>');">
            <?php echo csrf_field(); ?>
            <button type="submit" class="px-3.5 py-2 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl text-xs font-bold border border-rose-200 flex items-center gap-1.5 transition">
                <i class="bi bi-trash"></i> <?php echo e(app()->getLocale() == 'km' ? 'លុបកំណត់ត្រាទាំងអស់' : 'Clear All Logs'); ?>

            </button>
        </form>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
        <form method="GET" action="<?php echo e(route('admin.audit_logs.index')); ?>" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="<?php echo e(t('search')); ?>..." class="form-input pl-9 text-sm">
            </div>
            <div>
                <select name="action" class="form-input text-sm w-auto">
                    <option value=""><?php echo e(t('all')); ?> <?php echo e(t('actions')); ?></option>
                    <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($a->action); ?>" <?php echo e(($action ?? '') == $a->action ? 'selected' : ''); ?>><?php echo e(strtoupper($a->action)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <select name="module" class="form-input text-sm w-auto">
                    <option value=""><?php echo e(t('all')); ?> Modules</option>
                    <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m->module); ?>" <?php echo e(($module ?? '') == $m->module ? 'selected' : ''); ?>><?php echo e($m->module); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="btn-secondary text-sm">
                <i class="bi bi-funnel"></i> <?php echo e(t('filter')); ?>

            </button>
            <?php if(!empty($search) || !empty($action) || !empty($module)): ?>
                <a href="<?php echo e(route('admin.audit_logs.index')); ?>" class="text-xs text-rose-500 hover:underline">
                    <?php echo e(t('clear')); ?>

                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th><?php echo e(t('date')); ?></th>
                        <th><?php echo e(t('users')); ?></th>
                        <th><?php echo e(t('actions')); ?></th>
                        <th>Module</th>
                        <th>Record ID</th>
                        <th>IP Address</th>
                        <th class="text-right"><?php echo e(t('view_details')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-xs text-slate-500 whitespace-nowrap font-mono">
                            <?php echo e(date('Y-m-d H:i:s', strtotime($l->created_at))); ?>

                        </td>
                        <td>
                            <div class="font-bold text-slate-800 text-xs"><?php echo e($l->user_name ?: 'System'); ?></div>
                            <div class="text-[11px] text-slate-400 font-mono"><?php echo e($l->user_email ?: ''); ?></div>
                        </td>
                        <td>
                            <?php $act = strtolower($l->action); ?>
                            <?php if($act == 'create'): ?>
                                <span class="badge bg-emerald-100 text-emerald-800 text-[10px]">CREATE</span>
                            <?php elseif($act == 'update'): ?>
                                <span class="badge bg-blue-100 text-blue-800 text-[10px]">UPDATE</span>
                            <?php elseif($act == 'delete'): ?>
                                <span class="badge bg-rose-100 text-rose-800 text-[10px]">DELETE</span>
                            <?php elseif($act == 'approve'): ?>
                                <span class="badge bg-purple-100 text-purple-800 text-[10px]">APPROVE</span>
                            <?php else: ?>
                                <span class="badge bg-slate-100 text-slate-700 text-[10px]"><?php echo e(strtoupper($l->action)); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-xs font-semibold text-slate-700"><?php echo e($l->module); ?></td>
                        <td class="text-xs font-mono text-slate-600"><?php echo e($l->record_id ?: '-'); ?></td>
                        <td class="text-xs font-mono text-slate-500"><?php echo e($l->ip_address ?: '-'); ?></td>
                        <td class="text-right">
                            <a href="<?php echo e(route('admin.audit_logs.show', $l->id)); ?>" class="btn-secondary text-xs px-2.5 py-1">
                                <i class="bi bi-eye"></i> <?php echo e(t('view')); ?>

                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-400">
                            <i class="bi bi-clock-history text-3xl mb-2 block text-slate-300"></i>
                            <?php echo e(t('no_data_available')); ?>

                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if(method_exists($auditLogs, 'links')): ?>
        <div class="p-4 border-t border-slate-100">
            <?php echo e($auditLogs->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\invertory_managament_system\resources\views/admin/audit_logs/index.blade.php ENDPATH**/ ?>