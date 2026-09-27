@extends('layouts.app')

@section('title', t('messages.goods_receiving') . ' (GRN)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('messages.goods_receiving') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'កត់ត្រាការទទួលទំនិញជាក់ស្តែងពីការបញ្ជាទិញ (PO) និងគាំទ្រការទទួលដោយផ្នែក' : 'Record incoming deliveries against Purchase Orders with partial receiving support' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('goods-receipts.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('messages.create') ?? 'New GRN' }}
            </a>
        </div>
    </div>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$receipts">
        <x-slot name="head">
            <th>GRN #</th>
            <th>Receipt Date</th>
            <th>Purchase Order #</th>
            <th>{{ t('messages.suppliers') }}</th>
            <th>{{ t('messages.warehouse') }}</th>
            <th class="text-right">Items Received</th>
            <th class="text-right">Total Received Value</th>
            <th>Received By</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($receipts as $rc)
            <tr>
                <td class="font-mono font-bold text-slate-900">
                    <a href="{{ route('goods-receipts.show', $rc->id) }}" class="text-sky-600 hover:text-sky-700 transition">
                        {{ $rc->receipt_number }}
                    </a>
                </td>
                <td class="text-slate-600 whitespace-nowrap">{{ $rc->receipt_date }}</td>
                <td class="font-mono text-slate-700 text-xs">
                    <a href="{{ route('purchase-orders.show', $rc->purchase_order_id) }}" class="text-slate-700 hover:text-sky-600 font-semibold transition">
                        {{ $rc->po_number }}
                    </a>
                </td>
                <td class="text-slate-800 font-semibold">{{ $rc->supplier_name }}</td>
                <td class="text-slate-700 font-medium">{{ $rc->warehouse_name }}</td>
                <td class="text-right font-bold text-slate-800">{{ $rc->total_items }}</td>
                <td class="text-right font-bold text-slate-900">{{ format_currency($rc->total_received_value) }}</td>
                <td class="text-slate-600">{{ $rc->receiver_name }}</td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('goods-receipts.show', $rc->id) }}" class="btn-action btn-action-view" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="9" icon="box-seam" :title="app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាទទួលទំនិញទេ' : 'No Goods Receipts Recorded'" :description="app()->getLocale() == 'km' ? 'កត់ត្រាការទទួលទំនិញពីការបញ្ជាទិញរបស់អ្នក' : 'Create a goods receipt when deliveries arrive at the warehouse.'" :actionUrl="route('goods-receipts.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
