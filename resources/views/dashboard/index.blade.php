@extends('layouts.app')

@section('title', t('dashboard'))

@section('content')
<div class="space-y-6">

    <!-- 1. MODERN COMMAND & WELCOME HEADER -->
    <div class="glass-card p-6 border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-5 relative overflow-hidden">
        <div>
            <div class="flex items-center gap-2.5 mb-1.5 flex-wrap">
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                    {{ t('dashboard') }}
                </h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ app()->getLocale() == 'km' ? 'ប្រព័ន្ធដំណើរការប្រក្រតី' : 'System Operational' }}
                </span>
            </div>
            <p class="text-xs text-slate-500 font-medium flex items-center gap-2">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                <span>{{ \Carbon\Carbon::now()->translatedFormat(app()->getLocale() == 'km' ? 'l, d F Y' : 'l, F d, Y') }}</span>
                <span class="text-slate-300">•</span>
                <span>{{ app()->getLocale() == 'km' ? 'ការតាមដានស្តុក ចលនាទំនិញ និងទ្រព្យសម្បត្តិពេលវេលាជាក់ស្តែង' : 'Real-time stock movements, inventory tracking & asset management' }}</span>
            </p>
        </div>

        <!-- Quick Action Pill Buttons with Lucide Icons -->
        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" @click="$dispatch('open-scan-modal')" 
                    class="btn-secondary !rounded-full text-xs hover:border-sky-300 hover:text-sky-600 transition"
                    title="{{ app()->getLocale() == 'km' ? 'ស្កេនបាកូដ / QR Code' : 'Scan Barcode / QR' }}">
                <i data-lucide="scan-line" class="w-4 h-4 text-sky-600 mr-1.5"></i>
                <span>{{ app()->getLocale() == 'km' ? 'ស្កេនបាកូដ' : 'Scan Barcode' }}</span>
            </button>

            <button type="button" @click="$dispatch('open-trash-modal')" 
                    class="btn-secondary !rounded-full text-xs hover:border-rose-300 hover:text-rose-600 transition"
                    title="{{ app()->getLocale() == 'km' ? 'ធុងសំរាមរួម (Trash Hub)' : 'Trash & Recycle Bin' }}">
                <i data-lucide="trash-2" class="w-4 h-4 text-rose-500 mr-1.5"></i>
                <span>{{ app()->getLocale() == 'km' ? 'ធុងសំរាម' : 'Trash' }}</span>
                @if(isset($totalTrashCount) && $totalTrashCount > 0)
                    <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-rose-100 text-rose-700 font-bold">
                        {{ $totalTrashCount }}
                    </span>
                @endif
            </button>

            <a href="{{ route('stock-in.create') }}" class="btn-primary !rounded-full text-xs !bg-emerald-600 hover:!bg-emerald-700">
                <i data-lucide="arrow-down-to-dot" class="w-4 h-4 mr-1.5"></i>
                <span>{{ app()->getLocale() == 'km' ? 'នាំចូលស្តុក (Stock In)' : 'Stock In' }}</span>
            </a>

            <a href="{{ route('stock-out.create') }}" class="btn-primary !rounded-full text-xs !bg-sky-600 hover:!bg-sky-700">
                <i data-lucide="arrow-up-from-dot" class="w-4 h-4 mr-1.5"></i>
                <span>{{ app()->getLocale() == 'km' ? 'នាំចេញស្តុក (Stock Out)' : 'Stock Out' }}</span>
            </a>
        </div>
    </div>

    <!-- 2. TOP 4 HIGH-IMPACT KPI COUNT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- CARD 1: TOTAL INVENTORY VALUE -->
        <div class="glass-card p-5 border border-slate-200/80 transition-all duration-200 relative overflow-hidden group flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                        {{ app()->getLocale() == 'km' ? 'តម្លៃស្តុកសរុប' : 'Total Inventory Value' }}
                    </span>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ format_currency($totalInventoryValue) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500/15 to-teal-500/15 text-emerald-600 border border-emerald-500/20 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition duration-300">
                    <i data-lucide="circle-dollar-sign" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-emerald-600 font-bold flex items-center gap-1">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> {{ app()->getLocale() == 'km' ? 'ស្តុកសកម្មខ្ពស់' : 'Active Inventory' }}
                </span>
                <a href="{{ route('inventory.index') }}" class="text-slate-400 hover:text-sky-600 font-semibold transition">
                    {{ app()->getLocale() == 'km' ? 'មើលស្តុក →' : 'View Stock →' }}
                </a>
            </div>
        </div>

        <!-- CARD 2: TOTAL ITEMS & SKUs -->
        <div class="glass-card p-5 border border-slate-200/80 transition-all duration-200 relative overflow-hidden group flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                        {{ app()->getLocale() == 'km' ? 'មុខទំនិញសរុប' : 'Total Items' }}
                    </span>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalItems) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500/15 to-blue-500/15 text-sky-600 border border-sky-500/20 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition duration-300">
                    <i data-lucide="package" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-sky-600 font-bold flex items-center gap-1">
                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> {{ app()->getLocale() == 'km' ? 'រួចរាល់សម្រាប់ចរាចរ' : 'Ready for Circulation' }}
                </span>
                <a href="{{ route('items.index') }}" class="text-slate-400 hover:text-sky-600 font-semibold transition">
                    {{ app()->getLocale() == 'km' ? 'បញ្ជីទំនិញ →' : 'Item Catalog →' }}
                </a>
            </div>
        </div>

        <!-- CARD 3: STOCK ALERTS (LOW & OUT OF STOCK) -->
        <div class="glass-card p-5 border border-slate-200/80 transition-all duration-200 relative overflow-hidden group flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                        {{ app()->getLocale() == 'km' ? 'ការព្រមានស្តុក' : 'Stock Alerts' }}
                    </span>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-2xl font-black {{ ($lowStockItems + $outOfStockItems) > 0 ? 'text-amber-500' : 'text-slate-900' }} tracking-tight">
                            {{ $lowStockItems + $outOfStockItems }}
                        </h3>
                        @if(($lowStockItems + $outOfStockItems) > 0)
                            <span class="text-xs font-bold text-slate-400">({{ $lowStockItems }} {{ app()->getLocale() == 'km' ? 'ជិតអស់' : 'low' }}, {{ $outOfStockItems }} {{ app()->getLocale() == 'km' ? 'អស់' : 'out' }})</span>
                        @endif
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500/15 to-rose-500/15 text-amber-600 border border-amber-500/20 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition duration-300">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-rose-500 font-bold flex items-center gap-1">
                    <i data-lucide="bell" class="w-3.5 h-3.5"></i> {{ app()->getLocale() == 'km' ? 'ទាមទារបញ្ជាទិញបន្ថែម' : 'Reorder Needed' }}
                </span>
                <a href="{{ route('inventory.low-stock') }}" class="text-slate-400 hover:text-amber-600 font-semibold transition">
                    {{ app()->getLocale() == 'km' ? 'ពិនិត្យស្តុក →' : 'Check Stock →' }}
                </a>
            </div>
        </div>

        <!-- CARD 4: FIXED ASSETS & FACILITIES -->
        <div class="glass-card p-5 border border-slate-200/80 transition-all duration-200 relative overflow-hidden group flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                        {{ app()->getLocale() == 'km' ? 'ទ្រព្យសកម្មរឹង' : 'Fixed Assets' }}
                    </span>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalAssets) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-500/15 to-indigo-500/15 text-purple-600 border border-purple-500/20 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition duration-300">
                    <i data-lucide="laptop" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-purple-600 font-bold flex items-center gap-1">
                    <i data-lucide="wrench" class="w-3.5 h-3.5"></i> {{ app()->getLocale() == 'km' ? 'ថែទាំ' : 'Maintenance' }}: {{ $assetsMaintenance }}
                </span>
                <a href="{{ route('assets.index') }}" class="text-slate-400 hover:text-purple-600 font-semibold transition">
                    {{ app()->getLocale() == 'km' ? 'បញ្ជីទ្រព្យ →' : 'Asset Register →' }}
                </a>
            </div>
        </div>
    </div>

    <!-- 3. OPERATIONAL PIPELINE BADGES -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <a href="{{ route('purchase-requests.index', ['status' => 'PENDING']) }}" 
           class="p-3.5 bg-white border border-slate-200/80 rounded-2xl flex items-center justify-between hover:border-amber-300 transition group">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 flex-shrink-0"></span>
                <span class="text-xs font-bold text-slate-700 truncate group-hover:text-amber-700 transition">
                    {{ app()->getLocale() == 'km' ? 'រង់ចាំអនុម័តសំណើទិញ' : 'Pending PR Approvals' }}
                </span>
            </div>
            <span class="text-xs font-black text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-100 flex-shrink-0">
                {{ $pendingPurchaseRequests }}
            </span>
        </a>

        <a href="{{ route('stock-transfers.index', ['status' => 'PENDING']) }}" 
           class="p-3.5 bg-white border border-slate-200/80 rounded-2xl flex items-center justify-between hover:border-sky-300 transition group">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-2.5 h-2.5 rounded-full bg-sky-400 flex-shrink-0"></span>
                <span class="text-xs font-bold text-slate-700 truncate group-hover:text-sky-700 transition">
                    {{ app()->getLocale() == 'km' ? 'រង់ចាំផ្ទេរស្តុក' : 'Pending Stock Transfers' }}
                </span>
            </div>
            <span class="text-xs font-black text-sky-700 bg-sky-50 px-2.5 py-0.5 rounded-full border border-sky-100 flex-shrink-0">
                {{ $pendingStockRequests }}
            </span>
        </a>

        <a href="{{ route('maintenance.index', ['status' => 'IN_PROGRESS']) }}" 
           class="p-3.5 bg-white border border-slate-200/80 rounded-2xl flex items-center justify-between hover:border-rose-300 transition group">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-400 flex-shrink-0"></span>
                <span class="text-xs font-bold text-slate-700 truncate group-hover:text-rose-700 transition">
                    {{ app()->getLocale() == 'km' ? 'កំពុងថែទាំ/ជួសជុល' : 'In Maintenance' }}
                </span>
            </div>
            <span class="text-xs font-black text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-100 flex-shrink-0">
                {{ $assetsMaintenance }}
            </span>
        </a>

        <a href="{{ route('assets.warranty') }}" 
           class="p-3.5 bg-white border border-slate-200/80 rounded-2xl flex items-center justify-between hover:border-purple-300 transition group">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-400 flex-shrink-0"></span>
                <span class="text-xs font-bold text-slate-700 truncate group-hover:text-purple-700 transition">
                    {{ app()->getLocale() == 'km' ? 'ជិតផុតធានា (៣០ថ្ងៃ)' : 'Warranty Expiring (30d)' }}
                </span>
            </div>
            <span class="text-xs font-black text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-full border border-purple-100 flex-shrink-0">
                {{ $warrantyExpiringSoon }}
            </span>
        </a>
    </div>

    <!-- 4. TWO MODERN CHARTS (DOUGHNUT & LINE CHART) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- CHART 1: MODERN DOUGHNUT CHART (Value by Category) -->
        <div class="lg:col-span-5 glass-card p-6 border border-slate-200/80 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="pie-chart" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wide">
                                {{ app()->getLocale() == 'km' ? 'តម្លៃស្តុកតាមប្រភេទទំនិញ' : 'Inventory Value by Category' }}
                            </h3>
                            <p class="text-[10px] text-slate-400 font-medium">
                                {{ app()->getLocale() == 'km' ? 'ការបែងចែកតម្លៃតាមប្រភេទផលិតផល' : 'Valuation breakdown by product category' }}
                            </p>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                        {{ count($valueByCategory) }} {{ app()->getLocale() == 'km' ? 'ប្រភេទ' : 'Categories' }}
                    </span>
                </div>

                <!-- Donut Canvas with Center KPI Overlay -->
                <div class="relative h-64 my-2">
                    <canvas id="categoryDonutChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            {{ app()->getLocale() == 'km' ? 'សរុប' : 'Total' }}
                        </span>
                        <span class="text-base font-black text-slate-900 leading-tight">{{ format_currency($totalInventoryValue) }}</span>
                        <span class="text-[10px] text-emerald-600 font-semibold mt-0.5">{{ number_format($totalInventoryQty, 0) }} {{ app()->getLocale() == 'km' ? 'ឯកតា' : 'units' }}</span>
                    </div>
                </div>
            </div>

            <!-- Custom Category Legend Chips -->
            <div class="pt-4 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                @php
                    $chartColors = ['#0284c7', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4'];
                @endphp
                @foreach(array_slice($valueByCategory, 0, 4) as $idx => $cat)
                    <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center gap-2 truncate">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $chartColors[$idx % count($chartColors)] }}"></span>
                            <span class="text-[11px] font-bold text-slate-700 truncate">{{ $cat->category_name }}</span>
                        </div>
                        <span class="text-[10px] font-black text-slate-900 ml-1">{{ format_currency($cat->total_val) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- CHART 2: MODERN LINE CHART (Stock In vs Stock Out Monthly Trends) -->
        <div class="lg:col-span-7 glass-card p-6 border border-slate-200/80 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4 flex-wrap gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="trending-up" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wide">
                                {{ app()->getLocale() == 'km' ? 'និន្នាការចរន្តស្តុក (ចូល vs ចេញ)' : 'Monthly Stock Movements (In vs Out)' }}
                            </h3>
                            <p class="text-[10px] text-slate-400 font-medium">
                                {{ app()->getLocale() == 'km' ? 'ប្រៀបធៀបបរិមាណនាំចូល និងនាំចេញ' : 'Compare inbound vs outbound volume' }}
                            </p>
                        </div>
                    </div>
                    <!-- Custom Chart Legend Badges -->
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            {{ app()->getLocale() == 'km' ? 'ស្តុកចូល (Stock In)' : 'Stock In' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-full border border-sky-100">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                            {{ app()->getLocale() == 'km' ? 'ស្តុកចេញ (Stock Out)' : 'Stock Out' }}
                        </span>
                    </div>
                </div>

                <!-- Spline Line Canvas -->
                <div class="h-64 my-2">
                    <canvas id="stockTrendLineChart"></canvas>
                </div>
            </div>

            <!-- Bottom Chart Summary -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 flex-wrap gap-2">
                <span class="text-[11px] font-medium flex items-center gap-1 text-slate-500">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-sky-500"></i>
                    {{ app()->getLocale() == 'km' ? 'ទិន្នន័យចរន្តស្តុកប្រចាំខែសម្រាប់វិភាគតម្រូវការឃ្លាំង' : 'Monthly inventory movement data for warehouse capacity analysis' }}
                </span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.index') }}" class="text-[11px] font-bold text-sky-600 hover:text-sky-700 transition">
                        {{ app()->getLocale() == 'km' ? 'មើលរបាយការណ៍លម្អិត →' : 'View Full Reports →' }}
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- MODERN CHARTS INITIALIZATION SCRIPT (Chart.js) -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Chart.defaults.font.family = '"Google Sans", "Plus Jakarta Sans", "Noto Sans Khmer", "Kantumruy Pro", system-ui, sans-serif';
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#64748b';

        // ── 1. MODERN DOUGHNUT CHART (Value by Category) ──
        const catLabels = {!! json_encode(array_column($valueByCategory, 'category_name')) !!};
        const catData = {!! json_encode(array_column($valueByCategory, 'total_val')) !!};
        const donutColors = ['#0284c7', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4'];

        const donutCtx = document.getElementById('categoryDonutChart');
        if (donutCtx) {
            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: catLabels.length ? catLabels : ['{{ app()->getLocale() == "km" ? "ទូទៅ" : "General" }}'],
                    datasets: [{
                        data: catData.length ? catData : [100],
                        backgroundColor: donutColors,
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 6,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '74%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 12,
                            cornerRadius: 12,
                            callbacks: {
                                label: function(context) {
                                    const val = context.parsed || 0;
                                    return ' ' + context.label + ': $' + Number(val).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                            }
                        }
                    }
                }
            });
        }

        // ── 2. MODERN LINE CHART (Stock In vs Stock Out Monthly Trends) ──
        const trendLabels = {!! json_encode($trendMonths ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!};
        const stockInData = {!! json_encode($trendStockIn ?? [25, 40, 30, 60, 45, 70]) !!};
        const stockOutData = {!! json_encode($trendStockOut ?? [15, 20, 25, 35, 30, 45]) !!};

        const lineCtx = document.getElementById('stockTrendLineChart');
        if (lineCtx) {
            const ctx2d = lineCtx.getContext('2d');

            const inGradient = ctx2d.createLinearGradient(0, 0, 0, 240);
            inGradient.addColorStop(0, 'rgba(16, 185, 129, 0.22)');
            inGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            const outGradient = ctx2d.createLinearGradient(0, 0, 0, 240);
            outGradient.addColorStop(0, 'rgba(2, 132, 199, 0.22)');
            outGradient.addColorStop(1, 'rgba(2, 132, 199, 0.0)');

            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [
                        {
                            label: '{{ app()->getLocale() == "km" ? "ស្តុកចូល (Stock In)" : "Stock In" }}',
                            data: stockInData,
                            borderColor: '#10b981',
                            backgroundColor: inGradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.42,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#10b981',
                            pointBorderWidth: 2.5,
                            pointRadius: 4.5,
                            pointHoverRadius: 7,
                            pointHoverBackgroundColor: '#10b981',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 2
                        },
                        {
                            label: '{{ app()->getLocale() == "km" ? "ស្តុកចេញ (Stock Out)" : "Stock Out" }}',
                            data: stockOutData,
                            borderColor: '#0284c7',
                            backgroundColor: outGradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.42,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#0284c7',
                            pointBorderWidth: 2.5,
                            pointRadius: 4.5,
                            pointHoverRadius: 7,
                            pointHoverBackgroundColor: '#0284c7',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 12,
                            cornerRadius: 12,
                            callbacks: {
                                label: function(context) {
                                    const unitText = '{{ app()->getLocale() == "km" ? " ឯកតា" : " units" }}';
                                    return ' ' + context.dataset.label + ': ' + Number(context.parsed.y).toLocaleString() + unitText;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { weight: '600' },
                                color: '#64748b'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            border: { dash: [4, 4] },
                            grid: {
                                color: 'rgba(226, 232, 240, 0.7)'
                            },
                            ticks: {
                                stepSize: 20,
                                color: '#94a3b8'
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
