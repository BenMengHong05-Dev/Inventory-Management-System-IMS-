<?php
    $currentLocale = app()->getLocale();
?>

<div x-data="{ open: false }" class="relative inline-block text-left" @click.outside="open = false">
    <!-- Active Language Button -->
    <button type="button" 
            @click="open = !open" 
            class="flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full border border-slate-200/90 bg-white/95 hover:bg-slate-50 text-slate-700 transition shadow-xs hover:border-sky-300 focus:outline-none"
            aria-expanded="false"
            aria-haspopup="true">
        <?php if($currentLocale === 'km'): ?>
            <!-- Cambodia Flag Icon -->
            <span class="w-5 h-3.5 rounded-xs overflow-hidden inline-flex items-center shadow-xs border border-slate-300/60 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480" class="w-full h-full object-cover">
                    <rect width="640" height="480" fill="#032ea1"/>
                    <rect width="640" height="240" y="120" fill="#e00025"/>
                    <g fill="#ffffff">
                        <path d="M320 160l-16 28h32zm-40 44h80v12h-80zm-16 16h112v10h-112zm-20 14h152v12h-152z"/>
                        <path d="M256 220l-10 24h20zm128 0l-10 24h20zm-64-50l-12 24h24z"/>
                    </g>
                </svg>
            </span>
            <span class="font-medium text-slate-800">ភាសាខ្មែរ</span>
        <?php else: ?>
            <!-- English / UK Flag Icon -->
            <span class="w-5 h-3.5 rounded-xs overflow-hidden inline-flex items-center shadow-xs border border-slate-300/60 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30" class="w-full h-full object-cover">
                    <clipPath id="uk-flag-s"><path d="M0 0v30h60V0z"/></clipPath>
                    <clipPath id="uk-flag-t"><path d="M30 15h30v15zm0 0v15H0zm0 0H0V0zm0 0V0h30z"/></clipPath>
                    <g clip-path="url(#uk-flag-s)">
                        <path d="M0 0v30h60V0z" fill="#012169"/>
                        <path d="M0 0l60 30m0-30L0 30" stroke="#ffffff" stroke-width="6"/>
                        <path d="M0 0l60 30m0-30L0 30" clip-path="url(#uk-flag-t)" stroke="#C8102E" stroke-width="4"/>
                        <path d="M30 0v30M0 15h60" stroke="#ffffff" stroke-width="10"/>
                        <path d="M30 0v30M0 15h60" stroke="#C8102E" stroke-width="6"/>
                    </g>
                </svg>
            </span>
            <span class="font-medium text-slate-800">English</span>
        <?php endif; ?>
        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" 
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
         class="absolute right-0 mt-2 w-48 bg-white rounded-2xl border border-slate-200/90 py-1.5 text-xs z-50 shadow-xl shadow-slate-900/10">
        
        <div class="px-3.5 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
            <?php echo e($currentLocale === 'km' ? 'ជ្រើសរើសភាសា' : 'Select Language'); ?>

        </div>

        <!-- 1. Khmer Option -->
        <a href="<?php echo e(route('locale.switch', 'km')); ?>"
           class="flex items-center justify-between px-3.5 py-2 hover:bg-sky-50 transition-colors <?php echo e($currentLocale === 'km' ? 'bg-sky-50/80 text-sky-700 font-bold' : 'text-slate-700 hover:text-sky-700 font-medium'); ?>">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-3.5 rounded-xs overflow-hidden inline-flex items-center shadow-xs border border-slate-300/60 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480" class="w-full h-full object-cover">
                        <rect width="640" height="480" fill="#032ea1"/>
                        <rect width="640" height="240" y="120" fill="#e00025"/>
                        <g fill="#ffffff">
                            <path d="M320 160l-16 28h32zm-40 44h80v12h-80zm-16 16h112v10h-112zm-20 14h152v12h-152z"/>
                            <path d="M256 220l-10 24h20zm128 0l-10 24h20zm-64-50l-12 24h24z"/>
                        </g>
                    </svg>
                </span>
                <span>ភាសាខ្មែរ (Khmer)</span>
            </div>
            <?php if($currentLocale === 'km'): ?>
                <i data-lucide="check" class="w-4 h-4 text-sky-600 flex-shrink-0"></i>
            <?php endif; ?>
        </a>

        <!-- 2. English Option -->
        <a href="<?php echo e(route('locale.switch', 'en')); ?>"
           class="flex items-center justify-between px-3.5 py-2 hover:bg-sky-50 transition-colors <?php echo e($currentLocale === 'en' ? 'bg-sky-50/80 text-sky-700 font-bold' : 'text-slate-700 hover:text-sky-700 font-medium'); ?>">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-3.5 rounded-xs overflow-hidden inline-flex items-center shadow-xs border border-slate-300/60 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30" class="w-full h-full object-cover">
                        <clipPath id="uk-flag-s2"><path d="M0 0v30h60V0z"/></clipPath>
                        <clipPath id="uk-flag-t2"><path d="M30 15h30v15zm0 0v15H0zm0 0H0V0zm0 0V0h30z"/></clipPath>
                        <g clip-path="url(#uk-flag-s2)">
                            <path d="M0 0v30h60V0z" fill="#012169"/>
                            <path d="M0 0l60 30m0-30L0 30" stroke="#ffffff" stroke-width="6"/>
                            <path d="M0 0l60 30m0-30L0 30" clip-path="url(#uk-flag-t2)" stroke="#C8102E" stroke-width="4"/>
                            <path d="M30 0v30M0 15h60" stroke="#ffffff" stroke-width="10"/>
                            <path d="M30 0v30M0 15h60" stroke="#C8102E" stroke-width="6"/>
                        </g>
                    </svg>
                </span>
                <span>English</span>
            </div>
            <?php if($currentLocale === 'en'): ?>
                <i data-lucide="check" class="w-4 h-4 text-sky-600 flex-shrink-0"></i>
            <?php endif; ?>
        </a>
    </div>
</div>
<?php /**PATH D:\invertory_managament_system\resources\views/components/language-switcher.blade.php ENDPATH**/ ?>