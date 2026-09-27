@extends('layouts.app')

@section('title', t('edit') . ' ' . t('employees') . ' - IMS')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'កែប្រែទិន្នន័យបុគ្គលិក' : 'Edit Employee' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ t('edit') }} {{ t('employees') }}: {{ localized_name($employee, 'name') }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() == 'km' ? 'ធ្វើបច្ចុប្បន្នភាពព័ត៌មានបុគ្គលិក ផ្នែក និងទំនាក់ទំនង' : 'Update employee profile, department placement, and contact records' }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('master.employees.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ t('back') }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('master.employees.update', $employee->id) }}" enctype="multipart/form-data" class="glass-card p-6 md:p-8 space-y-6">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានផ្ទាល់ខ្លួន និងតួនាទី' : 'Personal & Role Details' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ធ្វើបច្ចុប្បន្នភាពកូដ ឈ្មោះជាពីរភាសា ភេទ ផ្នែក និងមុខតំណែង' : 'Update code, bilingual names, gender, department, and job title' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="form-label required">{{ t('code') }}</label>
                    <input type="text" name="code" value="{{ old('code', $employee->code) }}" class="form-input font-mono uppercase" required>
                </div>
                <div>
                    <label class="form-label required">{{ t('name') }} (Khmer)</label>
                    <input type="text" name="name_km" value="{{ old('name_km', $employee->name_km ?? $employee->name_en) }}" class="form-input font-medium" required>
                </div>
                <div>
                    <label class="form-label required">{{ t('name') }} (English)</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $employee->name_en ?? $employee->name_km) }}" class="form-input font-medium" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="form-label required">{{ t('gender') ?? 'Gender' }}</label>
                    <select name="gender" class="form-input font-medium" required>
                        <option value="MALE" {{ old('gender', $employee->gender) == 'MALE' ? 'selected' : '' }}>{{ t('male') ?? 'Male' }}</option>
                        <option value="FEMALE" {{ old('gender', $employee->gender) == 'FEMALE' ? 'selected' : '' }}>{{ t('female') ?? 'Female' }}</option>
                        <option value="OTHER" {{ old('gender', $employee->gender) == 'OTHER' ? 'selected' : '' }}>{{ t('other') ?? 'Other' }}</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ t('departments') }}</label>
                    <select name="department_id" class="form-input font-medium">
                        <option value="">-- {{ t('select') }} {{ t('departments') }} --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ old('department_id', $employee->department_id) == $d->id ? 'selected' : '' }}>
                                {{ localized_name($d, 'name') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ t('position') ?? 'Position' }}</label>
                    <input type="text" name="position" value="{{ old('position', $employee->position ?? $employee->designation ?? '') }}" class="form-input font-medium">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="form-label">{{ t('phone') ?? 'Phone' }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="form-input font-mono">
                </div>
                <div>
                    <label class="form-label">{{ t('email') ?? 'Email' }}</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label required">{{ t('status') }}</label>
                    <select name="status" class="form-input font-medium" required>
                        <option value="ACTIVE" {{ strtoupper(old('status', $employee->status)) === 'ACTIVE' ? 'selected' : '' }}>{{ t('active') }}</option>
                        <option value="INACTIVE" {{ strtoupper(old('status', $employee->status)) === 'INACTIVE' ? 'selected' : '' }}>{{ t('inactive') }}</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">{{ t('photo') ?? 'Photo' }}</label>
                @if($employee->photo)
                    <div class="mb-3 flex items-center gap-3 p-2 rounded-xl bg-slate-50 border border-slate-200/80 w-fit">
                        <img src="{{ asset('storage/' . $employee->photo) }}" class="w-12 h-12 rounded-lg object-cover border border-slate-200" alt="Photo">
                        <span class="text-xs text-slate-500 font-medium">{{ app()->getLocale() == 'km' ? 'រូបថតបច្ចុប្បន្ន' : 'Current photo' }}</span>
                    </div>
                @endif
                <input type="file" name="photo" accept="image/*" class="form-input text-xs">
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
