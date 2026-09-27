@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'ចាប់ផ្តើមវគ្គរាប់ស្តុក' : 'Initiate Stock Take')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'ចាប់ផ្តើមវគ្គរាប់ស្តុកជាក់ស្តែង' : 'Initiate Physical Stock Take' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'ចាក់សោរូបថតស្តុកបច្ចុប្បន្នដើម្បីធ្វើការរាប់ជាក់ស្តែង និងគណនាភាពខុសគ្នា' : 'Freezes an inventory snapshot for physical counting and variance reconciliation' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('stock-takes.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់' : 'Back' }}
            </a>
        </div>
    </div>

    <!-- Form Card (Modern Clear Glass) -->
    <form action="{{ route('stock-takes.store') }}" method="POST" class="glass-card p-6 md:p-8 space-y-6">
        @csrf

        <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
            <div class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-600 flex items-center justify-center font-bold text-sm">
                <i class="bi bi-clipboard-check"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានលម្អិតនៃវគ្គសវនកម្ម' : 'Audit Session Scope & Location' }}</h2>
                <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសឃ្លាំង និងទីតាំងដែលត្រូវធ្វើសវនកម្ម' : 'Target facility, zone filters, and audit scope' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label required">{{ app()->getLocale() == 'km' ? 'លេខកូដសវនកម្ម #' : 'Stock Take Audit #' }}</label>
                <input type="text" name="stock_take_number" value="{{ old('stock_take_number', $nextNumber) }}" required
                       class="form-input font-mono uppercase" placeholder="STK-00001">
            </div>

            <div>
                <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទសវនកម្ម' : 'Audit Date' }}</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="form-input">
            </div>

            <div>
                <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ឃ្លាំងគោលដៅ' : 'Target Warehouse' }}</label>
                <select name="warehouse_id" required class="form-input">
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">{{ app()->getLocale() == 'km' ? 'ទីតាំង / តំបន់' : 'Location / Zone Filter' }}</label>
                <select name="location_id" class="form-input">
                    <option value="">{{ app()->getLocale() == 'km' ? 'គ្រប់ទីតាំងទាំងអស់ក្នុងឃ្លាំង' : 'All Locations in Warehouse' }}</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->code }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="form-label">{{ app()->getLocale() == 'km' ? 'គោលបំណង / វិសាលភាពសវនកម្ម' : 'Audit Purpose / Scope' }}</label>
                <textarea name="note" rows="2" placeholder="e.g. End of Quarter Q3 physical inventory verification..." class="form-input">{{ old('note') }}</textarea>
            </div>
        </div>

        <!-- Submit Footer -->
        <div class="pt-5 border-t border-slate-200/70 flex items-center justify-end space-x-3">
            <a href="{{ route('stock-takes.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary">
                <i class="bi bi-camera mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ចាប់ផ្តើម និងចាក់សោរូបថតស្តុក' : 'Start Audit & Freeze Snapshot' }}
            </button>
        </div>
    </form>
</div>
@endsection
