@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'បង្កើតទំនិញថ្មី' : 'Create New Item')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'បង្កើតទំនិញថ្មី' : 'Create New Item' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'កំណត់ទំនិញថ្មី កាតាឡុក បាកូដ និងកម្រិតគ្រប់គ្រងស្តុក' : 'Define product specifications, SKUs, barcodes, and inventory thresholds' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('items.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ទៅកាតាឡុក' : 'Back to Catalog' }}
            </a>
        </div>
    </div>

    <!-- Form Card (Modern Clear Glass) -->
    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="glass-card p-6 md:p-8 space-y-8">
        @csrf

        <!-- Section 1: Basic Information -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-upc-scan"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានមូលដ្ឋាន' : 'Basic Identification' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'កំណត់លេខកូដទំនិញ បាកូដ និងប្រភេទ' : 'Unique codes, barcode scanner numbers, and categorization' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'លេខកូដទំនិញ / SKU' : 'Item Code / SKU' }}</label>
                    <input type="text" name="code" value="{{ old('code', $nextCode) }}" required
                           class="form-input font-mono uppercase" placeholder="ITM-00001">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'បាកូដ (Barcode / EAN)' : 'Barcode (EAN/UPC)' }}</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}"
                           class="form-input font-mono" placeholder="Auto-generated if empty">
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ប្រភេទមុខទំនិញ' : 'Item Type' }}</label>
                    <select name="item_type" required class="form-input">
                        <option value="CONSUMABLE" {{ old('item_type') === 'CONSUMABLE' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ប្រើប្រាស់អស់ (Consumable)' : 'Consumable' }}</option>
                        <option value="NON_CONSUMABLE" {{ old('item_type') === 'NON_CONSUMABLE' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'មិនប្រើប្រាស់អស់ (Non-Consumable)' : 'Non-Consumable' }}</option>
                        <option value="ASSET" {{ old('item_type') === 'ASSET' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ទ្រព្យសកម្ម (Fixed Asset)' : 'Fixed Asset' }}</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ឈ្មោះជាភាសាអង់គ្លេស' : 'English Name' }}</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" required
                           class="form-input" placeholder="e.g. Dell Latitude 5420 Laptop">
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ឈ្មោះជាភាសាខ្មែរ' : 'Khmer Name' }}</label>
                    <input type="text" name="name_km" value="{{ old('name_km') }}" required
                           class="form-input" placeholder="ឧទាហរណ៍៖ កុំព្យូទ័រយួរដៃ Dell Latitude">
                </div>
            </div>
        </div>

        <!-- Section 2: Classification & Pricing -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-tags"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ចំណាត់ថ្នាក់ និងតម្លៃ' : 'Classification & Valuation' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ប្រភេទទំនិញ ម៉ាក ឯកតា និងតម្លៃថ្លៃដើម' : 'Categories, brands, units of measure, and default purchase cost' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label">{{ t('messages.categories') }}</label>
                    <select name="category_id" class="form-input">
                        <option value="">{{ t('messages.all') }} {{ t('messages.categories') }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ localized_name($cat, 'name') }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ប្រភេទរង' : 'Sub-Category' }}</label>
                    <select name="sub_category_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសប្រភេទរង' : 'Select Sub-Category' }}</option>
                        @foreach($subCategories as $scat)
                            <option value="{{ $scat->id }}" {{ old('sub_category_id') == $scat->id ? 'selected' : '' }}>{{ localized_name($scat, 'name') }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ t('messages.brands') }}</label>
                    <select name="brand_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសម៉ាក' : 'Select Brand' }}</option>
                        @foreach($brands as $brd)
                            <option value="{{ $brd->id }}" {{ old('brand_id') == $brd->id ? 'selected' : '' }}>{{ $brd->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ t('messages.unit') }}</label>
                    <select name="unit_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសឯកតា' : 'Select Unit' }}</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name_en }} ({{ $u->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ម៉ូឌែល / លេខសម្គាល់' : 'Model / Part Number' }}</label>
                    <input type="text" name="model" value="{{ old('model') }}" placeholder="e.g. Latitude-5420-G2" class="form-input">
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'តម្លៃដើមឯកតា ($)' : 'Default Unit Cost ($)' }}</label>
                    <input type="number" step="0.01" min="0" name="default_unit_cost" value="{{ old('default_unit_cost', '0.00') }}" required class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label required">{{ t('messages.status') }}</label>
                    <select name="status" required class="form-input">
                        <option value="ACTIVE" {{ old('status', 'ACTIVE') === 'ACTIVE' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'សកម្ម (Active)' : 'Active' }}</option>
                        <option value="INACTIVE" {{ old('status') === 'INACTIVE' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'អសកម្ម (Inactive)' : 'Inactive' }}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 3: Stock Control Thresholds -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'កម្រិតគ្រប់គ្រងស្តុក' : 'Stock Control Thresholds' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'កំណត់កម្រិតសុវត្ថិភាព និងចំណុចប្រកាសអាសន្នជិតអស់ស្តុក' : 'Automated safety stock and low inventory alert thresholds' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-white/50 border border-slate-200/70 space-y-1.5">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ស្តុកអប្បបរមា (Min Stock)' : 'Minimum Stock Level' }}</label>
                    <input type="number" step="1" min="0" name="min_stock" value="{{ old('min_stock', '5') }}" required class="form-input font-mono">
                    <p class="text-[10px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ចំនួនទាបបំផុតដែលអាចទទួលយកបាន' : 'Lowest acceptable emergency reserve.' }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50/40 border border-amber-200/60 space-y-1.5">
                    <label class="form-label required text-amber-900">{{ app()->getLocale() == 'km' ? 'កម្រិតបញ្ជាទិញឡើងវិញ (Reorder)' : 'Reorder Point Level' }}</label>
                    <input type="number" step="1" min="0" name="reorder_level" value="{{ old('reorder_level', '10') }}" required class="form-input font-mono">
                    <p class="text-[10px] text-amber-700/80">{{ app()->getLocale() == 'km' ? 'កម្រិតដែលប្រព័ន្ធត្រូវជូនដំណឹងឱ្យបញ្ជាទិញបន្ថែម' : 'Triggers automated low-stock warnings.' }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-white/50 border border-slate-200/70 space-y-1.5">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ស្តុកអតិបរមា (Max Stock)' : 'Maximum Stock Level' }}</label>
                    <input type="number" step="1" min="0" name="max_stock" value="{{ old('max_stock', '100') }}" required class="form-input font-mono">
                    <p class="text-[10px] text-slate-400">{{ app()->getLocale() == 'km' ? 'សមត្ថភាពផ្ទុកខ្ពស់បំផុតក្នុងឃ្លាំង' : 'Maximum holding limit.' }}</p>
                </div>
            </div>
        </div>

        <!-- Section 4: Image & Description -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-image"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'រូបភាព និងការពិពណ៌នា' : 'Media & Description' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'រូបថតទំនិញជាក់ស្តែង និងព័ត៌មានលម្អិតបន្ថែម' : 'Product photo upload and technical specifications' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-white/50 border border-slate-200/70 flex flex-col justify-center">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'រូបភាពទំនិញ' : 'Product Photo' }}</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 transition cursor-pointer">
                    <p class="text-[10px] text-slate-400 mt-2">{{ app()->getLocale() == 'km' ? 'គាំទ្រទម្រង់ PNG, JPG ឬ WEBP (ទំហំអតិបរមា 2MB)' : 'Supports PNG, JPG, or WEBP (Max: 2MB)' }}</p>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ការពិពណ៌នាបន្ថែម' : 'Description / Notes' }}</label>
                    <textarea name="description" rows="3" placeholder="Technical specifications, packaging notes..." class="form-input">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Footer -->
        <div class="pt-5 border-t border-slate-200/70 flex items-center justify-end space-x-3">
            <a href="{{ route('items.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary">
                <i class="bi bi-check2-circle mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'រក្សាទុកទំនិញ' : 'Save Item' }}
            </button>
        </div>
    </form>
</div>
@endsection
