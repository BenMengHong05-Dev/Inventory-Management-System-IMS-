@extends('layouts.app')

@section('title', t('categories') . ' - IMS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('categories') }}</h1>
            <p class="text-xs text-gray-500">{{ t('manage') }} {{ t('categories') }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" onclick="openModal('create_modal')" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('add_new') }} {{ t('categories') }}
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar (No white card background, no shadow, search on left, filters on right) -->
    <form method="GET" action="{{ route('categories.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
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
            <a href="{{ route('categories.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Clear">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
            @endif
        </div>
    </form>

    <!-- Categories Data Table -->
    <x-table :pagination="$categories">
        <x-slot name="head">
            <th class="w-10 text-center">
                <input type="checkbox" class="rounded text-sky-600 focus:ring-sky-500 border-slate-300">
            </th>
            <th>{{ t('code') }}</th>
            <th>{{ t('name') }}</th>
            <th>{{ t('parent_category') ?? 'Parent Category' }}</th>
            <th>{{ t('items') }}</th>
            <th>{{ t('status') }}</th>
            <th class="text-right">{{ t('actions') }}</th>
        </x-slot>

        @forelse($categories as $c)
        <tr>
            <td class="text-center">
                <input type="checkbox" class="rounded text-sky-600 focus:ring-sky-500 border-slate-300">
            </td>
            <td>
                <span class="font-mono text-xs font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md">
                    {{ $c->code }}
                </span>
            </td>
            <td>
                <div class="font-bold text-slate-900 text-xs">{{ localized_name($c, 'name') }}</div>
                <div class="text-[10px] text-slate-400">
                    {{ $c->name_en ?? '' }} {{ !empty($c->name_km) && $c->name_km !== $c->name_en ? ' • ' . $c->name_km : '' }}
                </div>
                @if($c->description)
                    <div class="text-[10px] text-slate-400 truncate max-w-xs mt-0.5">{{ $c->description }}</div>
                @endif
            </td>
            <td class="text-xs text-slate-600">
                {{ localized_name($c, 'parent_name') ?: '-' }}
            </td>
            <td>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                    <i data-lucide="package" class="w-3 h-3 mr-1 text-slate-400"></i> {{ $c->item_count ?? 0 }}
                </span>
            </td>
            <td>
                @if(strtoupper($c->status ?? '') === 'ACTIVE')
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
                <button onclick="editCategory({{ json_encode($c) }})" class="btn-action btn-action-edit" title="{{ t('edit') }}">
                    <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" action="{{ route('master.categories.destroy', $c->id) }}" onsubmit="return confirm('{{ t('confirm_delete') }}');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-action-delete" title="{{ t('delete') }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
            <x-table-empty :colspan="7" icon="folder-tree" :title="t('no_data_available')" />
        @endforelse
    </x-table>
</div>

<!-- Create Modal (Smooth Animation & Rounded) -->
<div id="create_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-md hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg p-6 space-y-4 border border-slate-100">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-[#ff5a36]"></i> {{ t('add_new') }} {{ t('categories') }}
            </h3>
            <button onclick="closeModal('create_modal')" class="p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('master.categories.store') }}" class="space-y-3.5">
            @csrf
            <div>
                <label class="form-label required">{{ t('code') }}</label>
                <input type="text" name="code" class="form-input uppercase font-mono" placeholder="e.g. CAT-001" required>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label required">{{ t('name') }} (Khmer)</label>
                    <input type="text" name="name_km" class="form-input" placeholder="ឧ. គ្រឿងអេឡិចត្រូនិច" required>
                </div>
                <div>
                    <label class="form-label required">{{ t('name') }} (English)</label>
                    <input type="text" name="name_en" class="form-input" placeholder="e.g. Electronics" required>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label">{{ t('parent_category') ?? 'Parent Category' }}</label>
                    <select name="parent_id" class="form-input">
                        <option value="">-- {{ t('none') ?? 'None' }} --</option>
                        @foreach($parentCategories as $p)
                            <option value="{{ $p->id }}">{{ localized_name($p, 'name') }}</option>
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
                <button type="button" onclick="closeModal('create_modal')" class="btn-secondary text-xs">{{ t('cancel') }}</button>
                <button type="submit" class="btn-primary text-xs">{{ t('save') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal (Smooth Animation & Rounded) -->
<div id="edit_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-md hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg p-6 space-y-4 border border-slate-100">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="pencil" class="w-4 h-4 text-[#ff5a36]"></i> {{ t('edit') }} {{ t('categories') }}
            </h3>
            <button onclick="closeModal('edit_modal')" class="p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="edit_form" method="POST" action="" class="space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label class="form-label required">{{ t('code') }}</label>
                <input type="text" name="code" id="edit_code" class="form-input uppercase font-mono" required>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label required">{{ t('name') }} (Khmer)</label>
                    <input type="text" name="name_km" id="edit_name_km" class="form-input" required>
                </div>
                <div>
                    <label class="form-label required">{{ t('name') }} (English)</label>
                    <input type="text" name="name_en" id="edit_name_en" class="form-input" required>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label">{{ t('parent_category') ?? 'Parent Category' }}</label>
                    <select name="parent_id" id="edit_parent_id" class="form-input">
                        <option value="">-- {{ t('none') ?? 'None' }} --</option>
                        @foreach($parentCategories as $p)
                            <option value="{{ $p->id }}">{{ localized_name($p, 'name') }}</option>
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
                <button type="button" onclick="closeModal('edit_modal')" class="btn-secondary text-xs">{{ t('cancel') }}</button>
                <button type="submit" class="btn-primary text-xs">{{ t('save') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editCategory(c) {
    document.getElementById('edit_form').action = `/master/categories/${c.id}`;
    document.getElementById('edit_code').value = c.code || '';
    document.getElementById('edit_name_km').value = c.name_km || c.name_en || '';
    document.getElementById('edit_name_en').value = c.name_en || c.name_km || '';
    document.getElementById('edit_parent_id').value = c.parent_id || '';
    document.getElementById('edit_description').value = c.description || '';
    document.getElementById('edit_status').value = (c.status || 'ACTIVE').toUpperCase();
    openModal('edit_modal');
}
</script>
@endpush
@endsection
