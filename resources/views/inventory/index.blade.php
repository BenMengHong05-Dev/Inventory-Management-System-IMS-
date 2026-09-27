@extends('layouts.app')

@section('title', t('messages.inventory'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('messages.inventory') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'បរិមាណស្តុកតាមពេលវេលាជាក់ស្តែង តាមទំនិញ និងឃ្លាំង/ទីតាំង' : 'Real-time stock quantities by item and warehouse/location' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('reports.inventory', ['export' => 'csv']) }}" class="btn-secondary">
                <i class="bi bi-download mr-1.5"></i>
                {{ t('messages.export') }} CSV
            </a>
            <a href="{{ route('stock-in.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('messages.stock_in') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('inventory.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search }}" placeholder="{{ t('messages.search') }}..." wrapperClass="!mb-0" icon="bi bi-search" />
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
                <x-select name="category_id" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.categories') }}</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ $categoryId == $c->id ? 'selected' : '' }}>{{ localized_name($c, 'name') }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full sm:w-40">
                <x-select name="stock_status" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.status') }}</option>
                    <option value="NORMAL" {{ $stockStatus === 'NORMAL' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ស្តុកធម្មតា' : 'Normal Stock' }}</option>
                    <option value="LOW" {{ $stockStatus === 'LOW' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ជិតអស់ស្តុក' : 'Low Stock' }}</option>
                    <option value="OUT" {{ $stockStatus === 'OUT' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'អស់ពីស្តុក' : 'Out of Stock' }}</option>
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('messages.filter') }}
            </x-button>
            <a href="{{ route('inventory.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$inventories">
        <x-slot name="head">
            <th>{{ t('messages.items') }} & SKU</th>
            <th>{{ t('messages.warehouses') }} & {{ t('messages.locations') }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'មានជាក់ស្តែង' : 'On Hand' }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'បានកក់ទុក' : 'Reserved' }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'អាចប្រើបាន' : 'Available' }}</th>
            <th class="text-right">{{ t('messages.unit_cost') }}</th>
            <th class="text-right">{{ t('messages.total_value') }}</th>
            <th>{{ t('messages.status') }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($inventories as $inv)
            <tr>
                <td>
                    <a href="{{ route('items.show', $inv->item_id) }}" class="font-bold text-slate-900 hover:text-sky-600 block transition">
                        {{ localized_name($inv, 'item_name') }}
                    </a>
                    <span class="text-[11px] text-slate-400 font-mono">{{ $inv->item_code }} &bull; {{ localized_name($inv, 'category_name') }}</span>
                </td>
                <td>
                    <span class="font-semibold text-slate-800 block">{{ $inv->warehouse_name }}</span>
                    <span class="text-[11px] text-slate-400 font-mono">{{ $inv->location_code ?? (app()->getLocale() == 'km' ? 'តំបន់ទូទៅ' : 'Main Area') }}</span>
                </td>
                <td class="text-right font-bold text-slate-900">
                    {{ number_format($inv->quantity, 0) }} <span class="text-[11px] text-slate-400 font-normal">/ {{ localized_name($inv, 'unit_name') }}</span>
                </td>
                <td class="text-right text-slate-500 font-mono text-xs">
                    {{ number_format($inv->reserved_quantity, 0) }}
                </td>
                <td class="text-right font-bold text-sm {{ $inv->available_quantity <= $inv->reorder_level ? 'text-amber-600' : 'text-emerald-600' }}">
                    {{ number_format($inv->available_quantity, 0) }}
                </td>
                <td class="text-right text-slate-600 font-medium">{{ format_currency($inv->unit_cost) }}</td>
                <td class="text-right font-bold text-slate-900">{{ format_currency($inv->total_value) }}</td>
                <td>
                    @if($inv->quantity <= 0)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            {{ app()->getLocale() == 'km' ? 'អស់ពីស្តុក' : 'Out of Stock' }}
                        </span>
                    @elseif($inv->quantity <= $inv->reorder_level)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            {{ app()->getLocale() == 'km' ? 'ជិតអស់ស្តុក' : 'Low Stock' }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ app()->getLocale() == 'km' ? 'ល្អគ្រប់គ្រាន់' : 'Optimal' }}
                        </span>
                    @endif
                </td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('stock-adjustments.create', ['item_id' => $inv->item_id, 'warehouse_id' => $inv->warehouse_id]) }}"
                       class="btn-action btn-action-edit" title="{{ t('messages.stock_adjustments') }}">
                        <i class="bi bi-sliders"></i>
                    </a>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="9" icon="boxes" :title="app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាស្តុកនៅឡើយទេ' : 'No Inventory Records Found'" :description="app()->getLocale() == 'km' ? 'កត់ត្រាទំនិញចូលឃ្លាំងដំបូង ដើម្បីចាប់ផ្តើមតាមដានស្តុកជាក់ស្តែង' : 'Stock levels will appear here once you record incoming goods or opening stock.'" :actionUrl="route('stock-in.create')" :actionLabel="t('messages.stock_in')" />
        @endforelse
    </x-table>
</div>
@endsection
