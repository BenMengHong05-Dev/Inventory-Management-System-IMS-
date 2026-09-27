@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'តាមដានការធានាទ្រព្យសកម្ម' : 'Asset Warranty Tracking')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'តាមដានការធានាទ្រព្យសកម្ម' : 'Asset Warranty Tracking' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'តាមដានកាលបរិច្ឆេទផុតកំណត់ធានា ការធានាដែលកំពុងមានសុពលភាព និងការធានាដែលផុតកំណត់' : 'Monitor asset warranty expiration dates, active coverage, and expired warranties' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('assets.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ទៅទ្រព្យសកម្ម' : 'Back to Assets' }}
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('assets.warranty') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div></div>
        <div class="flex items-center gap-2.5 justify-end w-full sm:w-auto">
            <label class="text-xs font-medium text-slate-600">{{ app()->getLocale() == 'km' ? 'ស្ថានភាពធានា:' : 'Coverage Status:' }}</label>
            <div class="w-full sm:w-60">
                <x-select name="status" onchange="this.form.submit()" wrapperClass="!mb-0">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ការធានាទាំងអស់' : 'All Warranties' }}</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'កំពុងមានសុពលភាព' : 'Active Coverage Only' }}</option>
                    <option value="expired" {{ $status == 'expired' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បានផុតកំណត់' : 'Expired Coverage Only' }}</option>
                    <option value="expiring_soon" {{ $status == 'expiring_soon' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ផុតកំណត់ក្នុងរង្វង់ ៣០ ថ្ងៃ' : 'Expiring within 30 Days' }}</option>
                </x-select>
            </div>
            <a href="{{ route('assets.warranty') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$assets">
        <x-slot name="head">
            <th>{{ t('messages.assets') }} & {{ t('messages.code') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'លេខស៊េរី' : 'Serial No' }}</th>
            <th>{{ t('messages.categories') }}</th>
            <th>{{ t('messages.suppliers') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទជាវ' : 'Purchase Date' }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទផុតធានា' : 'Warranty Expiry' }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'នៅសល់ / ស្ថានភាព' : 'Remaining / Status' }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($assets as $asset)
            @php
                $daysDiff = (int)$asset->remaining_days;
                $isExpired = $daysDiff < 0;
            @endphp
            <tr>
                <td>
                    <a href="{{ route('assets.show', $asset->id) }}" class="font-bold text-slate-900 hover:text-sky-600 block transition">
                        {{ $asset->asset_name }}
                    </a>
                    <span class="text-[11px] text-slate-400 font-mono">{{ $asset->asset_code }}</span>
                </td>
                <td class="font-mono text-slate-600 text-xs">{{ $asset->serial_number ?: '-' }}</td>
                <td class="text-slate-700 text-xs font-medium">{{ localized_name($asset, 'category_name') }}</td>
                <td class="text-slate-600 text-xs">{{ $asset->supplier_name ?: '-' }}</td>
                <td class="text-slate-500 text-xs whitespace-nowrap">{{ $asset->purchase_date ? date('M d, Y', strtotime($asset->purchase_date)) : '-' }}</td>
                <td class="font-bold text-slate-800 text-xs whitespace-nowrap">{{ date('M d, Y', strtotime($asset->warranty_expiry)) }}</td>
                <td>
                    @if($isExpired)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            {{ app()->getLocale() == 'km' ? 'ផុតកំណត់ (' . abs($daysDiff) . ' ថ្ងៃមុន)' : 'Expired (' . abs($daysDiff) . 'd ago)' }}
                        </span>
                    @elseif($daysDiff <= 30)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            {{ app()->getLocale() == 'km' ? 'នៅសល់ ' . $daysDiff . ' ថ្ងៃ' : $daysDiff . 'd remaining' }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ app()->getLocale() == 'km' ? 'មានសុពលភាព (' . $daysDiff . ' ថ្ងៃ)' : 'Active (' . $daysDiff . 'd)' }}
                        </span>
                    @endif
                </td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('assets.show', $asset->id) }}" class="btn-action btn-action-view" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="8" icon="shield-check" :title="app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យការធានាទេ' : 'No Warranty Records Found'" :description="app()->getLocale() == 'km' ? 'មិនមានទ្រព្យសកម្មត្រូវនឹងលក្ខខណ្ឌធានានេះទេ' : 'No assets found matching the selected warranty criteria.'" />
        @endforelse
    </x-table>
</div>
@endsection
