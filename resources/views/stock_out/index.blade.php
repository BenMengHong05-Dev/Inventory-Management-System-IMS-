@extends('layouts.app')

@section('title', t('messages.stock_out'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('messages.stock_out') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'បញ្ចេញទំនិញ និងសម្ភារប្រើប្រាស់ទៅកាន់នាយកដ្ឋាន និងបុគ្គលិកដោយកាត់កងស្តុក' : 'Issue items and consumables to departments and employees with stock deduction' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('stock-out.create') }}" class="btn-primary">
                <i class="bi bi-dash-lg mr-1.5"></i>
                {{ t('messages.create') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('stock-out.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
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
            <a href="{{ route('stock-out.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Stock Out Table (Modern Clear Glass) -->
    <x-table :pagination="$stockOuts">
        <x-slot name="head">
            <th>{{ t('messages.stock_out') }} #</th>
            <th>{{ t('messages.date') }}</th>
            <th>{{ t('messages.department') }} / {{ t('messages.employees') }}</th>
            <th>{{ t('messages.warehouse') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'គោលបំណង' : 'Purpose' }}</th>
            <th class="text-right">{{ t('messages.items') }}</th>
            <th class="text-right">{{ t('messages.total') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'អ្នកចេញ' : 'Issued By' }}</th>
            <th>{{ t('messages.status') }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($stockOuts as $so)
            <tr>
                <td class="font-bold font-mono text-slate-900">
                    <a href="{{ route('stock-out.show', $so->id) }}" class="text-sky-600 hover:text-sky-700 transition">
                        {{ $so->stock_out_number }}
                    </a>
                </td>
                <td class="text-slate-600 whitespace-nowrap">{{ $so->date }}</td>
                <td>
                    <span class="font-semibold text-slate-800 block">{{ $so->department_name ?? (app()->getLocale() == 'km' ? 'ទូទៅ' : 'General') }}</span>
                    <span class="text-[11px] text-slate-400">{{ $so->employee_name ?? (app()->getLocale() == 'km' ? 'មិនបានកំណត់អ្នកទទួល' : 'No recipient') }}</span>
                </td>
                <td class="text-slate-700 font-medium">{{ $so->warehouse_name }}</td>
                <td class="text-slate-600 truncate max-w-[140px]">{{ $so->purpose ?? '-' }}</td>
                <td class="text-right font-bold text-slate-800">{{ $so->total_items }}</td>
                <td class="text-right font-bold text-slate-900">{{ format_currency($so->total_value) }}</td>
                <td class="text-slate-600">{{ $so->issuer_name }}</td>
                <td>
                    @if(strtoupper($so->status) === 'APPROVED')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $so->status }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $so->status }}
                        </span>
                    @endif
                </td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('stock-out.show', $so->id) }}" class="btn-action btn-action-view" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="10" icon="package-minus" :title="app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាទំនិញចេញទេ' : 'No Stock Out Requisitions Recorded'" :description="app()->getLocale() == 'km' ? 'ចាប់ផ្តើមកត់ត្រាការបញ្ចេញទំនិញ ឬសម្ភារប្រើប្រាស់ដោយចុចប៊ូតុងខាងក្រោម' : 'Issue items and consumables to departments or employees.'" :actionUrl="route('stock-out.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
