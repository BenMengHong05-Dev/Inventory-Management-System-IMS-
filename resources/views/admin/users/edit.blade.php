@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'កែប្រែគណនីអ្នកប្រើប្រាស់ - IMS' : 'Edit User - IMS')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-sky-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'សុវត្ថិភាព & គណនី' : 'Security & Access' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ app()->getLocale() == 'km' ? 'កែប្រែគណនី៖ ' . $user->name : 'Edit User: ' . $user->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() == 'km' ? 'ធ្វើបច្ចុប្បន្នភាពសិទ្ធិប្រើប្រាស់ តួនាទីសុវត្ថិភាព និងការចាត់ចែងឃ្លាំង' : 'Update account permissions, security role, and warehouse assignment' }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ក្រោយ' : 'Back to Users' }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="glass-card p-6 md:p-8 space-y-6">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-person-gear"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានគណនី & សិទ្ធិប្រើប្រាស់' : 'Account & Access Level' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ឈ្មោះ អ៊ីមែល លេខសម្ងាត់ តួនាទី និងឃ្លាំងដែលត្រូវចាត់ចែង' : 'Name, login email, password, assigned role, and warehouse site' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ឈ្មោះពេញ' : 'Full Name' }}</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input font-medium" required>
                </div>
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'អាសយដ្ឋានអ៊ីមែល' : 'Email Address' }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input font-medium" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ពាក្យសម្ងាត់ (ទុកទំនេរបើមិនចង់ផ្លាស់ប្តូរ)' : 'Password (Leave blank to keep current)' }}</label>
                    <input type="password" name="password" class="form-input font-mono" 
                           placeholder="{{ app()->getLocale() == 'km' ? 'បញ្ចូលពាក្យសម្ងាត់ថ្មីបើចង់ផ្លាស់ប្តូរ' : 'Enter new password to change' }}">
                </div>
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'លេខទូរស័ព្ទ' : 'Phone Number' }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'តួនាទី / សិទ្ធិសុវត្ថិភាព' : 'Security Role' }}</label>
                    <select name="role_id" class="form-input font-medium" required>
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសតួនាទី --' : '-- Select Role --' }}</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ old('role_id', $user->role_id) == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ឃ្លាំងដែលបានកំណត់' : 'Assigned Warehouse' }}</label>
                    <select name="warehouse_id" class="form-input font-medium">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ឃ្លាំងរួមទាំងអស់ (Global / All Warehouses) --' : '-- Global / All Warehouses --' }}</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ old('warehouse_id', $user->warehouse_id) == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ស្ថានភាពគណនី' : 'Account Status' }}</label>
                <select name="status" class="form-input font-medium" required>
                    <option value="ACTIVE" {{ old('status', $user->status ?? 'ACTIVE') === 'ACTIVE' ? 'selected' : '' }}>
                        {{ app()->getLocale() == 'km' ? 'សកម្ម (អាចចូលប្រើប្រាស់បាន)' : 'Active (Can Login)' }}
                    </option>
                    <option value="INACTIVE" {{ old('status', $user->status ?? '') === 'INACTIVE' ? 'selected' : '' }}>
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
                <i class="bi bi-check-lg mr-1.5 text-sm"></i>
                {{ app()->getLocale() == 'km' ? 'ធ្វើបច្ចុប្បន្នភាពគណនី' : 'Update User Account' }}
            </button>
        </div>
    </form>
</div>
@endsection
