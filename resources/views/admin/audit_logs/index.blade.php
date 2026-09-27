@extends('layouts.app')

@section('title', t('audit_logs') . ' - IMS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('audit_logs') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'កំណត់ត្រាសវនកម្ម និងប្រវត្តិនៃការផ្លាស់ប្តូរទិន្នន័យប្រព័ន្ធដោយអ្នកប្រើប្រាស់' : 'System-wide audit trail recording user activities and data changes' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <form method="POST" action="{{ route('audit-logs.clear') }}" onsubmit="return confirm('{{ app()->getLocale() == 'km' ? 'តើអ្នកប្រាកដថាចង់លុបកំណត់ត្រាសកម្មភាពទាំងអស់ចេញពីប្រព័ន្ធមែនទេ?' : 'Are you sure you want to permanently delete all audit log records?' }}');">
                @csrf
                <button type="submit" class="btn-action btn-action-delete !w-auto !px-3.5 !py-2 !rounded-xl !gap-1.5 flex items-center text-xs font-semibold">
                    <i class="bi bi-trash"></i>
                    <span>{{ app()->getLocale() == 'km' ? 'លុបកំណត់ត្រាទាំងអស់' : 'Clear All Logs' }}</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('admin.audit_logs.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ t('search') }}..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-44">
                <x-select name="action" wrapperClass="!mb-0">
                    <option value="">{{ t('all') }} {{ t('actions') }}</option>
                    @foreach($actions as $a)
                        <option value="{{ $a->action }}" {{ ($action ?? '') == $a->action ? 'selected' : '' }}>{{ strtoupper($a->action) }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full sm:w-44">
                <x-select name="module" wrapperClass="!mb-0">
                    <option value="">{{ t('all') }} Modules</option>
                    @foreach($modules as $m)
                        <option value="{{ $m->module }}" {{ ($module ?? '') == $m->module ? 'selected' : '' }}>{{ $m->module }}</option>
                    @endforeach
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('filter') }}
            </x-button>
            <a href="{{ route('admin.audit_logs.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$auditLogs">
        <x-slot name="head">
            <th>{{ t('date') }}</th>
            <th>{{ t('users') }}</th>
            <th>{{ t('actions') }}</th>
            <th>Module</th>
            <th>Record ID</th>
            <th>IP Address</th>
            <th class="text-right">{{ t('view_details') }}</th>
        </x-slot>

        @forelse($auditLogs as $l)
            <tr>
                <td class="text-xs text-slate-500 whitespace-nowrap font-mono">
                    {{ date('Y-m-d H:i:s', strtotime($l->created_at)) }}
                </td>
                <td>
                    <div class="font-bold text-slate-900 text-xs">{{ $l->user_name ?: 'System' }}</div>
                    <div class="text-[11px] text-slate-400 font-mono">{{ $l->user_email ?: '' }}</div>
                </td>
                <td>
                    @php $act = strtolower($l->action); @endphp
                    @if($act == 'create')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">CREATE</span>
                    @elseif($act == 'update')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-500/10 text-sky-700 border border-sky-500/20">UPDATE</span>
                    @elseif($act == 'delete')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-700 border border-rose-500/20">DELETE</span>
                    @elseif($act == 'approve')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-500/10 text-purple-700 border border-purple-500/20">APPROVE</span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">{{ strtoupper($l->action) }}</span>
                    @endif
                </td>
                <td class="text-xs font-semibold text-slate-800">{{ $l->module }}</td>
                <td class="text-xs font-mono text-slate-600">{{ $l->record_id ?: '-' }}</td>
                <td class="text-xs font-mono text-slate-500">{{ $l->ip_address ?: '-' }}</td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('admin.audit_logs.show', $l->id) }}" class="btn-action btn-action-view" title="{{ t('view') }}">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="7" icon="clock-history" :title="app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាសវនកម្មទេ' : 'No Audit Logs Recorded'" :description="app()->getLocale() == 'km' ? 'រាល់សកម្មភាពប្រព័ន្ធនឹងត្រូវបានកត់ត្រានៅទីនេះ' : 'System security and modification activities will be captured here.'" />
        @endforelse
    </x-table>
</div>
@endsection
