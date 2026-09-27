@extends('layouts.app')

@section('title', 'Global Search: ' . $query . ' - IMS')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Search Results for "{{ $query }}"</h1>
            <p class="text-sm text-slate-500">Cross-system unified search results across items, assets, suppliers, orders, and personnel</p>
        </div>
    </div>

    <!-- Search Box (No white card background, no shadow, search on left, button on right) -->
    <form method="GET" action="{{ route('search.global') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div class="relative w-full max-w-md">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="q" value="{{ $query }}" placeholder="Search across everything..." class="form-input pl-9 bg-white border border-slate-200 rounded-xl">
        </div>
        <button type="submit" class="btn-dark !py-2 !px-5 text-xs font-bold">Search</button>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Items Found -->
        <div class="card overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-box-seam text-indigo-600"></i> Items & Products ({{ count($items) }})
                </h2>
            </div>
            <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                @forelse($items as $item)
                <div class="p-3 hover:bg-slate-50 flex items-center justify-between">
                    <div>
                        <a href="{{ route('items.show', $item->id) }}" class="font-bold text-indigo-600 hover:underline">
                            {{ $item->name }}
                        </a>
                        <div class="text-xs text-slate-500 font-mono">SKU: {{ $item->sku }} &bull; {{ $item->category_name }}</div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold font-mono text-slate-800">{{ format_currency($item->cost_price) }}</span>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-xs text-slate-400">No matching items found.</div>
                @endforelse
            </div>
        </div>

        <!-- Assets Found -->
        <div class="card overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-laptop text-emerald-600"></i> Fixed Assets & Equipment ({{ count($assets) }})
                </h2>
            </div>
            <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                @forelse($assets as $asset)
                <div class="p-3 hover:bg-slate-50 flex items-center justify-between">
                    <div>
                        <a href="{{ route('assets.show', $asset->id) }}" class="font-bold text-indigo-600 hover:underline">
                            {{ $asset->asset_tag }} - {{ $asset->name }}
                        </a>
                        <div class="text-xs text-slate-500 font-mono">SN: {{ $asset->serial_number ?: 'N/A' }}</div>
                    </div>
                    <div>
                        <span class="badge bg-slate-100 text-slate-700 text-[10px] uppercase">{{ $asset->status }}</span>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-xs text-slate-400">No matching assets found.</div>
                @endforelse
            </div>
        </div>

        <!-- Purchase Orders Found -->
        <div class="card overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-cart3 text-amber-600"></i> Purchase Orders ({{ count($purchaseOrders) }})
                </h2>
            </div>
            <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                @forelse($purchaseOrders as $po)
                <div class="p-3 hover:bg-slate-50 flex items-center justify-between">
                    <div>
                        <a href="{{ route('purchasing.orders.show', $po->id) }}" class="font-bold text-indigo-600 hover:underline">
                            {{ $po->po_number }}
                        </a>
                        <div class="text-xs text-slate-500">{{ $po->supplier_name }} &bull; {{ date('M d, Y', strtotime($po->order_date)) }}</div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold font-mono text-slate-800">{{ format_currency($po->total_amount) }}</span>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-xs text-slate-400">No matching purchase orders found.</div>
                @endforelse
            </div>
        </div>

        <!-- Suppliers & Employees Found -->
        <div class="card overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-people text-purple-600"></i> Contacts (Suppliers & Employees)
                </h2>
            </div>
            <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                @forelse($suppliers as $s)
                <div class="p-3 hover:bg-slate-50 flex items-center justify-between">
                    <div>
                        <a href="{{ route('master.suppliers.show', $s->id) }}" class="font-bold text-indigo-600 hover:underline">
                            {{ $s->name }} (Supplier)
                        </a>
                        <div class="text-xs text-slate-500 font-mono">{{ $s->code }} &bull; {{ $s->email }}</div>
                    </div>
                    <span class="badge bg-purple-100 text-purple-800 text-[10px]">Supplier</span>
                </div>
                @empty
                @endforelse

                @forelse($employees as $e)
                <div class="p-3 hover:bg-slate-50 flex items-center justify-between">
                    <div>
                        <a href="{{ route('master.employees.show', $e->id) }}" class="font-bold text-indigo-600 hover:underline">
                            {{ $e->name }} (Employee)
                        </a>
                        <div class="text-xs text-slate-500 font-mono">{{ $e->code }} &bull; {{ $e->department_name }}</div>
                    </div>
                    <span class="badge bg-emerald-100 text-emerald-800 text-[10px]">Staff</span>
                </div>
                @empty
                @endforelse

                @if(count($suppliers) == 0 && count($employees) == 0)
                <div class="p-4 text-center text-xs text-slate-400">No matching contacts found.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
