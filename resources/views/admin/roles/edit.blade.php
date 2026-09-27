@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'កំណត់សិទ្ធិសម្រាប់ ' . $role->name . ' - IMS' : 'Permissions for ' . $role->name . ' - IMS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-violet-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-violet-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'សិទ្ធិតួនាទីប្រព័ន្ធ' : 'Role Permissions' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ app()->getLocale() == 'km' ? 'កំណត់សិទ្ធិ៖ ' . $role->name : 'Role Permissions: ' . $role->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() == 'km' ? 'បើក ឬកំណត់កម្រិតសិទ្ធិប្រតិបត្តិការតាមម៉ូឌុលនីមួយៗសម្រាប់តួនាទីនេះ' : 'Enable or restrict module-level capabilities for this system role' }}
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" onclick="toggleAll(true)" class="btn-secondary !text-xs !py-1.5 !px-3">
                <i class="bi bi-check-all mr-1 text-emerald-600"></i>
                {{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទាំងអស់' : 'Select All' }}
            </button>
            <button type="button" onclick="toggleAll(false)" class="btn-secondary !text-xs !py-1.5 !px-3">
                <i class="bi bi-x mr-1 text-rose-500"></i>
                {{ app()->getLocale() == 'km' ? 'ដោះចេញទាំងអស់' : 'Deselect All' }}
            </button>
            <a href="{{ route('admin.roles.index') }}" class="btn-secondary !text-xs !py-1.5 !px-3">
                <i class="bi bi-arrow-left mr-1"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ក្រោយ' : 'Back to Roles' }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @php
            $grouped = [];
            foreach($permissions as $p) {
                $grouped[$p->module][] = $p;
            }
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($grouped as $module => $perms)
            <div class="glass-card p-5 space-y-3.5 border border-slate-200/80 transition-all hover:shadow-md">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-violet-500"></span>
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">{{ ucfirst($module) }}</h2>
                    </div>
                    <button type="button" onclick="toggleModule('{{ $module }}', this)" 
                            class="text-xs text-violet-600 hover:text-violet-800 font-bold px-2 py-0.5 rounded-md hover:bg-violet-50 transition">
                        {{ app()->getLocale() == 'km' ? 'បិទ/បើក' : 'Toggle' }}
                    </button>
                </div>
                <div class="space-y-1.5">
                    @foreach($perms as $p)
                    <label class="flex items-start gap-2.5 text-xs text-slate-700 cursor-pointer hover:bg-slate-50/80 p-2 rounded-xl border border-transparent hover:border-slate-200/60 transition group">
                        <input type="checkbox" name="permissions[]" value="{{ $p->id }}" data-module="{{ $module }}" 
                               class="perm-checkbox rounded text-violet-600 focus:ring-violet-500 mt-0.5 border-slate-300" 
                               {{ in_array($p->id, $rolePermissions) ? 'checked' : '' }}>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-800 group-hover:text-violet-700 transition">{{ $p->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono tracking-tight mt-0.5">{{ $p->slug }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200/80">
            <a href="{{ route('admin.roles.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary !bg-violet-600 hover:!bg-violet-700 shadow-md shadow-violet-500/20">
                <i class="bi bi-shield-check mr-1.5 text-sm"></i>
                {{ app()->getLocale() == 'km' ? 'រក្សាទុកសិទ្ធិតួនាទី' : 'Save Role Permissions' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleAll(checked) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = checked);
}
function toggleModule(mod, btn) {
    const boxes = document.querySelectorAll(`.perm-checkbox[data-module="${mod}"]`);
    const anyUnchecked = Array.from(boxes).some(cb => !cb.checked);
    boxes.forEach(cb => cb.checked = anyUnchecked);
}
</script>
@endpush
@endsection
