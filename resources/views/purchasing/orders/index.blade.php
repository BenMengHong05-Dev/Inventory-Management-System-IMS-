@extends('layouts.app')

@section('title', t('messages.purchase_orders'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('messages.purchase_orders') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'គ្រប់គ្រងការបញ្ជាទិញពីអ្នកផ្គត់ផ្គង់ លក្ខខណ្ឌ និងស្ថានភាពទទួលទំនិញ' : 'Manage supplier purchase orders, terms, line totals, and receiving statuses' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('purchase-orders.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('messages.create') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('purchase-orders.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search }}" placeholder="{{ t('messages.search') }} #..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-48">
                <x-select name="supplier_id" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.suppliers') }}</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $supplierId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full sm:w-40">
                <x-select name="status" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.status') }}</option>
                    <option value="ORDERED" {{ $status === 'ORDERED' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បានបញ្ជាទិញ' : 'Ordered' }}</option>
                    <option value="PARTIALLY_RECEIVED" {{ $status === 'PARTIALLY_RECEIVED' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បានទទួលខ្លះ' : 'Partially Received' }}</option>
                    <option value="RECEIVED" {{ $status === 'RECEIVED' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បានទទួលពេញលេញ' : 'Fully Received' }}</option>
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('messages.filter') }}
            </x-button>
            <a href="{{ route('purchase-orders.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$purchaseOrders">
        <x-slot name="head">
            <th>{{ t('messages.purchase_orders') }} #</th>
            <th>{{ t('messages.date') }}</th>
            <th>{{ t('messages.suppliers') }}</th>
            <th>{{ t('messages.warehouse') }}</th>
            <th class="text-right">{{ t('messages.items') }}</th>
            <th class="text-right">{{ t('messages.total') }}</th>
            <th>{{ t('messages.status') }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($purchaseOrders as $po)
            <tr>
                <td class="font-mono font-bold text-slate-900">
                    <a href="{{ route('purchase-orders.show', $po->id) }}" class="text-sky-600 hover:text-sky-700 transition">
                        {{ $po->po_number }}
                    </a>
                    @if($po->request_number)
                        <span class="block text-[10px] text-slate-400 font-sans">PR: {{ $po->request_number }}</span>
                    @endif
                </td>
                <td class="text-slate-600 whitespace-nowrap">{{ $po->order_date }}</td>
                <td class="text-slate-800 font-semibold">{{ $po->supplier_name }}</td>
                <td class="text-slate-700 font-medium">{{ $po->warehouse_name }}</td>
                <td class="text-right font-bold text-slate-800">{{ $po->total_items }}</td>
                <td class="text-right font-bold text-slate-900">{{ format_currency($po->total) }}</td>
                <td>
                    @if($po->status === 'RECEIVED')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $po->status }}
                        </span>
                    @elseif($po->status === 'PARTIALLY_RECEIVED')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-700 border border-sky-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                            {{ app()->getLocale() == 'km' ? 'បានទទួលខ្លះ' : 'Partially Received' }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $po->status }}
                        </span>
                    @endif
                </td>
                <td class="text-right whitespace-nowrap space-x-1.5">
                    <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn-action btn-action-view" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                    @if($po->status !== 'RECEIVED')
                        <a href="{{ route('goods-receipts.create', ['po_id' => $po->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-500/10 text-emerald-700 hover:bg-emerald-500/20 border border-emerald-500/20 rounded-xl text-xs font-semibold transition" title="Receive Goods">
                            <i class="bi bi-box-arrow-in-down"></i>
                            <span>{{ app()->getLocale() == 'km' ? 'ទទួល' : 'Receive' }}</span>
                        </a>
                    @endif
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="8" icon="bag-check" :title="app()->getLocale() == 'km' ? 'មិនមានការបញ្ជាទិញទេ' : 'No Purchase Orders Found'" :description="app()->getLocale() == 'km' ? 'បង្កើតការបញ្ជាទិញថ្មីទៅកាន់អ្នកផ្គត់ផ្គង់' : 'Create purchase orders to procure stock from vendors.'" :actionUrl="route('purchase-orders.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
