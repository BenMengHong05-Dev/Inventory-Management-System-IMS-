@extends('layouts.app')

@section('title', t('locations') . ' - IMS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('locations') }}</h1>
            <p class="text-xs text-gray-500">{{ t('manage') }} {{ t('locations') }} (Racks, Shelves, Bins)</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="document.getElementById('create_modal').classList.remove('hidden')" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('add_new') }} {{ t('locations') }}
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar (No white card background, no shadow, search on left, filters on right) -->
    <form method="GET" action="{{ route('master.locations.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ t('search') }}..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-56">
                <x-select name="warehouse_id" wrapperClass="!mb-0">
                    <option value="">{{ t('all') }} {{ t('warehouses') }}</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ ($warehouseId ?? '') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }} ({{ $w->code }})
                        </option>
                    @endforeach
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('filter') }}
            </x-button>
            @if(!empty($search) || !empty($warehouseId))
            <a href="{{ route('master.locations.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full shrink-0" title="Clear">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
            @endif
        </div>
    </form>

    <!-- Locations Table -->
    <x-table :pagination="$locations">
        <x-slot name="head">
            <th>{{ t('code') }}</th>
            <th>{{ t('warehouses') }}</th>
            <th>{{ t('rack') ?? 'Rack' }} / {{ t('shelf') ?? 'Shelf' }} / {{ t('bin') ?? 'Bin' }}</th>
            <th>{{ t('building') ?? 'Building' }} / {{ t('room') ?? 'Room' }}</th>
            <th>{{ t('current_stock') ?? 'Stock' }}</th>
            <th>{{ t('status') }}</th>
            <th class="text-right">{{ t('actions') }}</th>
        </x-slot>

        @forelse($locations as $loc)
        <tr>
            <td class="font-mono text-xs font-bold text-teal-600">{{ $loc->code }}</td>
            <td class="font-medium text-slate-800 text-xs">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                    <i class="bi bi-building"></i> {{ $loc->warehouse_name }}
                </span>
            </td>
            <td class="text-xs text-slate-600 font-mono">
                <span class="font-semibold text-slate-700">R:</span> {{ $loc->rack ?: '-' }} &nbsp;|&nbsp;
                <span class="font-semibold text-slate-700">S:</span> {{ $loc->shelf ?: '-' }} &nbsp;|&nbsp;
                <span class="font-semibold text-slate-700">B:</span> {{ $loc->bin ?: '-' }}
            </td>
            <td class="text-xs text-slate-500">
                {{ $loc->building ?: '-' }} {{ $loc->room ? ' • ' . $loc->room : '' }}
            </td>
            <td>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ ($loc->current_stock ?? 0) > 0 ? 'bg-teal-50 text-teal-700 border border-teal-200' : 'bg-slate-100 text-slate-500' }}">
                    <i class="bi bi-box-seam mr-1"></i> {{ number_format($loc->current_stock ?? 0) }}
                </span>
            </td>
            <td>
                @if(strtoupper($loc->status ?? '') === 'ACTIVE')
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
                <button onclick="editLocation({{ json_encode($loc) }})" class="btn-action btn-action-edit" title="{{ t('edit') }}">
                    <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" action="{{ route('master.locations.destroy', $loc->id) }}" onsubmit="return confirm('{{ t('confirm_delete') }}');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-action-delete" title="{{ t('delete') }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
            <x-table-empty :colspan="7" icon="bi bi-geo" :title="t('no_data_available')" />
        @endforelse
    </x-table>
</div>

<!-- Create Modal -->
<div id="create_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4 m-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-plus-circle text-teal-600"></i> {{ t('add_new') }} {{ t('locations') }}
            </h3>
            <button onclick="document.getElementById('create_modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('master.locations.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label required">{{ t('warehouses') }}</label>
                <select name="warehouse_id" class="form-input" required>
                    <option value="">-- {{ t('select') }} {{ t('warehouses') }} --</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }} ({{ $w->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label required">{{ t('code') }}</label>
                    <input type="text" name="code" class="form-input font-mono uppercase" placeholder="e.g. A-01-R01" required>
                </div>
                <div>
                    <label class="form-label required">{{ t('status') }}</label>
                    <select name="status" class="form-input" required>
                        <option value="ACTIVE">{{ t('active') }}</option>
                        <option value="INACTIVE">{{ t('inactive') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="form-label text-xs">{{ t('building') ?? 'Building' }}</label>
                    <input type="text" name="building" class="form-input text-xs" placeholder="Bldg A">
                </div>
                <div>
                    <label class="form-label text-xs">{{ t('floor') ?? 'Floor' }}</label>
                    <input type="text" name="floor" class="form-input text-xs" placeholder="1F">
                </div>
                <div>
                    <label class="form-label text-xs">{{ t('room') ?? 'Room' }}</label>
                    <input type="text" name="room" class="form-input text-xs" placeholder="Room 101">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="form-label text-xs">{{ t('rack') ?? 'Rack' }}</label>
                    <input type="text" name="rack" class="form-input text-xs" placeholder="R-01">
                </div>
                <div>
                    <label class="form-label text-xs">{{ t('shelf') ?? 'Shelf' }}</label>
                    <input type="text" name="shelf" class="form-input text-xs" placeholder="S-02">
                </div>
                <div>
                    <label class="form-label text-xs">{{ t('bin') ?? 'Bin' }}</label>
                    <input type="text" name="bin" class="form-input text-xs" placeholder="B-03">
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
                <i class="bi bi-pencil-square text-teal-600"></i> {{ t('edit') }} {{ t('locations') }}
            </h3>
            <button onclick="document.getElementById('edit_modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="edit_form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="form-label required">{{ t('warehouses') }}</label>
                <select name="warehouse_id" id="edit_warehouse_id" class="form-input" required>
                    <option value="">-- {{ t('select') }} {{ t('warehouses') }} --</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }} ({{ $w->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label required">{{ t('code') }}</label>
                    <input type="text" name="code" id="edit_code" class="form-input font-mono uppercase" required>
                </div>
                <div>
                    <label class="form-label required">{{ t('status') }}</label>
                    <select name="status" id="edit_status" class="form-input" required>
                        <option value="ACTIVE">{{ t('active') }}</option>
                        <option value="INACTIVE">{{ t('inactive') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="form-label text-xs">{{ t('building') ?? 'Building' }}</label>
                    <input type="text" name="building" id="edit_building" class="form-input text-xs">
                </div>
                <div>
                    <label class="form-label text-xs">{{ t('floor') ?? 'Floor' }}</label>
                    <input type="text" name="floor" id="edit_floor" class="form-input text-xs">
                </div>
                <div>
                    <label class="form-label text-xs">{{ t('room') ?? 'Room' }}</label>
                    <input type="text" name="room" id="edit_room" class="form-input text-xs">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="form-label text-xs">{{ t('rack') ?? 'Rack' }}</label>
                    <input type="text" name="rack" id="edit_rack" class="form-input text-xs">
                </div>
                <div>
                    <label class="form-label text-xs">{{ t('shelf') ?? 'Shelf' }}</label>
                    <input type="text" name="shelf" id="edit_shelf" class="form-input text-xs">
                </div>
                <div>
                    <label class="form-label text-xs">{{ t('bin') ?? 'Bin' }}</label>
                    <input type="text" name="bin" id="edit_bin" class="form-input text-xs">
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
function editLocation(loc) {
    document.getElementById('edit_form').action = `/master/locations/${loc.id}`;
    document.getElementById('edit_warehouse_id').value = loc.warehouse_id;
    document.getElementById('edit_code').value = loc.code || '';
    document.getElementById('edit_building').value = loc.building || '';
    document.getElementById('edit_floor').value = loc.floor || '';
    document.getElementById('edit_room').value = loc.room || '';
    document.getElementById('edit_rack').value = loc.rack || '';
    document.getElementById('edit_shelf').value = loc.shelf || '';
    document.getElementById('edit_bin').value = loc.bin || '';
    document.getElementById('edit_description').value = loc.description || '';
    document.getElementById('edit_status').value = (loc.status || 'ACTIVE').toUpperCase();
    document.getElementById('edit_modal').classList.remove('hidden');
}
</script>
@endpush
@endsection
