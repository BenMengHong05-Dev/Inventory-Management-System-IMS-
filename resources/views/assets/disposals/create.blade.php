@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'កត់ត្រាការទម្លាក់ចោលទ្រព្យសម្បត្តិ - IMS' : 'Record Asset Disposal - IMS')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-rose-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'ការទម្លាក់ចោលទ្រព្យសម្បត្តិ' : 'Asset Decommissioning' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ app()->getLocale() == 'km' ? 'កត់ត្រាការទម្លាក់ចោល / លក់ចេញទ្រព្យសម្បត្តិ' : 'Record Asset Disposal' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() == 'km' ? 'កត់ត្រាការលុបឈ្មោះ លក់អេតចាយ បរិច្ចាគ ឬកាត់កងទ្រព្យសម្បត្តិចេញពីប្រព័ន្ធ' : 'Decommission, scrap, sell, donate, or write off an asset permanently' }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('assets.disposals.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ក្រោយ' : 'Back to Disposals' }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('assets.disposals.store') }}" class="glass-card p-6 md:p-8 space-y-6 border border-rose-200/50">
        @csrf

        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-trash3"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានលម្អិតអំពីការទម្លាក់ចោល' : 'Disposal Particulars' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទ្រព្យសម្បត្តិ មូលហេតុ វិធីសាស្ត្រទម្លាក់ចោល និងតម្លៃលក់ចេញ' : 'Select asset, decommissioning reason, disposal method, and salvage value' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'លេខកូដប័ណ្ណទម្លាក់ចោល' : 'Disposal Number' }}</label>
                    <input type="text" name="disposal_number" value="{{ old('disposal_number', $nextNumber ?? 'DSP-0001') }}" 
                           class="form-input font-mono uppercase font-bold text-slate-700 bg-slate-50/50" required>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទអនុវត្ត' : 'Disposal Date' }}</label>
                    <input type="date" name="disposal_date" value="{{ old('disposal_date', date('Y-m-d')) }}" class="form-input font-mono" required>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទ្រព្យសម្បត្តិដែលត្រូវទម្លាក់ចោល' : 'Select Asset to Dispose' }}</label>
                    <select name="asset_id" class="form-input font-medium" required>
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសទ្រព្យសម្បត្តិ --' : '-- Choose Asset --' }}</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ (old('asset_id', $assetId ?? '') == $asset->id) ? 'selected' : '' }}>
                                [{{ $asset->asset_code }}] {{ $asset->asset_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-rose-500 mt-1 flex items-center gap-1 font-medium">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ app()->getLocale() == 'km' ? 'ការព្រមាន៖ ការទម្លាក់ចោលទ្រព្យសម្បត្តិនឹងផ្លាស់ប្តូរស្ថានភាពជា DISPOSED និងដកចេញពីបញ្ជីទ្រព្យសកម្ម។' : 'Warning: Disposing an asset marks it as DISPOSED and permanently decommissions it.' }}
                    </p>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'មូលហេតុទម្លាក់ចោល' : 'Reason' }}</label>
                    <select name="reason" class="form-input font-medium" required>
                        <option value="DAMAGED" {{ old('reason') == 'DAMAGED' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ខូចខាត (Damaged)' : 'Damaged' }}</option>
                        <option value="OLD" {{ old('reason') == 'OLD' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ចាស់ / ហួសកាលកំណត់ (Old / Obsolete)' : 'Old / Obsolete' }}</option>
                        <option value="BEYOND_REPAIR" {{ old('reason') == 'BEYOND_REPAIR' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'មិនអាចជួសជុលបាន (Beyond Repair)' : 'Beyond Economic Repair' }}</option>
                        <option value="LOST" {{ old('reason') == 'LOST' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បាត់បង់ / លួច (Lost / Stolen)' : 'Lost / Stolen' }}</option>
                        <option value="REPLACED" {{ old('reason') == 'REPLACED' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ជំនួសថ្មី (Replaced)' : 'Replaced with New' }}</option>
                        <option value="SOLD" {{ old('reason') == 'SOLD' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'លក់ចេញ (Sold / Salvage)' : 'Sold (Salvage / Liquidated)' }}</option>
                        <option value="DONATED" {{ old('reason') == 'DONATED' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បរិច្ចាគ (Donated)' : 'Donated' }}</option>
                        <option value="OTHER" {{ old('reason') == 'OTHER' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ផ្សេងៗ (Other)' : 'Other' }}</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'វិធីសាស្ត្រទម្លាក់ចោល' : 'Disposal Method' }}</label>
                    <input type="text" name="disposal_method" value="{{ old('disposal_method') }}" class="form-input font-medium" 
                           placeholder="{{ app()->getLocale() == 'km' ? 'ឧ. លក់អេតចាយ, បោះចោល, បរិច្ចាគ...' : 'e.g. Scrapped, Recycled, Auctioned' }}">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'តម្លៃលក់បានមកវិញ ($)' : 'Disposal / Recovery Value ($)' }}</label>
                    <input type="number" step="0.01" name="disposal_value" value="{{ old('disposal_value', '0.00') }}" class="form-input font-mono" placeholder="0.00">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កំណត់សម្គាល់បន្ថែម' : 'Note / Justification' }}</label>
                    <textarea name="note" rows="3" class="form-input" 
                              placeholder="{{ app()->getLocale() == 'km' ? 'កំណត់សម្គាល់លម្អិតអំពីមូលហេតុ និងដំណើរការទម្លាក់ចោល...' : 'Detailed explanation and approval notes...' }}">{{ old('note') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200/70">
            <a href="{{ route('assets.disposals.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary !bg-rose-600 hover:!bg-rose-700 shadow-md shadow-rose-500/20">
                <i class="bi bi-trash3 mr-1.5 text-sm"></i>
                {{ app()->getLocale() == 'km' ? 'អនុម័ត & ទម្លាក់ចោលទ្រព្យសម្បត្តិ' : 'Authorize & Decommission Asset' }}
            </button>
        </div>
    </form>
</div>
@endsection
