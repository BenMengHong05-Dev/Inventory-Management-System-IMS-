@extends('layouts.app')

@section('title', t('messages.warehouses'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('messages.warehouses') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'គ្រប់គ្រងទីតាំងស្តុកកណ្តាល សាខា និងមជ្ឈមណ្ឌលចែកចាយ' : 'Manage central storage sites, branches, and distribution centers' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="document.getElementById('create_modal').classList.remove('hidden')" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i> {{ t('messages.create') }}
            </button>
        </div>
    </div>

    <!-- Warehouses Table -->
    <x-table :pagination="$warehouses">
        <x-slot name="head">
            <th>{{ t('messages.code') }} & {{ t('messages.warehouse') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'អ្នកគ្រប់គ្រង' : 'Manager' }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'អាសយដ្ឋាន / ទូរស័ព្ទ' : 'Address / Phone' }}</th>
            <th class="text-right">{{ t('messages.locations') }}</th>
            <th class="text-right">{{ t('messages.stock') }}</th>
            <th class="text-right">{{ t('messages.total_value') }}</th>
            <th>{{ t('messages.status') }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($warehouses as $w)
        <tr>
            <td>
                <a href="{{ route('master.warehouses.show', $w->id) }}" class="font-bold text-slate-900 hover:text-sky-600 transition block">
                    {{ $w->name }}
                </a>
                <span class="text-[11px] text-slate-400 font-mono">{{ $w->code }}</span>
            </td>
            <td class="text-slate-700 font-medium">
                {{ $w->manager_name ?: '-' }}
            </td>
            <td class="text-slate-600">
                <div>{{ $w->address ?: '-' }}</div>
                @if($w->phone)
                    <div class="text-[11px] text-slate-400">{{ $w->phone }}</div>
                @endif
            </td>
            <td class="text-right font-bold text-slate-800">{{ $w->location_count ?? 0 }}</td>
            <td class="text-right font-bold text-slate-900">{{ number_format($w->total_stock ?? 0, 0) }}</td>
            <td class="text-right font-bold text-slate-900">{{ format_currency($w->total_value ?? 0) }}</td>
            <td>
                @if(strtoupper($w->status) == 'ACTIVE')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ t('messages.active') }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        {{ t('messages.inactive') }}
                    </span>
                @endif
            </td>
            <td class="text-right space-x-1.5 whitespace-nowrap">
                <a href="{{ route('master.warehouses.show', $w->id) }}" class="btn-action btn-action-view" title="View">
                    <i class="bi bi-eye"></i>
                </a>
                <button onclick="editWarehouse({{ json_encode($w) }})" class="btn-action btn-action-edit" title="Edit">
                    <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" action="{{ route('master.warehouses.destroy', $w->id) }}" onsubmit="return confirm('{{ app()->getLocale() == 'km' ? 'តើអ្នកប្រាកដជាចង់លុបឃ្លាំងនេះ?' : 'Delete this warehouse?' }}');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-action-delete" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
            <x-table-empty :colspan="8" icon="building" :title="app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យឃ្លាំងទេ' : 'No warehouses found.'" />
        @endforelse
    </x-table>
</div>

<!-- Create Modal -->
<div id="create_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
            <h3 class="text-base font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'បន្ថែមឃ្លាំងថ្មី' : 'Add New Warehouse' }}</h3>
            <button onclick="document.getElementById('create_modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('master.warehouses.store') }}" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">{{ t('messages.name') }} *</label>
                    <input type="text" name="name" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="e.g. Central Warehouse" required>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">{{ t('messages.code') }} *</label>
                    <input type="text" name="code" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="e.g. WH-01" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">{{ app()->getLocale() == 'km' ? 'អ្នកគ្រប់គ្រង' : 'Manager' }}</label>
                    <select name="manager_id" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសអ្នកគ្រប់គ្រង --' : '-- Select Manager --' }}</option>
                        @foreach($managers as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">{{ app()->getLocale() == 'km' ? 'លេខទូរស័ព្ទ' : 'Phone' }}</label>
                    <input type="text" name="phone" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="+855 ...">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">{{ app()->getLocale() == 'km' ? 'អាសយដ្ឋាន' : 'Address' }}</label>
                <input type="text" name="address" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="Street / Zone">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">{{ app()->getLocale() == 'km' ? 'ការពិពណ៌នា' : 'Description' }}</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none"></textarea>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">{{ t('messages.status') }}</label>
                <select name="status" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                    <option value="ACTIVE">{{ t('messages.active') }}</option>
                    <option value="INACTIVE">{{ t('messages.inactive') }}</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('create_modal').classList.add('hidden')" class="px-4 py-2 border rounded-xl text-gray-600 hover:bg-gray-50">{{ t('messages.cancel') }}</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold">{{ t('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
            <h3 class="text-base font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'កែប្រែឃ្លាំង' : 'Edit Warehouse' }}</h3>
            <button onclick="document.getElementById('edit_modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="edit_form" method="POST" action="" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">{{ t('messages.name') }} *</label>
                    <input type="text" name="name" id="edit_name" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none" required>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">{{ t('messages.code') }} *</label>
                    <input type="text" name="code" id="edit_code" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">{{ app()->getLocale() == 'km' ? 'អ្នកគ្រប់គ្រង' : 'Manager' }}</label>
                    <select name="manager_id" id="edit_manager_id" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសអ្នកគ្រប់គ្រង --' : '-- Select Manager --' }}</option>
                        @foreach($managers as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">{{ app()->getLocale() == 'km' ? 'លេខទូរស័ព្ទ' : 'Phone' }}</label>
                    <input type="text" name="phone" id="edit_phone" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">{{ app()->getLocale() == 'km' ? 'អាសយដ្ឋាន' : 'Address' }}</label>
                <input type="text" name="address" id="edit_address" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">{{ app()->getLocale() == 'km' ? 'ការពិពណ៌នា' : 'Description' }}</label>
                <textarea name="description" id="edit_description" rows="2" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none"></textarea>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 mb-1">{{ t('messages.status') }}</label>
                <select name="status" id="edit_status" class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                    <option value="ACTIVE">{{ t('messages.active') }}</option>
                    <option value="INACTIVE">{{ t('messages.inactive') }}</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('edit_modal').classList.add('hidden')" class="px-4 py-2 border rounded-xl text-gray-600 hover:bg-gray-50">{{ t('messages.cancel') }}</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold">{{ t('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editWarehouse(w) {
    document.getElementById('edit_form').action = `/master/warehouses/${w.id}`;
    document.getElementById('edit_name').value = w.name;
    document.getElementById('edit_code').value = w.code;
    document.getElementById('edit_manager_id').value = w.manager_id || '';
    document.getElementById('edit_address').value = w.address || '';
    document.getElementById('edit_phone').value = w.phone || '';
    document.getElementById('edit_description').value = w.description || '';
    document.getElementById('edit_status').value = (w.status || 'ACTIVE').toUpperCase();
    document.getElementById('edit_modal').classList.remove('hidden');
}
</script>
@endpush
@endsection
