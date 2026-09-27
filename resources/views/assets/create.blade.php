@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'ចុះបញ្ជីទ្រព្យសម្បត្តិរូបវន្ត' : 'Register Physical Asset')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'ការគ្រប់គ្រងទ្រព្យសម្បត្តិ' : 'Asset Management' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ app()->getLocale() == 'km' ? 'ចុះបញ្ជីទ្រព្យសម្បត្តិរូបវន្ត' : 'Register Physical Asset' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() == 'km' ? 'កត់ត្រា និងគ្រប់គ្រងទ្រព្យសម្បត្តិអចលនទ្រព្យ/សម្ភារៈប្រើប្រាស់ លេខស៊េរី ការធានា និងស្ថានភាពបច្ចុប្បន្ន' : 'Record a tracked individual fixed asset with serial, warranty, condition, and location' }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('assets.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ក្រោយ' : 'Back to Assets' }}
            </a>
        </div>
    </div>

    <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data" class="glass-card p-6 md:p-8 space-y-8">
        @csrf

        <!-- Section 1: Asset Identity -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-qr-code"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'អត្តសញ្ញាណទ្រព្យសម្បត្តិ' : 'Asset Identity' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'កូដទ្រព្យសម្បត្តិ ឈ្មោះ លេខស៊េរី ម៉ាក និងម៉ូដែល' : 'Unique asset code, title, serial number, brand and model' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កូដទ្រព្យសម្បត្តិ' : 'Asset Code / Tag' }}</label>
                    <input type="text" name="asset_code" value="{{ old('asset_code', $nextCode) }}" required
                           class="form-input font-mono uppercase font-bold text-emerald-700">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ឈ្មោះទ្រព្យសម្បត្តិ' : 'Asset Name' }}</label>
                    <input type="text" name="asset_name" value="{{ old('asset_name') }}" required
                           placeholder="e.g. MacBook Pro 16 M2 - Space Gray" class="form-input font-medium">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ភ្ជាប់ជាមួយកាតាឡុកទំនិញ' : 'Link to Catalog Item' }}</label>
                    <select name="item_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'ទ្រព្យសម្បត្តិដាច់ដោយឡែក (Independent)' : 'Independent Asset' }}</option>
                        @foreach($items as $it)
                            <option value="{{ $it->id }}">{{ $it->code }} - {{ $it->name_en }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'លេខស៊េរី (Serial #)' : 'Serial Number' }}</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                           placeholder="e.g. C02G789XMD6R" class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'បាកូដ (Barcode)' : 'Barcode' }}</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}"
                           placeholder="{{ app()->getLocale() == 'km' ? 'បង្កើតស្វ័យប្រវត្តិកាលបើទុកទំនេរ' : 'Auto-generated if empty' }}" class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ប្រភេទ / ប្រភេទក្រុម' : 'Category' }}</label>
                    <select name="category_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសប្រភេទ --' : 'Select Category' }}</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name_en }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ម៉ាកផលិតផល' : 'Brand' }}</label>
                    <select name="brand_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសម៉ាក --' : 'Select Brand' }}</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ម៉ូដែល / លក្ខណៈបច្ចេកទេស' : 'Model / Specs' }}</label>
                    <input type="text" name="model" value="{{ old('model') }}" placeholder="e.g. A2485" class="form-input">
                </div>
            </div>
        </div>

        <!-- Section 2: Procurement & Warranty -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានលទ្ធកម្ម ការធានា និងស្ថានភាព' : 'Procurement, Warranty & Condition' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'តម្លៃទិញ អ្នកផ្គត់ផ្គង់ ថ្ងៃទិញ និងរយៈពេលធានា' : 'Purchase cost, vendor, condition, warranty dates and storage warehouse' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'អ្នកផ្គត់ផ្គង់' : 'Supplier' }}</label>
                    <select name="supplier_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសអ្នកផ្គត់ផ្គង់ --' : 'Select Supplier' }}</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទបញ្ជាទិញ' : 'Purchase Date' }}</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'តម្លៃទិញ ($)' : 'Purchase Cost ($)' }}</label>
                    <input type="number" step="0.01" min="0" name="purchase_cost" value="{{ old('purchase_cost', '0.00') }}" required
                           class="form-input font-mono font-bold text-slate-800">
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ស្ថានភាពរូបវន្តបច្ចុប្បន្ន' : 'Current Condition' }}</label>
                    <select name="condition" required class="form-input font-semibold">
                        <option value="NEW" {{ old('condition') === 'NEW' ? 'selected' : '' }}>✨ {{ app()->getLocale() == 'km' ? 'ថ្មី (Brand New)' : 'New (Brand New)' }}</option>
                        <option value="GOOD" {{ old('condition', 'GOOD') === 'GOOD' ? 'selected' : '' }}>✅ {{ app()->getLocale() == 'km' ? 'ល្អ (Operating well)' : 'Good (Operating well)' }}</option>
                        <option value="FAIR" {{ old('condition') === 'FAIR' ? 'selected' : '' }}>⚠️ {{ app()->getLocale() == 'km' ? 'មធ្យម (Usable with wear)' : 'Fair (Usable with wear)' }}</option>
                        <option value="DAMAGED" {{ old('condition') === 'DAMAGED' ? 'selected' : '' }}>❌ {{ app()->getLocale() == 'km' ? 'ខូចខាត (Needs repair)' : 'Damaged (Needs repair)' }}</option>
                        <option value="CRITICAL" {{ old('condition') === 'CRITICAL' ? 'selected' : '' }}>⛔ {{ app()->getLocale() == 'km' ? 'ខូចទាំងស្រុង (Broken)' : 'Critical (Broken)' }}</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទចាប់ផ្ដើមធានា' : 'Warranty Start' }}</label>
                    <input type="date" name="warranty_start" value="{{ old('warranty_start') }}" class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទផុតកំណត់ធានា' : 'Warranty Expiry' }}</label>
                    <input type="date" name="warranty_expiry" value="{{ old('warranty_expiry') }}" class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ឃ្លាំងផ្ទុកដំបូង' : 'Initial Warehouse' }}</label>
                    <select name="warehouse_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'មិនទាន់បញ្ជាក់' : 'None' }}</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ទីតាំងផ្ទុកដំបូង' : 'Initial Location' }}</label>
                    <select name="location_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'កន្លែងផ្ទុកចម្បង (Main)' : 'Main' }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->code }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 3: Photo & Notes -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-card-image"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'រូបថត និងកំណត់សម្គាល់' : 'Photo & Description' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'រូបភាពទ្រព្យសម្បត្តិ ឯកសារភ្ជាប់ ឬចំណាំអំពីការប្រើប្រាស់' : 'Upload photo and optional notes or accessories' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'រូបថតទ្រព្យសម្បត្តិ' : 'Asset Photo' }}</label>
                    <div class="glass-card p-4 border border-dashed border-slate-300 rounded-2xl text-center hover:border-emerald-400 transition-colors">
                        <i class="bi bi-cloud-arrow-up text-3xl text-slate-400 block mb-2"></i>
                        <input type="file" name="image" accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-2">PNG, JPG up to 5MB</p>
                    </div>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កំណត់សម្គាល់បន្ថែម' : 'Notes / Description' }}</label>
                    <textarea name="notes" rows="4" placeholder="{{ app()->getLocale() == 'km' ? 'គ្រឿងបន្លាស់បន្ថែម លក្ខខណ្ឌកុងត្រា ព័ត៌មានអ្នកធានា...' : 'Asset accessories, warranty provider terms, serial details...' }}"
                              class="form-input">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="pt-5 border-t border-slate-200/70 flex items-center justify-end space-x-3">
            <a href="{{ route('assets.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary !bg-emerald-600 hover:!bg-emerald-700 shadow-md shadow-emerald-500/20">
                <i class="bi bi-check2-circle mr-1.5 text-sm"></i>
                {{ app()->getLocale() == 'km' ? 'ចុះបញ្ជីទ្រព្យសម្បត្តិ' : 'Register Asset' }}
            </button>
        </div>
    </form>
</div>
@endsection
