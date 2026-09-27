@extends('layouts.app')

@section('title', $supplier->name . ' - Supplier Details - IMS')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-800">{{ $supplier->name }}</h1>
                @if($supplier->status == 'active')
                    <span class="badge bg-emerald-100 text-emerald-800">Active</span>
                @else
                    <span class="badge bg-slate-100 text-slate-700">Inactive</span>
                @endif
            </div>
            <p class="text-sm text-slate-500 font-mono">Code: {{ $supplier->code }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('master.suppliers.edit', $supplier->id) }}" class="btn-primary">
                <i class="bi bi-pencil"></i> Edit Supplier
            </a>
            <a href="{{ route('master.suppliers.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Suppliers
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Supplier Profile -->
        <div class="glass-card p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3">Contact & Details</h2>

            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Contact Person</span>
                    <span class="font-medium text-slate-700">{{ $supplier->contact_name ?: 'None specified' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Email Address</span>
                    <span class="font-medium text-slate-700">{{ $supplier->email ?: 'None specified' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Phone Number</span>
                    <span class="font-medium text-slate-700">{{ $supplier->phone ?: 'None specified' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Tax ID / VAT</span>
                    <span class="font-mono text-slate-700">{{ $supplier->tax_id ?: 'None specified' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Payment Terms</span>
                    <span class="font-medium text-slate-700">{{ $supplier->payment_terms ?: 'Immediate' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Address</span>
                    <span class="font-medium text-slate-700">
                        {{ $supplier->address ?: '' }}
                        {{ $supplier->city ? ', ' . $supplier->city : '' }}
                        {{ $supplier->country ? ', ' . $supplier->country : '' }}
                    </span>
                </div>
                @if($supplier->notes)
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-xs text-slate-400 block mb-1">Notes</span>
                    <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded">{{ $supplier->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Orders & Supplied Items tabs/panels -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Purchase Orders -->
            <div class="glass-card overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-800">Purchase Orders ({{ count($orders) }})</h2>
                    <a href="{{ route('purchasing.orders.create', ['supplier_id' => $supplier->id]) }}" class="btn-secondary text-xs">
                        <i class="bi bi-plus-lg"></i> New PO
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $o)
                            <tr>
                                <td>
                                    <a href="{{ route('purchasing.orders.show', $o->id) }}" class="font-bold text-indigo-600 hover:text-indigo-800">
                                        {{ $o->po_number }}
                                    </a>
                                </td>
                                <td class="text-xs text-slate-600">{{ date('M d, Y', strtotime($o->order_date)) }}</td>
                                <td class="text-xs font-semibold font-mono text-slate-800">{{ format_currency($o->total_amount) }}</td>
                                <td>
                                    <span class="badge bg-slate-100 text-slate-700 text-[10px]">{{ ucfirst($o->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-xs text-slate-400">No purchase orders placed with this supplier.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Supplied Items -->
            <div class="glass-card overflow-hidden">
                <div class="p-4 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-800">Catalog Items ({{ count($items) }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Cost Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('items.show', $item->id) }}" class="font-mono text-xs font-bold text-indigo-600">
                                        {{ $item->sku }}
                                    </a>
                                </td>
                                <td class="text-sm font-medium text-slate-800">{{ $item->name }}</td>
                                <td class="text-xs text-slate-600">{{ $item->category_name ?: '-' }}</td>
                                <td class="text-xs font-mono font-semibold text-slate-700">{{ format_currency($item->cost_price) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-xs text-slate-400">No items explicitly assigned to this supplier.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
