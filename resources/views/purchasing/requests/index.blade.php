@extends('layouts.app')

@section('title', t('messages.purchase_requests'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('messages.purchase_requests') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'សំណើទិញផ្ទៃក្នុង ដំណើរការអនុម័ត និងតាមដានស្ថានភាព' : 'Internal procurement requests, approval workflows, and status tracking' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('purchase-requests.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('messages.create') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('purchase-requests.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search }}" placeholder="{{ t('messages.search') }} #..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-40">
                <x-select name="status" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.status') }}</option>
                    <option value="PENDING" {{ $status === 'PENDING' ? 'selected' : '' }}>{{ t('messages.pending') }}</option>
                    <option value="APPROVED" {{ $status === 'APPROVED' ? 'selected' : '' }}>{{ t('messages.approved') }}</option>
                    <option value="REJECTED" {{ $status === 'REJECTED' ? 'selected' : '' }}>{{ t('messages.rejected') }}</option>
                    <option value="COMPLETED" {{ $status === 'COMPLETED' ? 'selected' : '' }}>{{ t('messages.completed') }}</option>
                </x-select>
            </div>
            <div class="w-full sm:w-40">
                <x-select name="priority" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ app()->getLocale() == 'km' ? 'កម្រិតអាទិភាព' : 'Priorities' }}</option>
                    <option value="URGENT" {{ $priority === 'URGENT' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បន្ទាន់បំផុត' : 'Urgent' }}</option>
                    <option value="HIGH" {{ $priority === 'HIGH' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ខ្ពស់' : 'High' }}</option>
                    <option value="MEDIUM" {{ $priority === 'MEDIUM' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'មធ្យម' : 'Medium' }}</option>
                    <option value="LOW" {{ $priority === 'LOW' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ទាប' : 'Low' }}</option>
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('messages.filter') }}
            </x-button>
            <a href="{{ route('purchase-requests.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$purchaseRequests">
        <x-slot name="head">
            <th>{{ t('messages.purchase_requests') }} #</th>
            <th>{{ t('messages.date') }}</th>
            <th>{{ t('messages.department') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'អ្នកស្នើសុំ' : 'Requester' }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'អាទិភាព' : 'Priority' }}</th>
            <th class="text-right">{{ t('messages.items') }}</th>
            <th class="text-right">{{ app()->getLocale() == 'km' ? 'តម្លៃប៉ាន់ស្មាន' : 'Estimated Total' }}</th>
            <th>{{ t('messages.status') }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($purchaseRequests as $pr)
            <tr>
                <td class="font-mono font-bold text-slate-900">
                    <a href="{{ route('purchase-requests.show', $pr->id) }}" class="text-sky-600 hover:text-sky-700 transition">
                        {{ $pr->request_number }}
                    </a>
                </td>
                <td class="text-slate-600 whitespace-nowrap">{{ $pr->request_date }}</td>
                <td class="text-slate-800 font-semibold">{{ $pr->department_name ?? (app()->getLocale() == 'km' ? 'ទូទៅ' : 'General') }}</td>
                <td class="text-slate-700 font-medium">{{ $pr->requester_name }}</td>
                <td>
                    @if($pr->priority === 'URGENT')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                            {{ $pr->priority }}
                        </span>
                    @elseif($pr->priority === 'HIGH')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $pr->priority }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $pr->priority }}
                        </span>
                    @endif
                </td>
                <td class="text-right font-bold text-slate-800">{{ $pr->total_items }}</td>
                <td class="text-right font-bold text-slate-900">{{ format_currency($pr->estimated_total) }}</td>
                <td>
                    @if($pr->status === 'APPROVED')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $pr->status }}
                        </span>
                    @elseif($pr->status === 'COMPLETED')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-500/10 text-purple-700 border border-purple-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                            {{ $pr->status }}
                        </span>
                    @elseif($pr->status === 'REJECTED')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            {{ $pr->status }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $pr->status }}
                        </span>
                    @endif
                </td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('purchase-requests.show', $pr->id) }}" class="btn-action btn-action-view" title="View & Process">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="9" icon="file-earmark-text" :title="app()->getLocale() == 'km' ? 'មិនមានសំណើទិញទេ' : 'No Purchase Requests Found'" :description="app()->getLocale() == 'km' ? 'បង្កើតសំណើទិញទំនិញថ្មីសម្រាប់នាយកដ្ឋានរបស់អ្នក' : 'Submit a purchase requisition for management review.'" :actionUrl="route('purchase-requests.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
