@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'បន្ថែមគណនីអ្នកប្រើប្រាស់ - IMS' : 'Add User Account - IMS')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-sky-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'សុវត្ថិភាព & គណនី' : 'Security & Access' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ app()->getLocale() == 'km' ? 'បន្ថែមគណនីអ្នកប្រើប្រាស់ថ្មី' : 'Add New User Account' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() == 'km' ? 'បង្កើតគណនីចូលប្រើប្រាស់ និងកំណត់សិទ្ធិប្រតិបត្តិការក្នុងប្រព័ន្ធ' : 'Create login credentials and configure system permissions' }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ក្រោយ' : 'Back to Users' }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="glass-card p-6 md:p-8 space-y-6">
        @csrf

        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានគណនី & សិទ្ធិប្រើប្រាស់' : 'Account & Access Level' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ឈ្មោះ អ៊ីមែល លេខសម្ងាត់ តួនាទី និងឃ្លាំងដែលត្រូវចាត់ចែង' : 'Name, login email, password, assigned role, and warehouse site' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ឈ្មោះពេញ' : 'Full Name' }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input font-medium" 
                           placeholder="{{ app()->getLocale() == 'km' ? 'ឧ. ចាន់ សុភា' : 'e.g. John Doe' }}" required>
                </div>
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'អាសយដ្ឋានអ៊ីមែល (ឈ្មោះគណនី)' : 'Email Address (Username)' }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input font-medium" placeholder="john@example.com" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ពាក្យសម្ងាត់' : 'Password' }}</label>
                    <input type="password" name="password" class="form-input font-mono" 
                           placeholder="{{ app()->getLocale() == 'km' ? 'យ៉ាងតិច ៨ តួអក្សរ' : 'Minimum 8 characters' }}" required>
                </div>
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'លេខទូរស័ព្ទ' : 'Phone Number' }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-input font-mono" placeholder="+855 ...">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'តួនាទី / សិទ្ធិសុវត្ថិភាព' : 'Security Role' }}</label>
                    <select name="role_id" class="form-input font-medium" required>
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសតួនាទី --' : '-- Select Role --' }}</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ឃ្លាំងដែលបានកំណត់' : 'Assigned Warehouse' }}</label>
                    <select name="warehouse_id" class="form-input font-medium">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ឃ្លាំងរួមទាំងអស់ (Global / All Warehouses) --' : '-- Global / All Warehouses --' }}</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">{{ app()->getLocale() == 'km' ? 'កំណត់ព្រំដែនប្រតិបត្តិការទៅកាន់ឃ្លាំងជាក់លាក់' : 'Limits user operations to a specific site if selected' }}</p>
                </div>
            </div>

            <div>
                <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ស្ថានភាពគណនី' : 'Account Status' }}</label>
                <select name="status" class="form-input font-medium" required>
                    <option value="ACTIVE" {{ old('status', 'ACTIVE') === 'ACTIVE' ? 'selected' : '' }}>
                        {{ app()->getLocale() == 'km' ? 'សកម្ម (អាចចូលប្រើប្រាស់បាន)' : 'Active (Can Login)' }}
                    </option>
                    <option value="INACTIVE" {{ old('status') === 'INACTIVE' ? 'selected' : '' }}>
                        {{ app()->getLocale() == 'km' ? 'ផ្អាកដំណើរការ (មិនអាចចូលប្រើប្រាស់បាន)' : 'Suspended / Inactive (Cannot Login)' }}
                    </option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200/70">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary !bg-sky-600 hover:!bg-sky-700 shadow-md shadow-sky-500/20">
                <i class="bi bi-person-check mr-1.5 text-sm"></i>
                {{ app()->getLocale() == 'km' ? 'បង្កើតគណនីអ្នកប្រើប្រាស់' : 'Create User Account' }}
            </button>
        </div>
    </form>
</div>
@endsection
