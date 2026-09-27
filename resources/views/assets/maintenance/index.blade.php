@extends('layouts.app')

@section('title', t('asset_maintenance') ?? 'Asset Maintenance')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('asset_maintenance') ?? 'Asset Maintenance & Repairs' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'តាមដានការថែទាំបង្ការ ការជួសជុល និងការក្រិតតាមខ្នាតឧបករណ៍' : 'Track preventive servicing, repairs, calibration, and vendor maintenance work' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('assets.index') }}" class="btn-secondary">
                <i class="bi bi-laptop mr-1.5"></i>
                {{ t('messages.assets') }}
            </a>
            <a href="{{ route('assets.maintenance.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                Schedule Maintenance
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('assets.maintenance.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div></div>
        <div class="flex items-center gap-2.5 justify-end w-full sm:w-auto">
            <label class="text-xs font-medium text-slate-600">Status:</label>
            <div class="w-full sm:w-52">
                <x-select name="status" onchange="this.form.submit()" wrapperClass="!mb-0">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Maintenance</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </x-select>
            </div>
            <a href="{{ route('assets.maintenance.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$maintenances">
        <x-slot name="head">
            <th>Asset & Tag</th>
            <th>Title / Type</th>
            <th>Service Provider</th>
            <th>Start Date</th>
            <th>Expected End</th>
            <th>Cost</th>
            <th>{{ t('messages.status') }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($maintenances as $m)
            <tr>
                <td>
                    <a href="{{ route('assets.show', $m->asset_id) }}" class="font-bold text-slate-900 hover:text-sky-600 block transition">
                        {{ $m->asset_tag }}
                    </a>
                    <span class="text-[11px] text-slate-400">{{ $m->asset_name }}</span>
                </td>
                <td>
                    <div class="font-semibold text-slate-800 text-xs">{{ $m->title }}</div>
                    <span class="text-[10px] text-slate-400 uppercase font-medium">
                        {{ str_replace('_', ' ', $m->maintenance_type) }}
                    </span>
                    @if($m->priority == 'urgent' || $m->priority == 'high')
                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-rose-500/10 text-rose-700 ml-1 uppercase">{{ $m->priority }}</span>
                    @endif
                </td>
                <td class="text-xs text-slate-600">{{ $m->supplier_name ?: 'Internal Technician' }}</td>
                <td class="text-xs text-slate-500 whitespace-nowrap">{{ $m->start_date ? date('M d, Y', strtotime($m->start_date)) : '-' }}</td>
                <td class="text-xs text-slate-500 whitespace-nowrap">{{ $m->expected_completion_date ? date('M d, Y', strtotime($m->expected_completion_date)) : '-' }}</td>
                <td class="text-xs font-bold text-slate-900 font-mono">
                    {{ $m->actual_cost ? format_currency($m->actual_cost) : ($m->cost ? format_currency($m->cost) . ' (est)' : '-') }}
                </td>
                <td>
                    @if($m->status == 'completed')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Completed
                        </span>
                    @elseif($m->status == 'in_progress')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-700 border border-sky-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                            In Progress
                        </span>
                    @elseif($m->status == 'pending')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Pending
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ ucfirst($m->status) }}
                        </span>
                    @endif
                </td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('assets.maintenance.show', $m->id) }}" class="btn-action btn-action-view" title="View & Update">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="8" icon="tools" :title="app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាជួសជុលទេ' : 'No Maintenance Records Found'" :description="app()->getLocale() == 'km' ? 'កំណត់កាលវិភាគថែទាំ និងជួសជុលទ្រព្យសកម្ម' : 'Log equipment service schedules and vendor repairs.'" :actionUrl="route('assets.maintenance.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
