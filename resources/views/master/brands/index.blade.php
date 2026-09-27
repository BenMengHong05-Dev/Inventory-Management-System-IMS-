@extends('layouts.app')

@section('title', t('brands') . ' - IMS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('brands') }}</h1>
            <p class="text-xs text-gray-500">{{ t('manage') }} {{ t('brands') }} &amp; {{ t('manufacturers') ?? 'Manufacturers' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" onclick="openModal('create_modal')" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('add_new') }} {{ t('brands') }}
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar (No white card background, no shadow, search on left, filters on right) -->
    <form method="GET" action="{{ route('master.brands.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
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
            <a href="{{ route('master.brands.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Clear">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
            @endif
        </div>
    </form>

    <!-- Brands Data Table -->
    <x-table :pagination="$brands">
        <x-slot name="head">
            <th class="w-10 text-center">
                <input type="checkbox" class="rounded text-sky-600 focus:ring-sky-500 border-slate-300">
            </th>
            <th>{{ t('code') }}</th>
            <th>{{ t('name') }}</th>
            <th>{{ t('description') }}</th>
            <th>{{ t('items') }}</th>
            <th>{{ t('status') }}</th>
            <th class="text-right">{{ t('actions') }}</th>
        </x-slot>

        @forelse($brands as $b)
        <tr>
            <td class="text-center">
                <input type="checkbox" class="rounded text-sky-600 focus:ring-sky-500 border-slate-300">
            </td>
            <td>
                <span class="font-mono text-xs font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md">
                    {{ $b->code }}
                </span>
            </td>
            <td class="font-bold text-slate-900 text-xs">{{ $b->name }}</td>
            <td class="text-xs text-slate-500 max-w-xs truncate">{{ $b->description ?: '-' }}</td>
            <td>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                    <i data-lucide="package" class="w-3 h-3 mr-1 text-slate-400"></i> {{ $b->items_count ?? $b->item_count ?? 0 }}
                </span>
            </td>
            <td>
                @if(strtoupper($b->status ?? '') === 'ACTIVE')
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
                <button onclick="editBrand({{ json_encode($b) }})" class="btn-action btn-action-edit" title="{{ t('edit') }}">
                    <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" action="{{ route('master.brands.destroy', $b->id) }}" onsubmit="return confirm('{{ t('confirm_delete') }}');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-action-delete" title="{{ t('delete') }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
            <x-table-empty :colspan="7" icon="tag" :title="t('no_data_available')" />
        @endforelse
    </x-table>
</div>

<!-- Create Modal (Smooth Animation & Rounded) -->
<div id="create_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-md hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 space-y-4 border border-slate-100">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-[#ff5a36]"></i> {{ t('add_new') }} {{ t('brands') }}
            </h3>
            <button onclick="closeModal('create_modal')" class="p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('master.brands.store') }}" class="space-y-3.5">
            @csrf
            <div>
                <label class="form-label required">{{ t('code') }}</label>
                <input type="text" name="code" class="form-input uppercase font-mono" placeholder="e.g. DELL, HP, APPLE" required>
            </div>
            <div>
                <label class="form-label required">{{ t('name') }}</label>
                <input type="text" name="name" class="form-input" placeholder="e.g. Dell Inc." required>
            </div>
            <div>
                <label class="form-label required">{{ t('status') }}</label>
                <select name="status" class="form-input" required>
                    <option value="ACTIVE">{{ t('active') }}</option>
                    <option value="INACTIVE">{{ t('inactive') }}</option>
                </select>
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
    <div class="bg-white rounded-3xl w-full max-w-md p-6 space-y-4 border border-slate-100">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="pencil" class="w-4 h-4 text-[#ff5a36]"></i> {{ t('edit') }} {{ t('brands') }}
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
            <div>
                <label class="form-label required">{{ t('name') }}</label>
                <input type="text" name="name" id="edit_name" class="form-input" required>
            </div>
            <div>
                <label class="form-label required">{{ t('status') }}</label>
                <select name="status" id="edit_status" class="form-input" required>
                    <option value="ACTIVE">{{ t('active') }}</option>
                    <option value="INACTIVE">{{ t('inactive') }}</option>
                </select>
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
function editBrand(b) {
    document.getElementById('edit_form').action = `/master/brands/${b.id}`;
    document.getElementById('edit_code').value = b.code || '';
    document.getElementById('edit_name').value = b.name || '';
    document.getElementById('edit_description').value = b.description || '';
    document.getElementById('edit_status').value = (b.status || 'ACTIVE').toUpperCase();
    openModal('edit_modal');
}
</script>
@endpush
@endsection
