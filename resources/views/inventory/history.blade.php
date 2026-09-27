@extends('layouts.app')

@section('title', t('inventory_history') ?? 'Inventory Transaction History')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('inventory_history') ?? 'Inventory Transaction History' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'កំណត់ត្រាប្រវត្តិបន្លាស់ប្តូរស្តុកលម្អិតទាំងអស់រវាងឃ្លាំង និងប្រតិបត្តិការ' : 'Immutable ledger recording every single stock movement across all warehouses' }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <form action="{{ route('inventory.history') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Item Selector on Left -->
        <div class="w-full lg:w-72">
            <x-select name="item_id" wrapperClass="!mb-0">
                <option value="">{{ t('messages.all') }} {{ t('messages.items') }}</option>
                @foreach($items as $i)
                    <option value="{{ $i->id }}" {{ $itemId == $i->id ? 'selected' : '' }}>{{ $i->code }} - {{ $i->name_en }}</option>
                @endforeach
            </x-select>
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-44">
                <x-select name="warehouse_id" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.warehouses') }}</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ $warehouseId == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full sm:w-44">
                <x-select name="type" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('type') }}</option>
                    <option value="STOCK_IN" {{ $type === 'STOCK_IN' ? 'selected' : '' }}>STOCK_IN</option>
                    <option value="STOCK_OUT" {{ $type === 'STOCK_OUT' ? 'selected' : '' }}>STOCK_OUT</option>
                    <option value="TRANSFER_IN" {{ $type === 'TRANSFER_IN' ? 'selected' : '' }}>TRANSFER_IN</option>
                    <option value="TRANSFER_OUT" {{ $type === 'TRANSFER_OUT' ? 'selected' : '' }}>TRANSFER_OUT</option>
                    <option value="ADJUSTMENT_IN" {{ $type === 'ADJUSTMENT_IN' ? 'selected' : '' }}>ADJUSTMENT_IN</option>
                    <option value="ADJUSTMENT_OUT" {{ $type === 'ADJUSTMENT_OUT' ? 'selected' : '' }}>ADJUSTMENT_OUT</option>
                    <option value="STOCK_TAKE_IN" {{ $type === 'STOCK_TAKE_IN' ? 'selected' : '' }}>STOCK_TAKE_IN</option>
                    <option value="STOCK_TAKE_OUT" {{ $type === 'STOCK_TAKE_OUT' ? 'selected' : '' }}>STOCK_TAKE_OUT</option>
                    <option value="RETURN_IN" {{ $type === 'RETURN_IN' ? 'selected' : '' }}>RETURN_IN</option>
                </x-select>
            </div>
            <div class="w-36">
                <x-input type="date" name="start_date" value="{{ $startDate }}" placeholder="From Date" wrapperClass="!mb-0" />
            </div>
            <div class="w-36">
                <x-input type="date" name="end_date" value="{{ $endDate }}" placeholder="To Date" wrapperClass="!mb-0" />
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('messages.filter') }}
            </x-button>
            <a href="{{ route('inventory.history') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$transactions">
        <x-slot name="head">
            <th>{{ t('messages.date') }}</th>
            <th>{{ t('type') }}</th>
            <th>{{ t('messages.items') }} & {{ t('messages.code') }}</th>
            <th>{{ t('messages.warehouse') }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'ចូល' : 'In' }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'ចេញ' : 'Out' }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'សមតុល្យបន្ទាប់' : 'Balance After' }}</th>
            <th class="text-right">{{ t('messages.unit_cost') }}</th>
            <th>{{ t('reference_number') }}</th>
            <th>{{ t('created_by') }}</th>
        </x-slot>

        @forelse($transactions as $tx)
            <tr>
                <td class="text-slate-500 whitespace-nowrap">
                    <span class="font-medium text-slate-800">{{ $tx->date }}</span>
                    <span class="block text-[10px] text-slate-400 font-mono">{{ substr($tx->created_at, 11, 8) }}</span>
                </td>
                <td class="whitespace-nowrap">
                    @if(str_contains($tx->transaction_type, 'IN'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $tx->transaction_type }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            {{ $tx->transaction_type }}
                        </span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('items.show', $tx->item_id) }}" class="font-bold text-slate-900 hover:text-sky-600 block transition">
                        {{ $tx->item_name }}
                    </a>
                    <span class="text-[11px] text-slate-400 font-mono">{{ $tx->item_code }}</span>
                </td>
                <td>
                    <span class="font-medium text-slate-800 block">{{ $tx->warehouse_name }}</span>
                    <span class="text-[11px] text-slate-400 font-mono">{{ $tx->location_code ?? 'Main' }}</span>
                </td>
                <td class="text-right font-bold font-mono text-emerald-600">
                    {{ $tx->quantity_in > 0 ? '+' . number_format($tx->quantity_in, 0) : '-' }}
                </td>
                <td class="text-right font-bold font-mono text-rose-600">
                    {{ $tx->quantity_out > 0 ? '-' . number_format($tx->quantity_out, 0) : '-' }}
                </td>
                <td class="text-right font-bold font-mono text-sm text-slate-900">
                    {{ number_format($tx->balance_after, 0) }}
                </td>
                <td class="text-right text-slate-600 font-medium">{{ format_currency($tx->unit_cost) }}</td>
                <td>
                    <span class="text-slate-800 block font-medium text-xs">{{ $tx->reference_type ?? '-' }}</span>
                    <span class="text-[11px] text-slate-400 truncate max-w-[130px] block" title="{{ $tx->note }}">{{ $tx->note ?? '' }}</span>
                </td>
                <td class="text-slate-500 text-xs whitespace-nowrap">{{ $tx->user_name ?? 'System' }}</td>
            </tr>
        @empty
            <x-table-empty :colspan="10" icon="clock-history" :title="app()->getLocale() == 'km' ? 'មិនមានប្រវត្តិប្រតិបត្តិការស្តុកនៅឡើយទេ' : 'No Inventory Transactions Found'" :description="app()->getLocale() == 'km' ? 'រាល់ប្រតិបត្តិការស្តុកចូល ស្តុកចេញ និងផ្ទេរស្តុក នឹងត្រូវកត់ត្រានៅទីនេះ' : 'All transactions and item movements will be recorded here automatically.'" />
        @endforelse
    </x-table>
</div>
@endsection
