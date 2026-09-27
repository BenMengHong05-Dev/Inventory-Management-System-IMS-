<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type' => 'text', 'name', 'id' => null, 'value' => null, 'required' => false, 'label' => null, 'wrapperClass' => 'mb-3', 'icon' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['type' => 'text', 'name', 'id' => null, 'value' => null, 'required' => false, 'label' => null, 'wrapperClass' => 'mb-3', 'icon' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<?php
    $id = $id ?? $name;
?>
<div class="<?php echo e($wrapperClass); ?>">
    <div class="relative flex items-center px-3 <?php echo e($label ? 'py-1' : 'py-2'); ?> bg-white hover:bg-slate-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-slate-400 rounded-xl transition-all border border-slate-200 focus-within:border-slate-400">
        <?php if($icon): ?>
            <div class="text-slate-400 mr-2 flex-shrink-0 flex items-center justify-center">
                <i class="<?php echo e($icon); ?>"></i>
            </div>
        <?php endif; ?>
        <div class="flex-1 flex flex-col justify-center min-w-0 cursor-text">
            <?php if($label): ?>
                <label for="<?php echo e($id); ?>" class="block text-[10px] font-bold text-slate-500 cursor-text <?php echo e($required ? 'after:content-[\'*\'] after:text-rose-500 after:ml-0.5' : ''); ?>"><?php echo e($label); ?></label>
            <?php endif; ?>
            <input type="<?php echo e($type); ?>" name="<?php echo e($name); ?>" id="<?php echo e($id); ?>" value="<?php echo e(old($name, $value)); ?>" <?php echo e($required ? 'required' : ''); ?> <?php echo e($attributes->merge(['class' => 'w-full bg-transparent border-none p-0 focus:ring-0 text-sm font-medium text-slate-800 outline-none ' . ($label ? 'mt-0.5' : '')])); ?>>
        </div>
    </div>
    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="text-rose-500 text-[11px] mt-1"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH D:\invertory_managament_system\resources\views/components/input.blade.php ENDPATH**/ ?>