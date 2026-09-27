@extends('layouts.app')

@section('title', 'Maintenance Record #' . $maintenance->id . ' - IMS')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-800">{{ $maintenance->title }}</h1>
                @if($maintenance->status == 'completed')
                    <span class="badge bg-emerald-100 text-emerald-800">Completed</span>
                @elseif($maintenance->status == 'in_progress')
                    <span class="badge bg-blue-100 text-blue-800">In Progress</span>
                @elseif($maintenance->status == 'pending')
                    <span class="badge bg-amber-100 text-amber-800">Pending</span>
                @else
                    <span class="badge bg-slate-100 text-slate-700">{{ ucfirst($maintenance->status) }}</span>
                @endif
            </div>
            <p class="text-sm text-slate-500">Asset: <a href="{{ route('assets.show', $maintenance->asset_id) }}" class="text-indigo-600 font-semibold">{{ $maintenance->asset_tag }} ({{ $maintenance->asset_name }})</a></p>
        </div>
        <a href="{{ route('assets.maintenance.index') }}" class="btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Maintenance
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card p-6 space-y-4">
                <h2 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Maintenance Details</h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-slate-400 block text-xs">Type</span>
                        <span class="font-semibold text-slate-700 uppercase">{{ str_replace('_', ' ', $maintenance->maintenance_type) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs">Priority</span>
                        <span class="font-semibold text-slate-700 uppercase">{{ $maintenance->priority }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs">Service Provider</span>
                        <span class="font-semibold text-slate-700">{{ $maintenance->supplier_name ?: 'Internal Technician' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs">Start Date</span>
                        <span class="font-semibold text-slate-700">{{ $maintenance->start_date ? date('M d, Y', strtotime($maintenance->start_date)) : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs">Expected Completion</span>
                        <span class="font-semibold text-slate-700">{{ $maintenance->expected_completion_date ? date('M d, Y', strtotime($maintenance->expected_completion_date)) : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs">Estimated Cost</span>
                        <span class="font-semibold text-slate-700 font-mono">{{ $maintenance->cost ? format_currency($maintenance->cost) : '-' }}</span>
                    </div>
                </div>

                @if($maintenance->description)
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-slate-400 block text-xs mb-1">Description / Reported Issue</span>
                    <p class="text-sm text-slate-700 bg-slate-50 p-3 rounded-lg">{{ $maintenance->description }}</p>
                </div>
                @endif

                @if($maintenance->status == 'completed')
                <div class="pt-3 border-t border-slate-100 bg-emerald-50/50 p-4 rounded-lg space-y-2">
                    <h3 class="text-sm font-bold text-emerald-800">Completion Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400">Actual Completion Date:</span>
                            <span class="font-semibold text-slate-700 ml-1">{{ $maintenance->actual_completion_date ? date('M d, Y', strtotime($maintenance->actual_completion_date)) : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400">Actual Cost:</span>
                            <span class="font-semibold text-slate-700 font-mono ml-1">{{ $maintenance->actual_cost ? format_currency($maintenance->actual_cost) : '-' }}</span>
                        </div>
                    </div>
                    @if($maintenance->resolution_notes)
                    <div class="text-xs pt-1">
                        <span class="text-slate-400 block">Resolution Notes:</span>
                        <p class="text-slate-700 mt-0.5">{{ $maintenance->resolution_notes }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Status History -->
            <div class="glass-card p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Status & Action Audit History</h2>
                <div class="space-y-4">
                    @forelse($history as $h)
                    <div class="flex items-start gap-3 text-sm pb-3 border-b border-slate-100 last:border-0 last:pb-0">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 mt-2"></div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-slate-700">{{ ucfirst($h->from_status) }} &rarr; {{ ucfirst($h->to_status) }}</span>
                                <span class="text-xs text-slate-400">{{ date('M d, Y H:i', strtotime($h->created_at)) }}</span>
                            </div>
                            <div class="text-xs text-slate-500">By {{ $h->user_name }}</div>
                            @if($h->comment)
                            <div class="text-xs text-slate-600 mt-1 italic">"{{ $h->comment }}"</div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400">No status changes recorded.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Update Status Action Panel -->
        <div class="space-y-6">
            @if($maintenance->status != 'completed' && $maintenance->status != 'cancelled')
            <form method="POST" action="{{ route('assets.maintenance.update_status', $maintenance->id) }}" class="glass-card p-6 space-y-4">
                @csrf
                <h2 class="text-base font-bold text-slate-800">Update Maintenance Status</h2>

                <div>
                    <label class="form-label required">Status</label>
                    <select name="status" class="form-input" required>
                        @if($maintenance->status == 'pending')
                            <option value="in_progress">Start Maintenance (In Progress)</option>
                            <option value="completed">Mark as Completed</option>
                            <option value="cancelled">Cancel Maintenance</option>
                        @elseif($maintenance->status == 'in_progress')
                            <option value="completed">Mark as Completed</option>
                            <option value="cancelled">Cancel Maintenance</option>
                        @endif
                    </select>
                </div>

                <div>
                    <label class="form-label">Actual Completion Date</label>
                    <input type="date" name="actual_completion_date" value="{{ date('Y-m-d') }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Actual Cost ($)</label>
                    <input type="number" step="0.01" name="actual_cost" value="{{ $maintenance->cost }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Performed By</label>
                    <input type="text" name="performed_by" class="form-input" placeholder="Technician / Engineer name">
                </div>

                <div>
                    <label class="form-label">Resolution / Work Done</label>
                    <textarea name="resolution_notes" rows="3" class="form-input" placeholder="Details of repair or replacement components..."></textarea>
                </div>

                <button type="submit" class="btn-primary w-full justify-center">
                    <i class="bi bi-arrow-repeat"></i> Update Status
                </button>
            </form>
            @else
            <div class="glass-card p-6 text-center text-slate-500">
                <i class="bi bi-check-circle-fill text-3xl text-emerald-500 mb-2 block"></i>
                <p class="font-semibold text-slate-700">Maintenance is {{ ucfirst($maintenance->status) }}</p>
                <p class="text-xs text-slate-400 mt-1">This ticket is closed and can no longer be updated.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
