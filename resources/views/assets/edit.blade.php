@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'កែប្រែទ្រព្យសម្បត្តិ - ' . $asset->asset_name : 'Edit Asset - ' . $asset->asset_name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'កែប្រែព័ត៌មានទ្រព្យសម្បត្តិ' : 'Update Physical Asset' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ $asset->asset_name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                <span class="font-mono font-bold text-slate-700 mr-2">{{ $asset->asset_code }}</span>
                {{ app()->getLocale() == 'km' ? 'កែសម្រួលស្ថានភាពបច្ចុប្បន្ន លក្ខខណ្ឌ ការធានា ទីតាំង និងព័ត៌មានលម្អិត' : 'Update asset condition, warranty, location, and metadata' }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('assets.show', $asset->id) }}" class="btn-secondary">
                <i class="bi bi-eye mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'មើលលម្អិត' : 'View Details' }}
            </a>
            <a href="{{ route('assets.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ក្រោយ' : 'Back' }}
            </a>
        </div>
    </div>

    <form action="{{ route('assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data" class="glass-card p-6 md:p-8 space-y-8">
        @csrf
        @method('PUT')

        <!-- Section 1: Asset Identity -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-sm">
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
                    <input type="text" name="asset_code" value="{{ old('asset_code', $asset->asset_code) }}" required
                           class="form-input font-mono uppercase font-bold text-amber-700">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ឈ្មោះទ្រព្យសម្បត្តិ' : 'Asset Name' }}</label>
                    <input type="text" name="asset_name" value="{{ old('asset_name', $asset->asset_name) }}" required
                           placeholder="e.g. MacBook Pro 16 M2 - Space Gray" class="form-input font-medium">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'លេខស៊េរី (Serial #)' : 'Serial Number' }}</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}"
                           placeholder="e.g. C02G789XMD6R" class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'បាកូដ (Barcode)' : 'Barcode' }}</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $asset->barcode) }}"
                           class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ប្រភេទ' : 'Category' }}</label>
                    <select name="category_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសប្រភេទ --' : 'Select Category' }}</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ old('category_id', $asset->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ម៉ាក' : 'Brand' }}</label>
                    <select name="brand_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសម៉ាក --' : 'Select Brand' }}</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ old('brand_id', $asset->brand_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ម៉ូដែល / លក្ខណៈបច្ចេកទេស' : 'Model / Specs' }}</label>
                    <input type="text" name="model" value="{{ old('model', $asset->model) }}" placeholder="e.g. A2485" class="form-input">
                </div>
            </div>
        </div>

        <!-- Section 2: Procurement, Status & Condition -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-activity"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ស្ថានភាព លក្ខខណ្ឌ និងការធានា' : 'Status, Condition & Warranty' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'គ្រប់គ្រងវដ្តជីវិត ស្ថានភាពរូបវន្ត និងឃ្លាំងផ្ទុក' : 'Manage lifecycle status, physical condition, warranty, and storage warehouse' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ស្ថានភាពវដ្តជីវិត' : 'Lifecycle Status' }}</label>
                    <select name="status" required class="form-input font-bold">
                        <option value="IN_STOCK" {{ old('status', $asset->status) === 'IN_STOCK' ? 'selected' : '' }}>📦 In Stock</option>
                        <option value="ASSIGNED" {{ old('status', $asset->status) === 'ASSIGNED' ? 'selected' : '' }}>👤 Assigned</option>
                        <option value="IN_USE" {{ old('status', $asset->status) === 'IN_USE' ? 'selected' : '' }}>⚡ In Use</option>
                        <option value="MAINTENANCE" {{ old('status', $asset->status) === 'MAINTENANCE' ? 'selected' : '' }}>🔧 Maintenance</option>
                        <option value="LOST" {{ old('status', $asset->status) === 'LOST' ? 'selected' : '' }}>❓ Lost</option>
                        <option value="DAMAGED" {{ old('status', $asset->status) === 'DAMAGED' ? 'selected' : '' }}>⚠️ Damaged</option>
                        <option value="DISPOSED" {{ old('status', $asset->status) === 'DISPOSED' ? 'selected' : '' }}>🗑️ Disposed</option>
                    </select>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ស្ថានភាពរូបវន្ត' : 'Condition' }}</label>
                    <select name="condition" required class="form-input font-semibold">
                        <option value="NEW" {{ old('condition', $asset->condition) === 'NEW' ? 'selected' : '' }}>✨ {{ app()->getLocale() == 'km' ? 'ថ្មី (Brand New)' : 'New' }}</option>
                        <option value="GOOD" {{ old('condition', $asset->condition) === 'GOOD' ? 'selected' : '' }}>✅ {{ app()->getLocale() == 'km' ? 'ល្អ (Good)' : 'Good' }}</option>
                        <option value="FAIR" {{ old('condition', $asset->condition) === 'FAIR' ? 'selected' : '' }}>⚠️ {{ app()->getLocale() == 'km' ? 'មធ្យម (Fair)' : 'Fair' }}</option>
                        <option value="DAMAGED" {{ old('condition', $asset->condition) === 'DAMAGED' ? 'selected' : '' }}>❌ {{ app()->getLocale() == 'km' ? 'ខូចខាត (Damaged)' : 'Damaged' }}</option>
                        <option value="CRITICAL" {{ old('condition', $asset->condition) === 'CRITICAL' ? 'selected' : '' }}>⛔ {{ app()->getLocale() == 'km' ? 'ខូចទាំងស្រុង (Critical)' : 'Critical' }}</option>
                    </select>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'តម្លៃទិញ ($)' : 'Purchase Cost ($)' }}</label>
                    <input type="number" step="0.01" min="0" name="purchase_cost" value="{{ old('purchase_cost', $asset->purchase_cost) }}" required
                           class="form-input font-mono font-bold text-slate-800">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'អ្នកផ្គត់ផ្គង់' : 'Supplier' }}</label>
                    <select name="supplier_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសអ្នកផ្គត់ផ្គង់ --' : 'Select Supplier' }}</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id', $asset->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទចាប់ផ្ដើមធានា' : 'Warranty Start' }}</label>
                    <input type="date" name="warranty_start" value="{{ old('warranty_start', $asset->warranty_start) }}" class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទផុតកំណត់ធានា' : 'Warranty Expiry' }}</label>
                    <input type="date" name="warranty_expiry" value="{{ old('warranty_expiry', $asset->warranty_expiry) }}" class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ឃ្លាំងផ្ទុក' : 'Warehouse' }}</label>
                    <select name="warehouse_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'មិនទាន់បញ្ជាក់' : 'None' }}</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ old('warehouse_id', $asset->warehouse_id) == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ទីតាំងផ្ទុក' : 'Location' }}</label>
                    <select name="location_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'កន្លែងផ្ទុកចម្បង (Main)' : 'Main' }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id', $asset->location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->code }}</option>
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
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'រូបថត និងកំណត់សម្គាល់' : 'Photo & Notes' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ផ្លាស់ប្តូររូបថត និងព័ត៌មានបន្ថែម' : 'Update asset image and additional remarks' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ផ្លាស់ប្តូររូបថត' : 'Change Photo' }}</label>
                    <div class="glass-card p-4 border border-dashed border-slate-300 rounded-2xl flex items-center gap-4 hover:border-amber-400 transition-colors">
                        @if($asset->image_path)
                            <img src="{{ asset('storage/' . $asset->image_path) }}" alt="{{ $asset->asset_name }}" class="w-16 h-16 rounded-xl object-cover border border-slate-200 shadow-sm shrink-0">
                        @else
                            <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 shrink-0">
                                <i class="bi bi-image text-2xl"></i>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <input type="file" name="image" accept="image/*"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer">
                            <p class="text-[10px] text-slate-400 mt-1">PNG, JPG up to 5MB</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កំណត់សម្គាល់' : 'Notes' }}</label>
                    <textarea name="notes" rows="3" class="form-input">{{ old('notes', $asset->notes) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="pt-5 border-t border-slate-200/70 flex items-center justify-end space-x-3">
            <a href="{{ route('assets.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary !bg-amber-600 hover:!bg-amber-700 shadow-md shadow-amber-500/20">
                <i class="bi bi-check2-circle mr-1.5 text-sm"></i>
                {{ app()->getLocale() == 'km' ? 'រក្សាទុកការកែប្រែ' : 'Update Asset' }}
            </button>
        </div>
    </form>
</div>
@endsection
