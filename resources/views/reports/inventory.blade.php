@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'របាយការណ៍វាយតម្លៃស្តុក' : 'Inventory Valuation Report')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'របាយការណ៍វាយតម្លៃស្តុក' : 'Inventory Valuation Report' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'បរិមាណស្តុកតាមពេលវេលាជាក់ស្តែង និងការវាយតម្លៃហិរញ្ញវត្ថុតាមឃ្លាំង និងប្រភេទទំនិញ' : 'Real-time stock quantities and financial valuation by warehouse and category' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <i class="bi bi-grid mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'មជ្ឈមណ្ឌល' : 'Hub' }}
            </a>
            <a href="{{ route('reports.inventory.export', request()->query()) }}" class="btn-primary">
                <i class="bi bi-download mr-1.5"></i>
                {{ t('messages.export') }} CSV
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('reports.inventory') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <div></div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-48">
                <x-select name="category_id" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.categories') }}</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ $categoryId == $c->id ? 'selected' : '' }}>{{ localized_name($c, 'name') }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full sm:w-48">
                <x-select name="warehouse_id" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.warehouses') }}</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ $warehouseId == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('messages.filter') }}
            </x-button>
            <a href="{{ route('reports.inventory') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$report">
        <x-slot name="head">
            <th>SKU & {{ t('messages.code') }}</th>
            <th>{{ t('messages.items') }}</th>
            <th>{{ t('messages.categories') }}</th>
            <th>{{ t('messages.warehouse') }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'មានជាក់ស្តែង' : 'Qty On Hand' }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'បានកក់ទុក' : 'Reserved' }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'អាចប្រើបាន' : 'Available' }}</th>
            <th class="text-right">{{ t('messages.unit_cost') }}</th>
            <th class="text-right">{{ t('messages.total_value') }}</th>
        </x-slot>

        @php $sumValuation = 0; $sumQty = 0; @endphp
        @forelse($report as $r)
            @php 
                $sumValuation += $r->total_value; 
                $sumQty += $r->quantity;
            @endphp
            <tr>
                <td class="font-mono font-bold text-slate-900 text-xs">{{ $r->item_code ?? $r->sku }}</td>
                <td class="font-semibold text-slate-800 text-xs">{{ localized_name($r, 'item_name') }}</td>
                <td class="text-slate-600 text-xs">{{ localized_name($r, 'category_name') }}</td>
                <td class="text-slate-600 text-xs">{{ $r->warehouse_name }}</td>
                <td class="text-right font-mono font-medium text-slate-800">{{ number_format($r->quantity, 2) }}</td>
                <td class="text-right font-mono text-amber-600">{{ number_format($r->reserved_quantity, 2) }}</td>
                <td class="text-right font-mono font-bold text-emerald-600">{{ number_format($r->available_quantity ?? $r->available_qty, 2) }}</td>
                <td class="text-right font-mono text-slate-600">{{ format_currency($r->unit_cost ?? $r->cost_price) }}</td>
                <td class="text-right font-mono font-bold text-slate-900">{{ format_currency($r->total_value) }}</td>
            </tr>
        @empty
            <x-table-empty :colspan="9" icon="bar-chart-line" :title="app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យស្តុកទេ' : 'No Inventory Items Found'" :description="app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យស្តុកត្រូវនឹងលក្ខខណ្ឌត្រងទេ' : 'No inventory items match the current filter criteria.'" />
        @endforelse

        @if(count($report) > 0)
            <tfoot class="border-t border-slate-200/80 bg-slate-50/50 font-semibold text-xs">
                <tr>
                    <td colspan="4" class="py-3 px-4 text-right font-bold text-slate-700">{{ app()->getLocale() == 'km' ? 'សរុបរួម:' : 'Report Summary:' }}</td>
                    <td class="py-3 px-4 text-right font-mono text-slate-800 font-bold">{{ number_format($sumQty, 2) }}</td>
                    <td colspan="3"></td>
                    <td class="py-3 px-4 text-right font-mono text-emerald-700 font-bold text-sm">{{ format_currency($sumValuation) }}</td>
                </tr>
            </tfoot>
        @endif
    </x-table>
</div>
@endsection
