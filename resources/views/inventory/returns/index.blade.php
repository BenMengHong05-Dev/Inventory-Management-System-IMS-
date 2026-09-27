@extends('layouts.app')

@section('title', t('inventory_returns') ?? 'Inventory Returns')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('inventory_returns') ?? 'Inventory Returns' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'គ្រប់គ្រងការប្រគល់ទំនិញត្រឡប់ទៅកាន់អ្នកផ្គត់ផ្គង់ ឬការប្រគល់ត្រឡប់ពីបុគ្គលិក/នាយកដ្ឋាន' : 'Manage stock returns to suppliers and returns back from employees or departments' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('inventory.index') }}" class="btn-secondary">
                <i class="bi bi-boxes mr-1.5"></i>
                {{ t('messages.inventory') }}
            </a>
            <a href="{{ route('inventory.returns.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('messages.create') ?? 'New Return Record' }}
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('inventory.returns.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div></div>
        <div class="flex items-center gap-2.5 justify-end w-full sm:w-auto">
            <div class="w-full sm:w-64">
                <x-select name="type" onchange="this.form.submit()" wrapperClass="!mb-0">
                    <option value="all" {{ $type == 'all' ? 'selected' : '' }}>All Returns</option>
                    <option value="to_supplier" {{ $type == 'to_supplier' ? 'selected' : '' }}>Return to Supplier (Debit / Defective)</option>
                    <option value="from_employee" {{ $type == 'from_employee' ? 'selected' : '' }}>Return from Employee (Restock)</option>
                </x-select>
            </div>
            <a href="{{ route('inventory.returns.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$returns">
        <x-slot name="head">
            <th>Return No #</th>
            <th>Type</th>
            <th>Party (Supplier / Employee)</th>
            <th>{{ t('messages.warehouse') }}</th>
            <th>{{ t('messages.date') }}</th>
            <th>{{ t('created_by') }}</th>
            <th>{{ t('notes') ?? 'Reason' }}</th>
        </x-slot>

        @forelse($returns as $r)
            <tr>
                <td class="font-bold font-mono text-sky-600 text-xs">{{ $r->return_number }}</td>
                <td>
                    @if($r->return_type == 'to_supplier')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-500/10 text-purple-700 border border-purple-500/20">
                            <i class="bi bi-arrow-down-right"></i>
                            To Supplier
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <i class="bi bi-arrow-up-right"></i>
                            From Employee
                        </span>
                    @endif
                </td>
                <td class="text-xs font-semibold text-slate-800">
                    @if($r->return_type == 'to_supplier')
                        {{ $r->supplier_name ?: 'Supplier' }}
                    @else
                        {{ $r->employee_name ?: 'Employee' }}
                    @endif
                </td>
                <td class="text-xs text-slate-700 font-medium">{{ $r->warehouse_name }}</td>
                <td class="text-xs text-slate-500 whitespace-nowrap">{{ date('M d, Y', strtotime($r->return_date)) }}</td>
                <td class="text-xs text-slate-600">{{ $r->creator_name ?: 'System' }}</td>
                <td class="text-xs text-slate-500 max-w-xs truncate">{{ $r->reason ?: '-' }}</td>
            </tr>
        @empty
            <x-table-empty :colspan="7" icon="arrow-return-left" :title="app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាប្រគល់ទំនិញត្រឡប់ទេ' : 'No Return Records Found'" :description="app()->getLocale() == 'km' ? 'កត់ត្រាការប្រគល់ទំនិញខូចខាត ឬសល់ត្រឡប់ចូលស្តុកវិញ' : 'Record returns to vendors or returns back into inventory.'" :actionUrl="route('inventory.returns.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
