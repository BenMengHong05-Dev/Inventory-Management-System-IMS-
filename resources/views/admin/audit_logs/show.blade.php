@extends('layouts.app')

@section('title', 'Audit Log #' . $log->id . ' - IMS')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Audit Log Record #{{ $log->id }}</h1>
            <p class="text-sm text-slate-500">Immutable trace details and property mutation payload</p>
        </div>
        <a href="{{ route('admin.audit_logs.index') }}" class="btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Logs
        </a>
    </div>

    <div class="glass-card p-6 space-y-6">
        <!-- Metadata -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs text-slate-400 block">Timestamp</span>
                <span class="font-semibold text-slate-700">{{ date('M d, Y H:i:s', strtotime($log->created_at)) }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Operator / User</span>
                <span class="font-semibold text-slate-700">{{ $log->user_name ?: 'System' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Action</span>
                <span class="font-semibold text-slate-700 uppercase">{{ $log->action }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Module</span>
                <span class="font-semibold text-slate-700 uppercase">{{ $log->module }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Record ID</span>
                <span class="font-mono text-slate-700">{{ $log->record_id ?: 'N/A' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">IP Address</span>
                <span class="font-mono text-slate-700">{{ $log->ip_address ?: '-' }}</span>
            </div>
        </div>

        <!-- Mutation Payloads -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-700 mb-2">Previous Values (Before Mutation)</h3>
                <pre class="bg-slate-900 text-slate-200 p-3 rounded-lg text-xs font-mono overflow-x-auto max-h-80">{{ $log->old_values ? json_encode(json_decode($log->old_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : 'None (Record Creation)' }}</pre>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-700 mb-2">New Values (After Mutation)</h3>
                <pre class="bg-slate-900 text-slate-200 p-3 rounded-lg text-xs font-mono overflow-x-auto max-h-80">{{ $log->new_values ? json_encode(json_decode($log->new_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : 'None (Record Deletion)' }}</pre>
            </div>
        </div>

        @if($log->user_agent)
        <div class="pt-4 border-t border-slate-100 text-xs text-slate-400">
            <span class="font-medium text-slate-500">Client User Agent:</span> {{ $log->user_agent }}
        </div>
        @endif
    </div>
</div>
@endsection
