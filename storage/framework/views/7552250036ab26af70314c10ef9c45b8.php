<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'id' => null, 'required' => false, 'label' => null, 'options' => [], 'selected' => null, 'wrapperClass' => 'mb-3', 'multiple' => false]));

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

foreach (array_filter((['name', 'id' => null, 'required' => false, 'label' => null, 'options' => [], 'selected' => null, 'wrapperClass' => 'mb-3', 'multiple' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
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
<div class="relative <?php echo e($wrapperClass); ?>"
     x-data="{
        open: false,
        multiple: <?php echo e($multiple ? 'true' : 'false'); ?>,
        value: <?php echo e($multiple ? '[]' : 'null'); ?>,
        text: 'Select...',
        options: [],
        syncing: false,
        init() {
            let selectEl = this.$refs.select;
            this.updateOptions(selectEl);
            
            const observer = new MutationObserver(() => this.updateOptions(selectEl));
            observer.observe(selectEl, { childList: true, subtree: true });
            
            selectEl.addEventListener('change', (e) => {
                if (!this.syncing) {
                    if (this.multiple) {
                        this.value = Array.from(selectEl.selectedOptions).map(o => o.value);
                    } else {
                        this.value = selectEl.value;
                    }
                    this.updateText();
                }
            });
        },
        updateOptions(selectEl) {
            this.options = Array.from(selectEl.options).map(opt => ({
                value: opt.value,
                text: opt.text,
                selected: opt.selected,
                disabled: opt.disabled
            }));
            
            if (this.multiple) {
                this.value = Array.from(selectEl.selectedOptions).map(o => o.value);
            } else {
                this.value = selectEl.value;
            }
            
            this.updateText();
        },
        updateText() {
            if (this.multiple) {
                if (!Array.isArray(this.value)) this.value = [];
                let selectedTexts = this.options.filter(opt => this.value.includes(opt.value)).map(opt => opt.text);
                this.text = selectedTexts.length > 0 ? selectedTexts.join(', ') : '<?php echo e(t('select') ?? 'Select options...'); ?>';
            } else {
                let selectedOpt = this.options.find(opt => opt.value == this.value);
                if (selectedOpt) {
                    this.text = selectedOpt.text;
                } else if (this.options.length > 0) {
                    this.text = this.options[0].text;
                }
            }
        },
        selectOption(val) {
            let opt = this.options.find(o => o.value == val);
            if(opt && opt.disabled) return;
            
            this.syncing = true;
            if (this.multiple) {
                if (!Array.isArray(this.value)) this.value = [];
                let idx = this.value.indexOf(val);
                if (idx > -1) {
                    this.value.splice(idx, 1);
                } else {
                    this.value.push(val);
                }
                
                Array.from(this.$refs.select.options).forEach(o => {
                    o.selected = this.value.includes(o.value);
                });
            } else {
                this.value = val;
                this.$refs.select.value = val;
                this.open = false;
            }
            this.$refs.select.dispatchEvent(new Event('change', { bubbles: true }));
            this.updateText();
            this.$nextTick(() => { this.syncing = false; });
        }
     }"
     @click.outside="open = false"
>
    <!-- Hidden native select for form submission and options parsing -->
    <select x-ref="select" name="<?php echo e($name); ?><?php echo e($multiple ? '[]' : ''); ?>" id="<?php echo e($id); ?>" <?php echo e($required ? 'required' : ''); ?> <?php echo e($multiple ? 'multiple' : ''); ?> class="hidden">
        <?php if(empty($options) && $slot->isNotEmpty()): ?>
            <?php echo e($slot); ?>

        <?php else: ?>
            <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value); ?>" <?php echo e(old($name, $selected) == $value ? 'selected' : ''); ?>><?php echo e($text); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </select>

    <!-- Custom HeroUI-style Trigger -->
    <div @click="open = !open" 
         class="relative flex flex-col justify-center px-3 <?php echo e($label ? 'py-1' : 'py-2'); ?> bg-white hover:bg-slate-50 rounded-xl transition-all cursor-pointer border border-slate-200"
         :class="open ? 'bg-white ring-2 ring-slate-400 border-slate-400' : ''">
        
        <?php if($label): ?>
            <label class="block text-[10px] font-bold text-slate-500 cursor-pointer <?php echo e($required ? 'after:content-[\'*\'] after:text-rose-500 after:ml-0.5' : ''); ?>"><?php echo e($label); ?></label>
        <?php endif; ?>
        
        <div class="flex items-center justify-between <?php echo e($label ? 'mt-0.5' : ''); ?>">
            <span class="text-sm font-medium text-slate-800 truncate" x-text="text"></span>
            <div class="text-slate-400 flex items-center justify-center transition-transform" :class="open ? 'rotate-180' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Dropdown Popover -->
    <div x-show="open" 
         x-transition.opacity.duration.200ms
         style="display: none;"
         class="absolute z-50 w-full mt-1 bg-white rounded-xl border border-slate-200 py-1 max-h-60 overflow-y-auto custom-scrollbar left-0">
        <template x-for="opt in options" :key="opt.value">
            <div @click="selectOption(opt.value)"
                 class="px-3 py-2 text-sm transition-colors flex items-center justify-between"
                 :class="[
                    (multiple ? value.includes(opt.value) : value == opt.value) ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-100 cursor-pointer',
                    opt.disabled ? 'opacity-50 cursor-not-allowed bg-slate-50' : ''
                 ]">
                <span x-text="opt.text"></span>
                <svg x-show="multiple ? value.includes(opt.value) : value == opt.value" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-emerald-600">
                    <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z" clip-rule="evenodd" />
                </svg>
            </div>
        </template>
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
<?php /**PATH D:\invertory_managament_system\resources\views/components/select.blade.php ENDPATH**/ ?>