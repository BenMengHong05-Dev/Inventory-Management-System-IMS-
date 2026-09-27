@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'របាយការណ៍បញ្ជាទិញ និងអ្នកផ្គត់ផ្គង់' : 'Purchasing & Vendor Spend Report')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'របាយការណ៍បញ្ជាទិញ និងអ្នកផ្គត់ផ្គង់' : 'Purchasing & Vendor Spend Report' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'តាមដានការចំណាយលើការបញ្ជាទិញ ការប្តេជ្ញាចិត្តរបស់អ្នកផ្គត់ផ្គង់ និងការបំពេញទំនិញ' : 'Track purchase order expenditures, supplier commitments, and fulfillment timelines' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <i class="bi bi-grid mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'មជ្ឈមណ្ឌល' : 'Hub' }}
            </a>
            <a href="{{ route('reports.purchasing.export', request()->query()) }}" class="btn-primary">
                <i class="bi bi-download mr-1.5"></i>
                {{ t('messages.export') }} CSV
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('reports.purchasing') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <div></div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-44">
                <x-select name="status" wrapperClass="!mb-0">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>{{ t('messages.all') }} {{ t('messages.status') }}</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>{{ t('messages.approved') }}</option>
                    <option value="ordered" {{ $status == 'ordered' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បានបញ្ជាទិញ' : 'Ordered' }}</option>
                    <option value="received" {{ $status == 'received' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បានទទួលពេញលេញ' : 'Received / Complete' }}</option>
                </x-select>
            </div>
            <div class="w-full sm:w-44">
                <x-select name="supplier_id" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.suppliers') }}</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $supplierId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
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
            <a href="{{ route('reports.purchasing') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$report">
        <x-slot name="head">
            <th>{{ t('messages.purchase_orders') }} #</th>
            <th>{{ t('messages.suppliers') }}</th>
            <th>{{ t('messages.date') }}</th>
            <th class="text-right">{{ t('messages.total') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'បង្កើតដោយ' : 'Created By' }}</th>
            <th>{{ t('messages.status') }}</th>
        </x-slot>

        @php $sumTotal = 0; @endphp
        @forelse($report as $r)
            @php $sumTotal += ($r->total ?? $r->total_amount ?? 0); @endphp
            <tr>
                <td class="font-mono font-bold text-slate-900 text-xs">
                    {{ $r->po_number }}
                </td>
                <td class="font-semibold text-slate-800 text-xs">{{ $r->supplier_name }}</td>
                <td class="text-slate-600 text-xs whitespace-nowrap">{{ date('M d, Y', strtotime($r->order_date)) }}</td>
                <td class="text-right font-mono font-bold text-slate-900 text-xs">{{ format_currency($r->total ?? $r->total_amount ?? 0) }}</td>
                <td class="text-slate-600 text-xs">{{ $r->creator_name ?: 'System' }}</td>
                <td>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                        {{ $r->status }}
                    </span>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="6" icon="receipt" :title="app()->getLocale() == 'km' ? 'មិនមានការបញ្ជាទិញទេ' : 'No Purchase Orders Found'" :description="app()->getLocale() == 'km' ? 'មិនមានការបញ្ជាទិញត្រូវនឹងលក្ខខណ្ឌត្រងទេ' : 'No purchase orders match the selected filter criteria.'" />
        @endforelse

        @if(count($report) > 0)
            <tfoot class="border-t border-slate-200/80 bg-slate-50/50 font-semibold text-xs">
                <tr>
                    <td colspan="3" class="py-3 px-4 text-right font-bold text-slate-700">{{ app()->getLocale() == 'km' ? 'សរុបចំណាយ:' : 'Total Spend:' }}</td>
                    <td class="py-3 px-4 text-right font-mono text-emerald-700 font-bold text-sm">{{ format_currency($sumTotal) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </x-table>
</div>
@endsection
