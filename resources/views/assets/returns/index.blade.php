@extends('layouts.app')

@section('title', t('asset_returns') ?? 'Asset Returns')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('asset_returns') ?? 'Asset Returns History' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'កំណត់ត្រាឧបករណ៍ និងទ្រព្យសកម្មដែលបុគ្គលិកបានប្រគល់ត្រឡប់មកវិញ' : 'Record of checked-in assets returned by employees and departments' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('assets.assignments.index') }}" class="btn-secondary">
                <i class="bi bi-person-check mr-1.5"></i>
                Active Assignments
            </a>
            <a href="{{ route('assets.returns.create') }}" class="btn-primary">
                <i class="bi bi-arrow-return-left mr-1.5"></i>
                Check In / Return Asset
            </a>
        </div>
    </div>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$returns">
        <x-slot name="head">
            <th>Asset Tag & Name</th>
            <th>Returned By</th>
            <th>Return Date</th>
            <th>Condition</th>
            <th>Received By</th>
            <th>Notes</th>
        </x-slot>

        @forelse($returns as $ret)
            <tr>
                <td>
                    <a href="{{ route('assets.show', $ret->asset_id) }}" class="font-bold text-slate-900 hover:text-sky-600 block transition">
                        {{ $ret->asset_tag }}
                    </a>
                    <span class="text-[11px] text-slate-400">{{ $ret->asset_name }}</span>
                </td>
                <td>
                    <div class="font-semibold text-slate-800 text-xs">{{ $ret->employee_name }}</div>
                    <div class="text-[11px] text-slate-400 font-mono">{{ $ret->employee_code }}</div>
                </td>
                <td class="text-xs text-slate-500 whitespace-nowrap">{{ date('M d, Y', strtotime($ret->return_date)) }}</td>
                <td>
                    @if($ret->condition == 'good')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            Good Condition
                        </span>
                    @elseif($ret->condition == 'needs_repair')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            Needs Repair
                        </span>
                    @elseif($ret->condition == 'damaged')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                            Damaged
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ ucfirst($ret->condition) }}
                        </span>
                    @endif
                </td>
                <td class="text-xs text-slate-600">{{ $ret->receiver_name ?: 'System' }}</td>
                <td class="text-xs text-slate-500 max-w-xs truncate">{{ $ret->notes ?: '-' }}</td>
            </tr>
        @empty
            <x-table-empty :colspan="6" icon="arrow-counterclockwise" :title="app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាប្រគល់ត្រឡប់ទេ' : 'No Asset Return Records Found'" :description="app()->getLocale() == 'km' ? 'កត់ត្រាការប្រគល់ឧបករណ៍ ឬទ្រព្យសកម្មត្រឡប់មកស្តុកវិញ' : 'Process checked-in asset returns from team members.'" :actionUrl="route('assets.returns.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
