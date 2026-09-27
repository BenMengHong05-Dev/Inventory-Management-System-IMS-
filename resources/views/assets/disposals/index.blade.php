@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'ធុងសំរាម & ការបោះបង់ទ្រព្យ' : 'Trash & Disposals Hub')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'ធុងសំរាម & ការបោះបង់ទ្រព្យ' : 'Trash & Disposals Hub' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'គ្រប់គ្រងកំណត់ត្រាទំនិញដែលបានលុប និងការកម្ទេច/បោះបង់ទ្រព្យសម្បត្តិ' : 'Manage disposal records, salvaged values, and decommissioning history' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('audit-logs.index') }}" class="btn-secondary">
                <i class="bi bi-shield-check mr-1.5 text-amber-500"></i>
                Audit Logs
            </a>
            <a href="{{ route('assets.index') }}" class="btn-secondary">
                <i class="bi bi-laptop mr-1.5"></i>
                {{ t('messages.assets') }}
            </a>
            <a href="{{ route('assets.disposals.create') }}" class="btn-primary">
                <i class="bi bi-trash mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'កត់ត្រាការបោះបង់' : 'New Disposal' }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('assets.disposals.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-80">
            <x-input type="text" name="search" value="{{ request('search', $search ?? '') }}" placeholder="{{ app()->getLocale() == 'km' ? 'ស្វែងរកតាមលេខកូដបោះបង់, លេខកូដទ្រព្យ, ឈ្មោះ...' : 'Search disposal #, tag, name...' }}" wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-56">
                @php $currentReason = request('reason', $reason ?? ''); @endphp
                <x-select name="reason" wrapperClass="!mb-0">
                    <option value="" {{ empty($currentReason) ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'មូលហេតុទាំងអស់' : 'All Reasons' }}</option>
                    <option value="DAMAGED" {{ $currentReason == 'DAMAGED' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ខូចខាត (Damaged)' : 'Damaged' }}</option>
                    <option value="OLD" {{ $currentReason == 'OLD' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ចាស់ទ្រុឌទ្រោម (Old / Obsolete)' : 'Old / Obsolete' }}</option>
                    <option value="BEYOND_REPAIR" {{ $currentReason == 'BEYOND_REPAIR' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'មិនអាចជួសជុលបាន' : 'Beyond Repair' }}</option>
                    <option value="LOST" {{ $currentReason == 'LOST' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បាត់បង់ (Lost / Stolen)' : 'Lost / Stolen' }}</option>
                    <option value="SOLD" {{ $currentReason == 'SOLD' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'លក់ចេញ (Sold)' : 'Sold' }}</option>
                    <option value="DONATED" {{ $currentReason == 'DONATED' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បរិច្ចាគ (Donated)' : 'Donated' }}</option>
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('messages.filter') }}
            </x-button>
            @if(!empty(request('search')) || !empty(request('reason')))
                <a href="{{ route('assets.disposals.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$disposals">
        <x-slot name="head">
            <th>{{ app()->getLocale() == 'km' ? 'លេខបោះបង់' : 'Disposal #' }}</th>
            <th>{{ t('messages.assets') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'មូលហេតុ' : 'Reason' }}</th>
            <th>{{ t('messages.date') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'តម្លៃសង្គ្រោះ' : 'Salvage Value' }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'អនុម័តដោយ' : 'Authorized By' }}</th>
        </x-slot>

        @forelse($disposals as $d)
            <tr>
                <td class="font-bold text-xs text-slate-900 font-mono">
                    {{ $d->disposal_number }}
                </td>
                <td>
                    <a href="{{ route('assets.show', $d->asset_id) }}" class="font-bold text-sky-600 hover:text-sky-700 text-xs block transition">
                        {{ $d->asset_code ?? $d->asset_tag ?? ('Asset #' . $d->asset_id) }}
                    </a>
                    <span class="text-[11px] text-slate-500 font-medium">{{ $d->asset_name ?? '' }}</span>
                </td>
                <td>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                        {{ $d->reason ?? $d->disposal_type ?? 'DISPOSED' }}
                    </span>
                </td>
                <td class="text-xs text-slate-600 whitespace-nowrap">
                    {{ date('d M Y', strtotime($d->disposal_date)) }}
                </td>
                <td class="text-xs font-bold text-slate-900 font-mono">
                    {{ $d->disposal_value ? format_currency($d->disposal_value) : '$0.00' }}
                </td>
                <td class="text-xs text-slate-600">
                    {{ $d->approver_name ?? $d->authorized_by_name ?? '-' }}
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="6" icon="trash2" :title="app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាបោះបង់ទេ' : 'No Disposal Records Found'" :description="app()->getLocale() == 'km' ? 'ទ្រព្យសម្បត្តិដែលកម្ទេច ឬបោះបង់ចោលនឹងបង្ហាញនៅទីនេះ' : 'Decommissioned or scrapped assets will be archived here.'" :actionUrl="route('assets.disposals.create')" :actionLabel="app()->getLocale() == 'km' ? 'កត់ត្រាការបោះបង់' : 'New Disposal'" />
        @endforelse
    </x-table>
</div>
@endsection
