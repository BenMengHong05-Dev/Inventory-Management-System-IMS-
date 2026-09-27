@extends('layouts.app')

@section('title', t('asset_assignments') ?? 'Asset Assignments')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('asset_assignments') ?? 'Asset Assignments' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'តាមដាន និងគ្រប់គ្រងឧបករណ៍ និងទ្រព្យសម្បត្តិដែលបានប្រគល់ជូនបុគ្គលិកប្រើប្រាស់' : 'Track and manage equipment and assets currently checked out to personnel' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('assets.returns.create') }}" class="btn-secondary">
                <i class="bi bi-arrow-return-left mr-1.5"></i>
                Check In / Return
            </a>
            <a href="{{ route('assets.assignments.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                New Assignment
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('assets.assignments.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div></div>
        <div class="flex items-center gap-2.5 justify-end w-full sm:w-auto">
            <label class="text-xs font-medium text-slate-600">Status:</label>
            <div class="w-full sm:w-48">
                <x-select name="status" onchange="this.form.submit()" wrapperClass="!mb-0">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Currently Active</option>
                    <option value="returned" {{ $status == 'returned' ? 'selected' : '' }}>Returned</option>
                </x-select>
            </div>
            <a href="{{ route('assets.assignments.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$assignments">
        <x-slot name="head">
            <th>Asset Tag & Item</th>
            <th>Assigned To</th>
            <th>{{ t('messages.department') }}</th>
            <th>Assigned Date</th>
            <th>Due Date</th>
            <th>{{ t('messages.location') }}</th>
            <th>{{ t('messages.status') }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($assignments as $assign)
            <tr>
                <td>
                    <a href="{{ route('assets.show', $assign->asset_id) }}" class="font-bold text-slate-900 hover:text-sky-600 block transition">
                        {{ $assign->asset_tag }}
                    </a>
                    <span class="text-[11px] text-slate-400">{{ $assign->asset_name }}</span>
                </td>
                <td>
                    <span class="font-semibold text-slate-800 block text-xs">{{ $assign->employee_name }}</span>
                    <span class="text-[11px] text-slate-400 font-mono">{{ $assign->employee_code }}</span>
                </td>
                <td class="text-xs text-slate-600">{{ $assign->department_name ?: '-' }}</td>
                <td class="text-xs text-slate-500 whitespace-nowrap">{{ date('M d, Y', strtotime($assign->assigned_date)) }}</td>
                <td class="text-xs whitespace-nowrap">
                    @if($assign->due_date)
                        <span class="text-slate-600 font-medium">{{ date('M d, Y', strtotime($assign->due_date)) }}</span>
                        @if($assign->status == 'active' && \Carbon\Carbon::parse($assign->due_date)->isPast())
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-700 ml-1">Overdue</span>
                        @endif
                    @else
                        <span class="text-slate-400">Indefinite</span>
                    @endif
                </td>
                <td class="text-xs text-slate-600">{{ $assign->location_name ?: '-' }}</td>
                <td>
                    @if($assign->status == 'active')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-700 border border-sky-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            Returned
                        </span>
                    @endif
                </td>
                <td class="text-right whitespace-nowrap">
                    @if($assign->status == 'active')
                        <a href="{{ route('assets.returns.create', ['assignment_id' => $assign->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-sky-500/10 text-sky-700 hover:bg-sky-500/20 border border-sky-500/20 rounded-xl text-xs font-semibold transition" title="Check In">
                            <i class="bi bi-arrow-return-left"></i> Check In
                        </a>
                    @else
                        <span class="text-xs text-slate-400">Completed</span>
                    @endif
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="8" icon="person-badge" :title="app()->getLocale() == 'km' ? 'មិនមានការប្រគល់ទ្រព្យសកម្មទេ' : 'No Asset Assignments Found'" :description="app()->getLocale() == 'km' ? 'ប្រគល់ឧបករណ៍ ឬទ្រព្យសកម្មទៅឱ្យបុគ្គលិកប្រើប្រាស់' : 'Assign assets to staff members to track custody.'" :actionUrl="route('assets.assignments.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
