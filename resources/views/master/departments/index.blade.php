@extends('layouts.app')

@section('title', t('departments') . ' - IMS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('departments') }}</h1>
            <p class="text-xs text-gray-500">{{ t('manage') }} {{ t('departments') }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="document.getElementById('create_modal').classList.remove('hidden')" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('add_new') }} {{ t('departments') }}
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar (No white card background, no shadow, search on left, filters on right) -->
    <form method="GET" action="{{ route('master.departments.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ t('search') }}..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-36">
                <x-select name="status" wrapperClass="!mb-0">
                    <option value="">{{ t('all') }} {{ t('status') }}</option>
                    <option value="ACTIVE" {{ ($status ?? '') === 'ACTIVE' ? 'selected' : '' }}>{{ t('active') }}</option>
                    <option value="INACTIVE" {{ ($status ?? '') === 'INACTIVE' ? 'selected' : '' }}>{{ t('inactive') }}</option>
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('filter') }}
            </x-button>
            @if(!empty($search) || !empty($status))
            <a href="{{ route('master.departments.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Clear">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
            @endif
        </div>
    </form>

    <!-- Departments Table -->
    <x-table :pagination="$departments">
        <x-slot name="head">
            <th>{{ t('code') }}</th>
            <th>{{ t('name') }}</th>
            <th>{{ t('manager') ?? 'Manager' }}</th>
            <th>{{ t('employees') }}</th>
            <th>{{ t('description') }}</th>
            <th>{{ t('status') }}</th>
            <th class="text-right">{{ t('actions') }}</th>
        </x-slot>

        @forelse($departments as $d)
        <tr>
            <td class="font-mono text-xs font-bold text-sky-600">{{ $d->code }}</td>
            <td>
                <div class="font-bold text-slate-800">{{ localized_name($d, 'name') }}</div>
                <div class="text-xs text-slate-400">{{ $d->name_en ?? '' }} {{ !empty($d->name_km) && $d->name_km !== $d->name_en ? ' • ' . $d->name_km : '' }}</div>
            </td>
            <td class="text-xs font-medium text-slate-700">
                {{ $d->manager_name ?: '-' }}
            </td>
            <td>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                    <i class="bi bi-people mr-1"></i> {{ $d->employee_count ?? 0 }}
                </span>
            </td>
            <td class="text-xs text-slate-500 max-w-xs truncate">{{ $d->description ?: '-' }}</td>
            <td>
                @if(strtoupper($d->status ?? '') === 'ACTIVE')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ t('active') }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        {{ t('inactive') }}
                    </span>
                @endif
            </td>
            <td class="text-right space-x-1.5 whitespace-nowrap">
                <button onclick="editDepartment({{ json_encode($d) }})" class="btn-action btn-action-edit" title="{{ t('edit') }}">
                    <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" action="{{ route('master.departments.destroy', $d->id) }}" onsubmit="return confirm('{{ t('confirm_delete') }}');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-action-delete" title="{{ t('delete') }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
            <x-table-empty :colspan="7" icon="bi bi-building" :title="t('no_data_available')" />
        @endforelse
    </x-table>
</div>

<!-- Create Modal -->
<div id="create_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4 m-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-plus-circle text-sky-600"></i> {{ t('add_new') }} {{ t('departments') }}
            </h3>
            <button onclick="document.getElementById('create_modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('master.departments.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label required">{{ t('code') }}</label>
                <input type="text" name="code" class="form-input" placeholder="e.g. IT, HR, ACC" required>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label required">{{ t('name') }} (Khmer)</label>
                    <input type="text" name="name_km" class="form-input" placeholder="ឧ. ព័ត៌មានវិទ្យា" required>
                </div>
                <div>
                    <label class="form-label required">{{ t('name') }} (English)</label>
                    <input type="text" name="name_en" class="form-input" placeholder="e.g. Information Technology" required>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">{{ t('manager') ?? 'Manager' }}</label>
                    <select name="manager_id" class="form-input">
                        <option value="">-- {{ t('select') }} --</option>
                        @foreach($managers as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label required">{{ t('status') }}</label>
                    <select name="status" class="form-input" required>
                        <option value="ACTIVE">{{ t('active') }}</option>
                        <option value="INACTIVE">{{ t('inactive') }}</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">{{ t('description') }}</label>
                <textarea name="description" rows="2" class="form-input" placeholder="{{ t('notes') }}..."></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('create_modal').classList.add('hidden')" class="btn-secondary">{{ t('cancel') }}</button>
                <button type="submit" class="btn-primary">{{ t('save') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4 m-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-pencil-square text-sky-600"></i> {{ t('edit') }} {{ t('departments') }}
            </h3>
            <button onclick="document.getElementById('edit_modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="edit_form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="form-label required">{{ t('code') }}</label>
                <input type="text" name="code" id="edit_code" class="form-input" required>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label required">{{ t('name') }} (Khmer)</label>
                    <input type="text" name="name_km" id="edit_name_km" class="form-input" required>
                </div>
                <div>
                    <label class="form-label required">{{ t('name') }} (English)</label>
                    <input type="text" name="name_en" id="edit_name_en" class="form-input" required>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">{{ t('manager') ?? 'Manager' }}</label>
                    <select name="manager_id" id="edit_manager_id" class="form-input">
                        <option value="">-- {{ t('select') }} --</option>
                        @foreach($managers as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label required">{{ t('status') }}</label>
                    <select name="status" id="edit_status" class="form-input" required>
                        <option value="ACTIVE">{{ t('active') }}</option>
                        <option value="INACTIVE">{{ t('inactive') }}</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">{{ t('description') }}</label>
                <textarea name="description" id="edit_description" rows="2" class="form-input"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('edit_modal').classList.add('hidden')" class="btn-secondary">{{ t('cancel') }}</button>
                <button type="submit" class="btn-primary">{{ t('save') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editDepartment(d) {
    document.getElementById('edit_form').action = `/master/departments/${d.id}`;
    document.getElementById('edit_code').value = d.code || '';
    document.getElementById('edit_name_km').value = d.name_km || d.name_en || '';
    document.getElementById('edit_name_en').value = d.name_en || d.name_km || '';
    document.getElementById('edit_manager_id').value = d.manager_id || '';
    document.getElementById('edit_description').value = d.description || '';
    document.getElementById('edit_status').value = (d.status || 'ACTIVE').toUpperCase();
    document.getElementById('edit_modal').classList.remove('hidden');
}
</script>
@endpush
@endsection
