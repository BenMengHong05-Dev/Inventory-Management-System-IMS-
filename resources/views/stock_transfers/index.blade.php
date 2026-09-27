@extends('layouts.app')

@section('title', t('messages.stock_transfers'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('messages.stock_transfers') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'ផ្ទេរទំនិញរវាងឃ្លាំង និងតាមដានទំនិញកំពុងដឹកជញ្ជូន' : 'Transfer inventory across warehouses and track in-transit shipments' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('stock-transfers.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('messages.create') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('stock-transfers.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search }}" placeholder="{{ t('messages.search') }} #..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-48">
                <x-select name="status" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.status') }}</option>
                    <option value="PENDING" {{ $status === 'PENDING' ? 'selected' : '' }}>{{ t('messages.pending') }}</option>
                    <option value="IN_TRANSIT" {{ $status === 'IN_TRANSIT' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'កំពុងដឹកជញ្ជូន' : 'In Transit' }}</option>
                    <option value="COMPLETED" {{ $status === 'COMPLETED' ? 'selected' : '' }}>{{ t('messages.completed') }}</option>
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('messages.filter') }}
            </x-button>
            <a href="{{ route('stock-transfers.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Transfers Table (Modern Clear Glass) -->
    <x-table :pagination="$transfers">
        <x-slot name="head">
            <th>{{ t('messages.stock_transfers') }} #</th>
            <th>{{ t('messages.date') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'ពីឃ្លាំង' : 'From Warehouse' }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'ទៅឃ្លាំង' : 'To Warehouse' }}</th>
            <th class="text-right">{{ t('messages.items') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'ស្នើដោយ' : 'Requested By' }}</th>
            <th>{{ t('messages.status') }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($transfers as $trf)
            <tr>
                <td class="font-bold font-mono text-slate-900">
                    <a href="{{ route('stock-transfers.show', $trf->id) }}" class="text-sky-600 hover:text-sky-700 transition">
                        {{ $trf->transfer_number }}
                    </a>
                </td>
                <td class="text-slate-600 whitespace-nowrap">{{ $trf->transfer_date }}</td>
                <td class="text-slate-800 font-medium">{{ $trf->from_warehouse_name }}</td>
                <td class="text-slate-900 font-bold flex items-center gap-1.5 py-3">
                    <i class="bi bi-arrow-right text-sky-500"></i>
                    <span>{{ $trf->to_warehouse_name }}</span>
                </td>
                <td class="text-right font-bold text-slate-800">{{ $trf->total_items }}</td>
                <td class="text-slate-600">{{ $trf->requester_name }}</td>
                <td>
                    @if($trf->status === 'COMPLETED')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $trf->status }}
                        </span>
                    @elseif($trf->status === 'IN_TRANSIT')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-700 border border-sky-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                            {{ app()->getLocale() == 'km' ? 'កំពុងដឹក' : 'In Transit' }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $trf->status }}
                        </span>
                    @endif
                </td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('stock-transfers.show', $trf->id) }}" class="btn-action btn-action-view" title="View & Process">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="8" icon="arrow-left-right" :title="app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាផ្ទេរស្តុកទេ' : 'No Stock Transfers Recorded'" :description="app()->getLocale() == 'km' ? 'ផ្ទេរទំនិញពីឃ្លាំងមួយទៅកាន់ឃ្លាំងមួយទៀតយ៉ាងងាយស្រួល' : 'Transfer inventory between storage facilities seamlessly.'" :actionUrl="route('stock-transfers.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
