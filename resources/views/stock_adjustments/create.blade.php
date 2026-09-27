@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'កែតម្រូវចំនួនស្តុក' : 'New Stock Adjustment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'កែតម្រូវចំនួនស្តុក' : 'New Stock Adjustment' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'កត់ត្រាការកើនឡើង ឬថយចុះចំនួនស្តុកជាក់ស្តែងដោយផ្អែកលើការត្រួតពិនិត្យ' : 'Record a verified stock count increase or decrease with discrepancy reasoning' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('stock-adjustments.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ទៅបញ្ជី' : 'Back to List' }}
            </a>
        </div>
    </div>

    <!-- Form Card (Modern Clear Glass) -->
    <form action="{{ route('stock-adjustments.store') }}" method="POST" class="glass-card p-6 md:p-8 space-y-8">
        @csrf

        <!-- Section 1: Adjustment Header -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-sliders"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មាននៃការកែតម្រូវ' : 'Adjustment Identification & Location' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'កំណត់លេខឯកសារ កាលបរិច្ឆេទ និងឃ្លាំង' : 'Reference code, date, and storage location' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'លេខកូដកែតម្រូវ #' : 'Adjustment #' }}</label>
                    <input type="text" name="adjustment_number" value="{{ old('adjustment_number', $nextNumber) }}" required
                           class="form-input font-mono uppercase" placeholder="ADJ-00001">
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទ' : 'Date' }}</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="form-input">
                </div>

                <div>
                    <label class="form-label required">{{ t('messages.warehouse') }}</label>
                    <select name="warehouse_id" required class="form-input">
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ (old('warehouse_id') ?? request('warehouse_id')) == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ទីតាំង' : 'Location' }}</label>
                    <select name="location_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'តំបន់ទូទៅ' : 'Main Storage' }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->code }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Item & Actual Count -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ទំនិញ និងចំនួនរាប់ជាក់ស្តែង' : 'Item Selection & Verified Count' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ប្រព័ន្ធនឹងគណនាភាពខុសគ្នាដោយស្វ័យប្រវត្តិធៀបនឹងស្តុកបច្ចុប្បន្ន' : 'Discrepancy difference will be calculated automatically against current stock' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ទំនិញដែលត្រូវកែតម្រូវ' : 'Item to Adjust' }}</label>
                    <select name="item_id" required class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទំនិញ...' : 'Select Item...' }}</option>
                        @foreach($items as $it)
                            <option value="{{ $it->id }}" {{ (old('item_id') ?? request('item_id')) == $it->id ? 'selected' : '' }}>
                                [{{ $it->code }}] {{ localized_name($it, 'name') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="p-4 rounded-2xl bg-white/50 border border-slate-200/70 space-y-1.5">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ចំនួនរាប់ជាក់ស្តែង' : 'Actual Counted Quantity' }}</label>
                    <input type="number" step="0.01" min="0" name="actual_quantity" value="{{ old('actual_quantity') }}" required placeholder="e.g. 45"
                           class="form-input font-bold font-mono text-sm">
                    <p class="text-[10px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ចំនួនជាក់ស្តែងដែលបានពិនិត្យឃើញក្នុងឃ្លាំង' : 'Physical count verified on the shelf.' }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-white/50 border border-slate-200/70 space-y-1.5">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'មូលហេតុនៃភាពមិនស៊ីគ្នា' : 'Reason for Discrepancy' }}</label>
                    <select name="reason" required class="form-input">
                        <option value="Damaged Goods">{{ app()->getLocale() == 'km' ? 'ទំនិញខូចខាត (Damaged Goods)' : 'Damaged Goods' }}</option>
                        <option value="Physical Count Correction">{{ app()->getLocale() == 'km' ? 'កែតម្រូវចំនួនរាប់ (Physical Count Correction)' : 'Physical Count Correction' }}</option>
                        <option value="Expired Products">{{ app()->getLocale() == 'km' ? 'ទំនិញផុតកំណត់ (Expired Products)' : 'Expired Products' }}</option>
                        <option value="Theft / Lost">{{ app()->getLocale() == 'km' ? 'បាត់បង់ (Theft / Lost)' : 'Theft / Lost' }}</option>
                        <option value="Supplier Mismatch">{{ app()->getLocale() == 'km' ? 'មិនស៊ីគ្នាពីអ្នកផ្គត់ផ្គង់ (Supplier Mismatch)' : 'Supplier Mismatch' }}</option>
                        <option value="Other">{{ app()->getLocale() == 'km' ? 'ផ្សេងៗ (Other)' : 'Other' }}</option>
                    </select>
                    <p class="text-[10px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ប្រភេទនៃការកែសម្រួលដើម្បីតាមដានសវនកម្ម' : 'Category for internal audit tracking.' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ការពន្យល់លម្អិត / កំណត់ចំណាំ' : 'Detailed Explanation / Notes' }}</label>
                    <textarea name="note" rows="2" placeholder="Describe the cause of variance and any investigation results..." class="form-input">{{ old('note') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Footer -->
        <div class="pt-5 border-t border-slate-200/70 flex items-center justify-end space-x-3">
            <a href="{{ route('stock-adjustments.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary">
                <i class="bi bi-check2-circle mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'អនុវត្តការកែតម្រូវ' : 'Apply Adjustment' }}
            </button>
        </div>
    </form>
</div>
@endsection
