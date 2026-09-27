@extends('layouts.app')

@section('title', t('add_new') . ' ' . t('employees') . ' - IMS')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'ទិន្នន័យបុគ្គលិក' : 'Employee Directory' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ t('add_new') }} {{ t('employees') }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() == 'km' ? 'ចុះឈ្មោះបុគ្គលិកថ្មីសម្រាប់ទទួលខុសត្រូវទ្រព្យ និងប្រតិបត្តិការស្តុក' : 'Register a new employee for asset assignment and inventory transactions' }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('master.employees.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ t('back') }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('master.employees.store') }}" enctype="multipart/form-data" class="glass-card p-6 md:p-8 space-y-6">
        @csrf

        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានផ្ទាល់ខ្លួន និងតួនាទី' : 'Personal & Role Details' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'បញ្ចូលកូដ ឈ្មោះជាពីរភាសា ភេទ ផ្នែក និងមុខតំណែង' : 'Enter code, bilingual names, gender, department, and job title' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="form-label required">{{ t('code') }}</label>
                    <input type="text" name="code" value="{{ old('code', $nextCode ?? '') }}" class="form-input font-mono uppercase" placeholder="EMP-001" required>
                </div>
                <div>
                    <label class="form-label required">{{ t('name') }} (Khmer)</label>
                    <input type="text" name="name_km" value="{{ old('name_km') }}" class="form-input font-medium" placeholder="ឧ. សុខ សាន" required>
                </div>
                <div>
                    <label class="form-label required">{{ t('name') }} (English)</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-input font-medium" placeholder="e.g. Sok San" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="form-label required">{{ t('gender') ?? 'Gender' }}</label>
                    <select name="gender" class="form-input font-medium" required>
                        <option value="MALE" {{ old('gender') == 'MALE' ? 'selected' : '' }}>{{ t('male') ?? 'Male' }}</option>
                        <option value="FEMALE" {{ old('gender') == 'FEMALE' ? 'selected' : '' }}>{{ t('female') ?? 'Female' }}</option>
                        <option value="OTHER" {{ old('gender') == 'OTHER' ? 'selected' : '' }}>{{ t('other') ?? 'Other' }}</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ t('departments') }}</label>
                    <select name="department_id" class="form-input font-medium">
                        <option value="">-- {{ t('select') }} {{ t('departments') }} --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>
                                {{ localized_name($d, 'name') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ t('position') ?? 'Position' }}</label>
                    <input type="text" name="position" value="{{ old('position') }}" class="form-input font-medium" placeholder="e.g. IT Specialist">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="form-label">{{ t('phone') ?? 'Phone' }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-input font-mono" placeholder="+855 12 345 678">
                </div>
                <div>
                    <label class="form-label">{{ t('email') ?? 'Email' }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="employee@company.com">
                </div>
                <div>
                    <label class="form-label required">{{ t('status') }}</label>
                    <select name="status" class="form-input font-medium" required>
                        <option value="ACTIVE" {{ old('status') == 'ACTIVE' ? 'selected' : '' }}>{{ t('active') }}</option>
                        <option value="INACTIVE" {{ old('status') == 'INACTIVE' ? 'selected' : '' }}>{{ t('inactive') }}</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">{{ t('photo') ?? 'Photo' }}</label>
                <div class="mt-1 flex items-center gap-4">
                    <input type="file" name="photo" accept="image/*" class="form-input text-xs">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200/70">
            <a href="{{ route('master.employees.index') }}" class="btn-secondary">{{ t('cancel') }}</a>
            <button type="submit" class="btn-primary !bg-indigo-600 hover:!bg-indigo-700 shadow-md shadow-indigo-500/20">
                <i class="bi bi-check-lg mr-1.5 text-sm"></i>
                {{ t('save') }}
            </button>
        </div>
    </form>
</div>
@endsection
