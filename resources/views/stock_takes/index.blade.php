@extends('layouts.app')

@section('title', t('stock_takes') . ' - IMS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('stock_takes') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'គ្រប់គ្រងវគ្គរាប់ស្តុកជាក់ស្តែង សវនកម្ម និងផ្ទៀងផ្ទាត់ទំនិញក្នុងឃ្លាំង' : 'Manage physical stock counting sessions, inventory audit, and verification' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('stock-takes.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('messages.create') ?? t('add_new') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('stock-takes.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
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
                <x-select name="status" wrapperClass="!mb-0">
                    <option value="">{{ t('all') }} {{ t('status') }}</option>
                    <option value="DRAFT" {{ ($status ?? '') === 'DRAFT' ? 'selected' : '' }}>{{ t('draft') }}</option>
                    <option value="COUNTING" {{ ($status ?? '') === 'COUNTING' ? 'selected' : '' }}>Counting</option>
                    <option value="REVIEWED" {{ ($status ?? '') === 'REVIEWED' ? 'selected' : '' }}>Reviewed</option>
                    <option value="APPROVED" {{ ($status ?? '') === 'APPROVED' ? 'selected' : '' }}>{{ t('approved') }}</option>
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('filter') }}
            </x-button>
            <a href="{{ route('stock-takes.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$stockTakes">
        <x-slot name="head">
            <th>{{ t('reference_number') }}</th>
            <th>{{ t('date') }}</th>
            <th>{{ t('warehouses') }}</th>
            <th>{{ t('locations') }}</th>
            <th class="text-right">{{ t('items') }}</th>
            <th class="text-right">{{ t('total_variance') ?? 'Variance Value' }}</th>
            <th>{{ t('created_by') }}</th>
            <th>{{ t('status') }}</th>
            <th class="text-right">{{ t('actions') }}</th>
        </x-slot>

        @forelse($stockTakes as $stk)
            <tr>
                <td class="font-mono text-xs font-bold text-teal-600">
                    <a href="{{ route('stock-takes.show', $stk->id) }}" class="text-sky-600 hover:text-sky-700 transition">
                        {{ $stk->stock_take_number }}
                    </a>
                </td>
                <td class="text-xs text-slate-500 whitespace-nowrap">{{ $stk->date }}</td>
                <td class="text-xs font-bold text-slate-800">{{ $stk->warehouse_name }}</td>
                <td class="text-xs text-slate-500 font-mono">{{ $stk->location_code ?? t('all') }}</td>
                <td class="text-right text-xs font-bold text-slate-800">{{ $stk->total_items }}</td>
                <td class="text-right text-xs font-bold font-mono {{ $stk->total_variance_value < 0 ? 'text-rose-600' : ($stk->total_variance_value > 0 ? 'text-emerald-600' : 'text-slate-800') }}">
                    {{ format_currency($stk->total_variance_value) }}
                </td>
                <td class="text-xs text-slate-600">{{ $stk->creator_name }}</td>
                <td>
                    @if($stk->status === 'APPROVED')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ t('approved') }}
                        </span>
                    @elseif($stk->status === 'REVIEWED')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-700 border border-sky-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                            REVIEWED
                        </span>
                    @elseif($stk->status === 'COUNTING')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-500/10 text-purple-700 border border-purple-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                            COUNTING
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $stk->status }}
                        </span>
                    @endif
                </td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('stock-takes.show', $stk->id) }}" class="btn-action btn-action-view" title="{{ t('view') }}">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="9" icon="clipboard-check" :title="app()->getLocale() == 'km' ? 'មិនមានវគ្គរាប់ស្តុកនៅឡើយទេ' : 'No Stock Takes Found'" :description="app()->getLocale() == 'km' ? 'បង្កើតវគ្គរាប់ស្តុកថ្មីដើម្បីត្រួតពិនិត្យ និងកត់ត្រាចំនួនជាក់ស្តែង' : 'Start a physical stock count session to verify stock levels.'" :actionUrl="route('stock-takes.create')" :actionLabel="t('messages.create') ?? t('add_new')" />
        @endforelse
    </x-table>
</div>
@endsection
