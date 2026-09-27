@extends('layouts.app')

@section('title', t('units') . ' - IMS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('units') }}</h1>
            <p class="text-xs text-gray-500">{{ t('manage') }} {{ t('units') }} (PCS, BOX, KG, LTR...)</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="document.getElementById('create_modal').classList.remove('hidden')" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i> {{ t('add_new') }} {{ t('units') }}
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar (No white card background, no shadow, search on left, filters on right) -->
    <form method="GET" action="{{ route('master.units.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ t('search') }}..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex items-center gap-2 justify-end w-full lg:w-auto">
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('filter') }}
            </x-button>
            @if(!empty($search))
            <a href="{{ route('master.units.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Clear">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
            @endif
        </div>
    </form>

    <!-- Units Table -->
    <x-table :pagination="$units">
        <x-slot name="head">
            <th>{{ t('code') }}</th>
            <th>{{ t('name') }}</th>
            <th>{{ t('items') }}</th>
            <th>{{ t('created_at') ?? 'Created At' }}</th>
            <th class="text-right">{{ t('actions') }}</th>
        </x-slot>

        @forelse($units as $u)
        <tr>
            <td class="font-mono text-xs font-bold text-amber-600">{{ $u->code }}</td>
            <td>
                <div class="font-bold text-slate-800">{{ localized_name($u, 'name') }}</div>
                <div class="text-xs text-slate-400">{{ $u->name_en ?? '' }} {{ !empty($u->name_km) && $u->name_km !== $u->name_en ? ' • ' . $u->name_km : '' }}</div>
            </td>
            <td>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/70">
                    <i class="bi bi-box-seam mr-1"></i> {{ $u->items_count ?? 0 }}
                </span>
            </td>
            <td class="text-xs text-slate-500">{{ !empty($u->created_at) ? date('Y-m-d', strtotime($u->created_at)) : '-' }}</td>
            <td class="text-right space-x-1.5 whitespace-nowrap">
                <button onclick="editUnit({{ json_encode($u) }})" class="btn-action btn-action-edit" title="{{ t('edit') }}">
                    <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" action="{{ route('master.units.destroy', $u->id) }}" onsubmit="return confirm('{{ t('confirm_delete') }}');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-action-delete" title="{{ t('delete') }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
            <x-table-empty :colspan="5" icon="bi bi-rulers" :title="t('no_data_available')" />
        @endforelse
    </x-table>
</div>

<!-- Create Modal -->
<div id="create_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4 m-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-plus-circle text-amber-600"></i> {{ t('add_new') }} {{ t('units') }}
            </h3>
            <button onclick="document.getElementById('create_modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('master.units.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label required">{{ t('code') }}</label>
                <input type="text" name="code" class="form-input uppercase font-mono" placeholder="e.g. PCS, BOX, KG, LTR" required>
            </div>
            <div>
                <label class="form-label required">{{ t('name') }} (Khmer)</label>
                <input type="text" name="name_km" class="form-input" placeholder="ឧ. ឯកតា, ប្រអប់, គីឡូក្រាម" required>
            </div>
            <div>
                <label class="form-label required">{{ t('name') }} (English)</label>
                <input type="text" name="name_en" class="form-input" placeholder="e.g. Piece, Box, Kilogram" required>
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
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4 m-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-pencil-square text-amber-600"></i> {{ t('edit') }} {{ t('units') }}
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
                <input type="text" name="code" id="edit_code" class="form-input uppercase font-mono" required>
            </div>
            <div>
                <label class="form-label required">{{ t('name') }} (Khmer)</label>
                <input type="text" name="name_km" id="edit_name_km" class="form-input" required>
            </div>
            <div>
                <label class="form-label required">{{ t('name') }} (English)</label>
                <input type="text" name="name_en" id="edit_name_en" class="form-input" required>
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
function editUnit(u) {
    document.getElementById('edit_form').action = `/master/units/${u.id}`;
    document.getElementById('edit_code').value = u.code || '';
    document.getElementById('edit_name_km').value = u.name_km || u.name_en || '';
    document.getElementById('edit_name_en').value = u.name_en || u.name_km || '';
    document.getElementById('edit_modal').classList.remove('hidden');
}
</script>
@endpush
@endsection
