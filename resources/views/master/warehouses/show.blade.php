@extends('layouts.app')

@section('title', $warehouse->name . ' - Warehouse Details - IMS')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-800">{{ $warehouse->name }}</h1>
                @if($warehouse->is_default)
                    <span class="badge bg-indigo-100 text-indigo-800">Default Site</span>
                @endif
                @if($warehouse->is_active)
                    <span class="badge bg-emerald-100 text-emerald-800">Active</span>
                @else
                    <span class="badge bg-slate-100 text-slate-700">Inactive</span>
                @endif
            </div>
            <p class="text-sm text-slate-500 font-mono">Code: {{ $warehouse->code }} | {{ $warehouse->city ? $warehouse->city . ' - ' : '' }}{{ $warehouse->address }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('master.locations.index', ['warehouse_id' => $warehouse->id]) }}" class="btn-primary">
                <i class="bi bi-geo-alt"></i> Manage Locations
            </a>
            <a href="{{ route('master.warehouses.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Warehouses
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Storage Locations in this Warehouse -->
        <div class="card overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-800">Storage Locations ({{ count($locations) }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Aisle/Rack/Shelf/Bin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locations as $loc)
                        <tr>
                            <td class="font-medium text-slate-800">{{ $loc->name }}</td>
                            <td class="text-xs font-mono text-slate-500">
                                {{ implode('-', array_filter([$loc->aisle, $loc->rack, $loc->shelf, $loc->bin])) ?: 'Open Floor' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center py-4 text-xs text-slate-400">No bins/locations created yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventory Stock in this Warehouse -->
        <div class="lg:col-span-2 card overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-800">Stock Levels in this Facility ({{ count($inventory) }})</h2>
                <a href="{{ route('stock_in.create', ['warehouse_id' => $warehouse->id]) }}" class="btn-secondary text-xs">
                    <i class="bi bi-arrow-down-left"></i> Stock In
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Item & SKU</th>
                            <th>Category</th>
                            <th class="text-right">Qty on Hand</th>
                            <th class="text-right">Reserved</th>
                            <th class="text-right">Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventory as $inv)
                        <tr>
                            <td>
                                <a href="{{ route('items.show', $inv->item_id) }}" class="font-bold text-indigo-600 hover:text-indigo-800">
                                    {{ $inv->item_name }}
                                </a>
                                <div class="text-xs text-slate-400 font-mono">{{ $inv->sku }}</div>
                            </td>
                            <td class="text-xs text-slate-600">{{ $inv->category_name ?: '-' }}</td>
                            <td class="text-right font-mono font-semibold text-slate-800">{{ number_format($inv->quantity, 2) }}</td>
                            <td class="text-right font-mono text-amber-600">{{ number_format($inv->reserved_quantity, 2) }}</td>
                            <td class="text-right font-mono font-bold text-emerald-600">{{ number_format($inv->available_quantity, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-xs text-slate-400">No items currently stocked in this warehouse.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
