@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'ប្រគល់ទ្រព្យសម្បត្តិជូនបុគ្គលិក - IMS' : 'New Asset Assignment - IMS')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'ការបែងចែកទ្រព្យសម្បត្តិ' : 'Asset Allocation' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ app()->getLocale() == 'km' ? 'ប្រគល់ទ្រព្យសម្បត្តិជូនបុគ្គលិក' : 'Assign Asset to Personnel' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() == 'km' ? 'កត់ត្រាការប្រគល់ទ្រព្យសម្បត្តិដែលមានក្នុងស្តុកជូនបុគ្គលិកប្រើប្រាស់' : 'Check out an available asset to an employee with assignment conditions' }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('assets.assignments.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ក្រោយ' : 'Back to List' }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('assets.assignments.store') }}" class="glass-card p-6 md:p-8 space-y-6">
        @csrf

        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-person-check"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មាននៃការប្រគល់' : 'Assignment Details' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទ្រព្យសម្បត្តិ បុគ្គលិកទទួល កាលបរិច្ឆេទ និងទីតាំងប្រើប្រាស់' : 'Select asset, recipient employee, date, and deployment location' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទ្រព្យសម្បត្តិ' : 'Select Asset' }}</label>
                    <select name="asset_id" class="form-input font-medium" required>
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសទ្រព្យសម្បត្តិទំនេរ --' : '-- Choose Available Asset --' }}</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                [{{ $asset->asset_tag }}] {{ $asset->name }} (SN: {{ $asset->serial_number ?: 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ប្រគល់ជូនបុគ្គលិក' : 'Assign to Employee' }}</label>
                    <select name="employee_id" class="form-input font-medium" required>
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសបុគ្គលិក --' : '-- Choose Employee --' }}</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->code }} - {{ $emp->name }} ({{ $emp->department_name ?: 'No Dept' }} / {{ $emp->designation ?: 'Staff' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទប្រគល់' : 'Assigned Date' }}</label>
                    <input type="date" name="assigned_date" value="{{ old('assigned_date', date('Y-m-d')) }}" class="form-input font-mono" required>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទទទួលមកវិញ (ជាជម្រើស)' : 'Expected Due Date (Optional)' }}</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}" class="form-input font-mono">
                    <p class="text-[10px] text-slate-400 mt-1">{{ app()->getLocale() == 'km' ? 'ទុកទំនេរសម្រាប់ការប្រគល់ប្រើប្រាស់អចិន្ត្រៃយ៍' : 'Leave empty for permanent or indefinite assignment' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ទីតាំងប្រើប្រាស់ជាក់ស្ដែង' : 'Deployment Location' }}</label>
                    <select name="location_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ទីតាំងការិយាល័យ / កន្លែងធ្វើការ --' : '-- Physical Location / Office --' }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->warehouse_name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ស្ថានភាពពេលប្រគល់ & ចំណាំ' : 'Condition & Notes' }}</label>
                    <textarea name="notes" rows="3" class="form-input"
                              placeholder="{{ app()->getLocale() == 'km' ? 'កត់សម្គាល់ស្ថានភាពរូបវន្តពេលប្រគល់ គ្រឿងបន្លាស់ ឆ្នាំងសាក...' : 'Note physical condition upon handover, included accessories, power cables, etc.' }}">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200/70">
            <a href="{{ route('assets.assignments.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary !bg-emerald-600 hover:!bg-emerald-700 shadow-md shadow-emerald-500/20">
                <i class="bi bi-check2-circle mr-1.5 text-sm"></i>
                {{ app()->getLocale() == 'km' ? 'បញ្ជាក់ការប្រគល់ទ្រព្យសម្បត្តិ' : 'Confirm Assignment' }}
            </button>
        </div>
    </form>
</div>
@endsection
