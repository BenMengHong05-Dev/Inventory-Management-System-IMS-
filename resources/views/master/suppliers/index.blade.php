@extends('layouts.app')

@section('title', t('messages.suppliers'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('messages.suppliers') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'គ្រប់គ្រងបញ្ជីឈ្មោះអ្នកផ្គត់ផ្គង់ ព័ត៌មានទំនាក់ទំនង លេខសម្គាល់សារពើពន្ធ និងលក្ខខណ្ឌ' : 'Manage supplier directories, contact details, payment terms, and order history' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('master.suppliers.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i> {{ t('messages.create') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar (No white card background, no shadow, search on left, filters on right) -->
    <form method="GET" action="{{ route('master.suppliers.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ t('messages.search') }}..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-48">
                <x-select name="status" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.status') }}</option>
                    <option value="ACTIVE" {{ ($status ?? '') === 'ACTIVE' ? 'selected' : '' }}>{{ t('messages.active') }}</option>
                    <option value="INACTIVE" {{ ($status ?? '') === 'INACTIVE' ? 'selected' : '' }}>{{ t('messages.inactive') }}</option>
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('messages.filter') }}
            </x-button>
            @if(!empty($search) || !empty($status))
                <a href="{{ route('master.suppliers.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Suppliers Table -->
    <x-table :pagination="$suppliers">
        <x-slot name="head">
            <th>{{ t('messages.code') }} & {{ t('messages.suppliers') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'អ្នកទំនាក់ទំនង' : 'Contact Person' }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'ទូរស័ព្ទ & អ៊ីមែល' : 'Phone & Email' }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'អាសយដ្ឋាន' : 'Address' }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'លេខសារពើពន្ធ' : 'Tax #' }}</th>
            <th>{{ t('messages.status') }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($suppliers as $s)
        <tr>
            <td>
                <a href="{{ route('master.suppliers.show', $s->id) }}" class="font-bold text-gray-900 hover:text-emerald-600 block">
                    {{ $s->name }}
                </a>
                <span class="text-[10px] text-gray-400 font-mono">{{ $s->code }}</span>
            </td>
            <td class="font-medium text-gray-700">{{ $s->contact_person ?: '-' }}</td>
            <td class="text-gray-600">
                @if($s->phone)
                    <div>{{ $s->phone }}</div>
                @endif
                @if($s->email)
                    <div class="text-[10px] text-gray-400">{{ $s->email }}</div>
                @endif
                @if(!$s->phone && !$s->email)
                    -
                @endif
            </td>
            <td class="text-gray-600 truncate max-w-xs">{{ $s->address ?: '-' }}</td>
            <td class="font-mono text-gray-600">{{ $s->tax_number ?: '-' }}</td>
            <td>
                @if(strtoupper($s->status) == 'ACTIVE')
                    <span class="badge bg-emerald-100 text-emerald-800">{{ t('messages.active') }}</span>
                @else
                    <span class="badge bg-slate-100 text-slate-700">{{ t('messages.inactive') }}</span>
                @endif
            </td>
            <td class="text-right whitespace-nowrap space-x-1">
                <a href="{{ route('master.suppliers.show', $s->id) }}" class="btn-secondary !p-1.5" title="View">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('master.suppliers.edit', $s->id) }}" class="btn-secondary !p-1.5 text-blue-600" title="Edit">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{ route('master.suppliers.destroy', $s->id) }}" onsubmit="return confirm('{{ app()->getLocale() == 'km' ? 'តើអ្នកប្រាកដជាចង់លុបអ្នកផ្គត់ផ្គង់នេះ?' : 'Delete this supplier?' }}');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger !p-1.5" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
            <x-table-empty :colspan="7" icon="building" :title="app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យអ្នកផ្គត់ផ្គង់ទេ' : 'No suppliers found.'" />
        @endforelse
    </x-table>
</div>
@endsection
