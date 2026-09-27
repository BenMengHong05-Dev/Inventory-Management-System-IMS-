@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'របាយការណ៍ទ្រព្យសកម្ម និងការកាន់កាប់' : 'Asset & Custody Report')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'របាយការណ៍ទ្រព្យសកម្ម និងការកាន់កាប់' : 'Asset & Custody Report' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'បញ្ជីទ្រព្យសកម្ម តម្លៃទិញ ស្ថានភាពបច្ចុប្បន្ន និងការតាមដានអ្នកកាន់កាប់' : 'Asset register, purchase valuations, current status, and custodian tracking' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <i class="bi bi-grid mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'មជ្ឈមណ្ឌល' : 'Hub' }}
            </a>
            <a href="{{ route('reports.assets.export', request()->query()) }}" class="btn-primary">
                <i class="bi bi-download mr-1.5"></i>
                {{ t('messages.export') }} CSV
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('reports.assets') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <div></div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-44">
                <x-select name="status" wrapperClass="!mb-0">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>{{ t('messages.all') }} {{ t('messages.status') }}</option>
                    <option value="IN_STOCK" {{ $status == 'IN_STOCK' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ក្នុងស្តុក' : 'In Stock' }}</option>
                    <option value="ASSIGNED" {{ $status == 'ASSIGNED' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បានប្រគល់ជូន' : 'Assigned' }}</option>
                    <option value="MAINTENANCE" {{ $status == 'MAINTENANCE' ? 'selected' : '' }}>{{ t('messages.maintenance') }}</option>
                    <option value="DISPOSED" {{ $status == 'DISPOSED' ? 'selected' : '' }}>{{ t('messages.disposal') }}</option>
                </x-select>
            </div>
            <div class="w-full sm:w-44">
                <x-select name="category_id" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.categories') }}</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ localized_name($c, 'name') }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full sm:w-44">
                <x-select name="department_id" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.department') }}</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ localized_name($d, 'name') }}</option>
                    @endforeach
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('messages.filter') }}
            </x-button>
            <a href="{{ route('reports.assets') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$report">
        <x-slot name="head">
            <th>{{ t('messages.assets') }} & {{ t('messages.code') }}</th>
            <th>{{ t('messages.categories') }}</th>
            <th>{{ t('messages.department') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'អ្នកកាន់កាប់' : 'Assigned Custodian' }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'ថ្លៃទិញ' : 'Purchase Cost' }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'ផុតធានា' : 'Warranty Expiry' }}</th>
            <th>{{ t('messages.status') }}</th>
        </x-slot>

        @php $sumPurchase = 0; @endphp
        @forelse($report as $r)
            @php
                $sumPurchase += $r->purchase_cost;
            @endphp
            <tr>
                <td>
                    <a href="{{ route('assets.show', $r->id) }}" class="font-bold text-slate-900 hover:text-sky-600 block transition">
                        {{ $r->asset_name }}
                    </a>
                    <span class="text-[11px] text-slate-400 font-mono">{{ $r->asset_code ?? $r->asset_tag }}</span>
                </td>
                <td class="text-slate-600 text-xs">{{ localized_name($r, 'category_name') }}</td>
                <td class="text-slate-600 text-xs">{{ localized_name($r, 'department_name') }}</td>
                <td class="font-semibold text-slate-800 text-xs">{{ localized_name($r, 'custodian_name') ?: (app()->getLocale() == 'km' ? 'ក្នុងស្តុកកណ្តាល' : 'None (In Pool)') }}</td>
                <td class="text-right font-mono font-medium text-slate-900">{{ format_currency($r->purchase_cost) }}</td>
                <td class="text-slate-500 text-xs whitespace-nowrap">
                    {{ $r->warranty_expiry ? date('M d, Y', strtotime($r->warranty_expiry)) : '-' }}
                </td>
                <td>
                    @if($r->status === 'IN_STOCK')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            {{ $r->status }}
                        </span>
                    @elseif($r->status === 'ASSIGNED')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-700 border border-sky-500/20">
                            {{ $r->status }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $r->status }}
                        </span>
                    @endif
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="7" icon="laptop" :title="app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យទ្រព្យសកម្មទេ' : 'No Assets Found'" :description="app()->getLocale() == 'km' ? 'មិនមានទ្រព្យសកម្មត្រូវនឹងលក្ខខណ្ឌត្រងទេ' : 'No assets found matching the selected filter criteria.'" />
        @endforelse

        @if(count($report) > 0)
            <tfoot class="border-t border-slate-200/80 bg-slate-50/50 font-semibold text-xs">
                <tr>
                    <td colspan="4" class="py-3 px-4 text-right font-bold text-slate-700">{{ app()->getLocale() == 'km' ? 'តម្លៃទិញសរុប:' : 'Total Cost:' }}</td>
                    <td class="py-3 px-4 text-right font-mono text-emerald-700 font-bold text-sm">{{ format_currency($sumPurchase) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </x-table>
</div>
@endsection
