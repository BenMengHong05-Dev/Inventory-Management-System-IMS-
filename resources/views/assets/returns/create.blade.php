@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'ប្រគល់ទ្រព្យសម្បត្តិត្រឡប់មកវិញ - IMS' : 'Process Asset Return - IMS')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-blue-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'ការប្រគល់ទ្រព្យត្រឡប់' : 'Asset Check-In' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ app()->getLocale() == 'km' ? 'កត់ត្រាការប្រគល់ទ្រព្យសម្បត្តិត្រឡប់មកវិញ' : 'Process Asset Return' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() == 'km' ? 'ទទួលទ្រព្យសម្បត្តិរូបវន្តត្រឡប់មកស្តុកវិញពីបុគ្គលិកប្រើប្រាស់' : 'Record physical return of an assigned asset from an employee' }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('assets.returns.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ក្រោយ' : 'Back to Returns' }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('assets.returns.store') }}" class="glass-card p-6 md:p-8 space-y-6">
        @csrf

        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-arrow-down-left-square"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានលម្អិតនៃការប្រគល់ត្រឡប់' : 'Return Details' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទ្រព្យសម្បត្តិសកម្ម កាលបរិច្ឆេទ ស្ថានភាពរូបវន្ត និងទីតាំងស្តុកទុក' : 'Select active assigned asset, return date, physical condition, and warehouse location' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទ្រព្យសម្បត្តិដែលត្រូវប្រគល់ត្រឡប់' : 'Select Assigned Asset' }}</label>
                    <select name="asset_id" class="form-input font-medium" required>
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសទ្រព្យសម្បត្តិដែលកំពុងប្រើប្រាស់ --' : '-- Choose Active Assigned Asset --' }}</option>
                        @foreach($assignedAssets as $assign)
                            <option value="{{ $assign->id }}" {{ (old('asset_id', $assetId ?? '') == $assign->id) ? 'selected' : '' }}>
                                [{{ $assign->asset_code }}] {{ $assign->asset_name }} ({{ app()->getLocale() == 'km' ? 'កាន់កាប់ដោយ' : 'Held by' }}: {{ $assign->employee_name ?: 'Staff' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទប្រគល់ត្រឡប់' : 'Return Date' }}</label>
                    <input type="date" name="return_date" value="{{ old('return_date', date('Y-m-d')) }}" class="form-input font-mono" required>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ស្ថានភាពរូបវន្តរបស់ទ្រព្យ' : 'Asset Condition' }}</label>
                    <select name="return_condition" class="form-input font-medium" required>
                        <option value="GOOD" {{ old('return_condition') == 'GOOD' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ល្អ (Good - អាចប្រើបន្តបាន)' : 'Good (Ready for Use)' }}</option>
                        <option value="NEW" {{ old('return_condition') == 'NEW' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ថ្មី (New / Like New)' : 'New / Like New' }}</option>
                        <option value="FAIR" {{ old('return_condition') == 'FAIR' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'មធ្យម (Fair - មានស្នាមប្រើប្រាស់)' : 'Fair (Acceptable)' }}</option>
                        <option value="DAMAGED" {{ old('return_condition') == 'DAMAGED' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ខូចខាត (Damaged - ត្រូវការជួសជុល)' : 'Damaged (Needs Repair)' }}</option>
                        <option value="CRITICAL" {{ old('return_condition') == 'CRITICAL' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ខូចទាំងស្រុង (Critical - មិនអាចជួសជុលបាន)' : 'Critical (Beyond Repair)' }}</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ប្រគល់ចូលទីតាំង / ឃ្លាំងស្តុកទុក' : 'Return to Location / Storage' }}</label>
                    <select name="location_id" class="form-input font-medium">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសទីតាំងស្តុកទុក --' : '-- Select Storage Location --' }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កំណត់សម្គាល់ការត្រួតពិនិត្យ' : 'Inspection Note' }}</label>
                    <textarea name="note" rows="3" class="form-input" 
                              placeholder="{{ app()->getLocale() == 'km' ? 'កត់សម្គាល់ស្ថានភាពរូបវន្ត ការឆ្កូត គ្រឿងបន្លាស់ ឆ្នាំងសាក ឬការជួសជុលចាំបាច់...' : 'Note physical condition, scratches, missing chargers, or repairs required...' }}">{{ old('note') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200/70">
            <a href="{{ route('assets.returns.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary !bg-blue-600 hover:!bg-blue-700 shadow-md shadow-blue-500/20">
                <i class="bi bi-check-lg mr-1.5 text-sm"></i>
                {{ app()->getLocale() == 'km' ? 'បញ្ចប់ការប្រគល់ត្រឡប់' : 'Complete Asset Return' }}
            </button>
        </div>
    </form>
</div>
@endsection
