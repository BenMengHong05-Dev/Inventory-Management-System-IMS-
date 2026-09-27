@extends('layouts.app')

@section('title', t('messages.stock_in'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('messages.stock_in') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'កត់ត្រាទំនិញចូល ទទួលពីអ្នកផ្គត់ផ្គង់ លេខបាច់/កាលបរិច្ឆេទផុតកំណត់ និងបង្កើនស្តុក' : 'Record incoming shipments, suppliers, batch/expiry numbers, and increase stock' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('stock-in.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('messages.create') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar (No white card background, no shadow, search on left, filters on right) -->
    <form action="{{ route('stock-in.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search }}" placeholder="{{ t('messages.search') }} #..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-48">
                <x-select name="warehouse_id" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.warehouses') }}</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ $warehouseId == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full sm:w-40">
                <x-select name="status" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.status') }}</option>
                    <option value="APPROVED" {{ $status === 'APPROVED' ? 'selected' : '' }}>{{ t('messages.approved') }}</option>
                    <option value="PENDING" {{ $status === 'PENDING' ? 'selected' : '' }}>{{ t('messages.pending') }}</option>
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('messages.filter') }}
            </x-button>
            <a href="{{ route('stock-in.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Stock In Table (Modern Clear Glass) -->
    <x-table :pagination="$stockIns">
        <x-slot name="head">
            <th>{{ t('messages.stock_in') }} #</th>
            <th>{{ t('messages.date') }}</th>
            <th>{{ t('messages.suppliers') }}</th>
            <th>{{ t('messages.warehouse') }}</th>
            <th class="text-right">{{ t('messages.items') }}</th>
            <th class="text-right">{{ t('messages.total') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'អ្នកទទួល' : 'Received By' }}</th>
            <th>{{ t('messages.status') }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($stockIns as $si)
            <tr>
                <td class="font-bold font-mono text-slate-900">
                    <a href="{{ route('stock-in.show', $si->id) }}" class="text-sky-600 hover:text-sky-700 transition">
                        {{ $si->stock_in_number }}
                    </a>
                    @if($si->reference_number)
                        <span class="block text-[10px] text-slate-400 font-sans">Ref: {{ $si->reference_number }}</span>
                    @endif
                </td>
                <td class="text-slate-600 whitespace-nowrap">{{ $si->date }}</td>
                <td class="text-slate-800 font-medium">{{ $si->supplier_name ?? (app()->getLocale() == 'km' ? 'ផ្ទៃក្នុង / ស្តុកដើមគ្រា' : 'Internal / Opening') }}</td>
                <td class="text-slate-700 font-medium">{{ $si->warehouse_name }}</td>
                <td class="text-right font-bold text-slate-800">{{ $si->total_items }}</td>
                <td class="text-right font-bold text-slate-900">{{ format_currency($si->total_amount) }}</td>
                <td class="text-slate-600">{{ $si->receiver_name }}</td>
                <td>
                    @if(strtoupper($si->status) === 'APPROVED')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $si->status }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $si->status }}
                        </span>
                    @endif
                </td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('stock-in.show', $si->id) }}" class="btn-action btn-action-view" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="9" icon="package-plus" :title="app()->getLocale() == 'km' ? 'មិនទាន់មានកំណត់ត្រាទំនិញចូលនៅឡើយទេ' : 'No Stock In Entries Yet'" :description="app()->getLocale() == 'km' ? 'កត់ត្រាទំនិញចូលថ្មីពីអ្នកផ្គត់ផ្គង់ ឬការផ្ទេរចូលឃ្លាំង' : 'Create a new stock in transaction to add inventory quantities.'" :actionUrl="route('stock-in.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
