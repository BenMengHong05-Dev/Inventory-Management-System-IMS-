@extends('layouts.app')

@section('title', t('messages.low_stock'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                <h1 class="text-xl font-bold text-gray-900">{{ t('messages.low_stock') }}</h1>
            </div>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'មុខទំនិញដែលដល់ ឬក្រោមចំណុចកំណត់បញ្ជាទិញឡើងវិញ ជាមួយបរិមាណដែលបានណែនាំ' : 'Items that are at or below their configured reorder points with suggested order quantities' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('purchase-requests.create') }}" class="btn-primary">
                <i class="bi bi-cart-plus mr-1.5"></i>
                {{ t('messages.purchase_requests') }}
            </a>
        </div>
    </div>

    <!-- Table (Modern Clear Glass) -->
    <x-table>
        <x-slot name="head">
            <th>{{ t('messages.items') }} & {{ t('messages.code') }}</th>
            <th>{{ t('messages.categories') }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'ស្តុកបច្ចុប្បន្ន' : 'Current Stock' }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'កម្រិតបញ្ជាទិញឡើងវិញ' : 'Reorder Level' }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'កម្រិតអប្បបរមា' : 'Minimum Level' }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'បរិមាណណែនាំ' : 'Suggested Reorder' }}</th>
            <th class="text-right">{{ t('messages.unit_cost') }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'តម្លៃប៉ាន់ស្មាន' : 'Est. Reorder Cost' }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($lowStockItems as $row)
            @php
                $reorderCost = $row->suggested_order_qty * $row->default_unit_cost;
            @endphp
            <tr>
                <td>
                    <a href="{{ route('items.show', $row->id) }}" class="font-bold text-slate-900 hover:text-sky-600 block transition">
                        {{ localized_name($row, 'name') }}
                    </a>
                    <span class="text-[11px] text-slate-400 font-mono">{{ $row->code }}</span>
                </td>
                <td class="text-slate-600 font-medium">{{ localized_name($row, 'category_name') }}</td>
                <td class="text-right font-bold text-sm {{ $row->current_stock <= 0 ? 'text-rose-600' : 'text-amber-600' }}">
                    {{ number_format($row->current_stock, 0) }} <span class="text-[11px] text-slate-400 font-normal">/ {{ localized_name($row, 'unit_name') }}</span>
                    @if($row->current_stock <= 0)
                        <span class="block text-[10px] text-rose-600 font-bold uppercase tracking-wider">{{ app()->getLocale() == 'km' ? 'អស់ពីស្តុក' : 'OUT OF STOCK' }}</span>
                    @endif
                </td>
                <td class="text-right font-bold text-slate-700 font-mono">{{ number_format($row->reorder_level, 0) }}</td>
                <td class="text-right text-slate-500 font-mono">{{ number_format($row->min_stock, 0) }}</td>
                <td class="text-right font-bold text-sm text-emerald-600 font-mono">
                    +{{ number_format($row->suggested_order_qty, 0) }}
                </td>
                <td class="text-right text-slate-600 font-medium">{{ format_currency($row->default_unit_cost) }}</td>
                <td class="text-right font-bold text-slate-900">{{ format_currency($reorderCost) }}</td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('purchase-requests.create', ['item_id' => $row->id, 'qty' => $row->suggested_order_qty]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl text-xs font-semibold shadow-sm shadow-emerald-500/20 hover:from-emerald-600 hover:to-teal-700 transition">
                        <i class="bi bi-cart-plus"></i>
                        <span>{{ app()->getLocale() == 'km' ? 'បញ្ជាទិញ' : 'Reorder' }}</span>
                    </a>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="9" icon="check2-circle" :title="app()->getLocale() == 'km' ? 'ស្តុកគ្រប់គ្រាន់ល្អ' : 'Healthy Inventory Levels'" :description="app()->getLocale() == 'km' ? 'គ្រប់មុខទំនិញទាំងអស់សុទ្ធតែមានស្តុកគ្រប់គ្រាន់លើសកម្រិតកំណត់។' : 'All items are currently stocked safely above their reorder thresholds.'" />
        @endforelse
    </x-table>
</div>
@endsection
