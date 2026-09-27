@extends('layouts.app')

@section('title', t('stock_adjustments') . ' - IMS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('stock_adjustments') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'កែតម្រូវចំនួនស្តុក និងកត់ត្រាភាពមិនស៊ីគ្នារវាងប្រព័ន្ធ និងស្តុកជាក់ស្តែង' : 'Adjust stock quantities and reconcile discrepancies with actual physical counts' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('stock-adjustments.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('messages.create') ?? t('add_new') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('stock-adjustments.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ t('search') }}..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-52">
                <x-select name="warehouse_id" wrapperClass="!mb-0">
                    <option value="">{{ t('all') }} {{ t('warehouses') }}</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ ($warehouseId ?? '') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full sm:w-40">
                <x-select name="type" wrapperClass="!mb-0">
                    <option value="">{{ t('all') }} {{ t('type') }}</option>
                    <option value="INCREASE" {{ ($type ?? '') === 'INCREASE' ? 'selected' : '' }}>{{ t('increase') ?? 'Increase (+)' }}</option>
                    <option value="DECREASE" {{ ($type ?? '') === 'DECREASE' ? 'selected' : '' }}>{{ t('decrease') ?? 'Decrease (-)' }}</option>
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('filter') }}
            </x-button>
            <a href="{{ route('stock-adjustments.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$adjustments">
        <x-slot name="head">
            <th>{{ t('reference_number') }}</th>
            <th>{{ t('date') }}</th>
            <th>{{ t('items') }}</th>
            <th>{{ t('warehouses') }}</th>
            <th>{{ t('type') }}</th>
            <th class="text-right">{{ t('system_qty') ?? 'System Qty' }}</th>
            <th class="text-right">{{ t('actual_qty') ?? 'Actual Qty' }}</th>
            <th class="text-right">{{ t('difference') ?? 'Difference' }}</th>
            <th>{{ t('notes') }}</th>
            <th>{{ t('created_by') }}</th>
        </x-slot>

        @forelse($adjustments as $adj)
            <tr>
                <td class="font-mono text-xs font-bold text-amber-600">{{ $adj->adjustment_number }}</td>
                <td class="text-xs text-slate-500 whitespace-nowrap">{{ $adj->date }}</td>
                <td>
                    <a href="{{ route('items.show', $adj->item_id) }}" class="font-bold text-slate-800 hover:text-sky-600 block text-xs">
                        {{ localized_name((object)['name_en' => $adj->item_name, 'name_km' => $adj->item_name_km ?? ''], 'name') }}
                    </a>
                    <span class="text-[11px] text-slate-400 font-mono">{{ $adj->item_code }}</span>
                </td>
                <td class="text-xs text-slate-700 font-medium">{{ $adj->warehouse_name }}</td>
                <td>
                    @if(strtoupper($adj->adjustment_type) === 'INCREASE')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <i class="bi bi-arrow-up text-[10px]"></i>
                            {{ t('increase') ?? 'INCREASE' }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                            <i class="bi bi-arrow-down text-[10px]"></i>
                            {{ t('decrease') ?? 'DECREASE' }}
                        </span>
                    @endif
                </td>
                <td class="text-right text-xs text-slate-600 font-mono">{{ number_format($adj->system_quantity, 0) }}</td>
                <td class="text-right text-xs font-bold text-slate-800 font-mono">{{ number_format($adj->actual_quantity, 0) }}</td>
                <td class="text-right text-xs font-bold font-mono {{ strtoupper($adj->adjustment_type) === 'INCREASE' ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ strtoupper($adj->adjustment_type) === 'INCREASE' ? '+' : '-' }}{{ number_format($adj->difference, 0) }}
                </td>
                <td class="text-xs text-slate-600 max-w-xs truncate">{{ $adj->reason ?: '-' }}</td>
                <td class="text-xs text-slate-500">{{ $adj->creator_name }}</td>
            </tr>
        @empty
            <x-table-empty :colspan="10" icon="sliders" :title="app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាកែតម្រូវស្តុកទេ' : 'No Stock Adjustments Found'" :description="app()->getLocale() == 'km' ? 'បង្កើតការកែតម្រូវស្តុកដំបូងរបស់អ្នកដើម្បីដោះស្រាយភាពខុសគ្នានៃចំនួនស្តុក' : 'Reconcile physical inventory discrepancies with ease.'" :actionUrl="route('stock-adjustments.create')" :actionLabel="t('messages.create') ?? t('add_new')" />
        @endforelse
    </x-table>
</div>
@endsection
