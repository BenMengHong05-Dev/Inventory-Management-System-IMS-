@extends('layouts.app')

@section('title', $employee->name . ' - Employee Profile - IMS')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-800">{{ $employee->name }}</h1>
                @if($employee->status == 'active')
                    <span class="badge bg-emerald-100 text-emerald-800">Active</span>
                @else
                    <span class="badge bg-slate-100 text-slate-700">Inactive</span>
                @endif
            </div>
            <p class="text-sm text-slate-500 font-mono">ID: {{ $employee->code }} | {{ $employee->department_name ?: 'No Department' }} &bull; {{ $employee->designation ?: 'Staff' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('assets.assignments.create', ['employee_id' => $employee->id]) }}" class="btn-primary">
                <i class="bi bi-laptop"></i> Assign Asset
            </a>
            <a href="{{ route('master.employees.edit', $employee->id) }}" class="btn-secondary">
                <i class="bi bi-pencil"></i> Edit Profile
            </a>
            <a href="{{ route('master.employees.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Employees
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contact Card -->
        <div class="glass-card p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3">Contact & Department</h2>

            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Department</span>
                    <span class="font-medium text-slate-700">{{ $employee->department_name ?: 'Unassigned' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Job Designation</span>
                    <span class="font-medium text-slate-700">{{ $employee->designation ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Email Address</span>
                    <span class="font-medium text-slate-700">{{ $employee->email ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Phone</span>
                    <span class="font-medium text-slate-700">{{ $employee->phone ?: '-' }}</span>
                </div>
                @if($employee->notes)
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-xs text-slate-400 block mb-1">Notes</span>
                    <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded">{{ $employee->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Assignments & Returns Tables -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Active & Past Assignments -->
            <div class="glass-card overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-800">Assigned Equipment & Assets ({{ count($assignments) }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Asset Tag & Name</th>
                                <th>Assigned Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $a)
                            <tr>
                                <td>
                                    <a href="{{ route('assets.show', $a->asset_id) }}" class="font-bold text-indigo-600 hover:text-indigo-800">
                                        {{ $a->asset_tag }}
                                    </a>
                                    <div class="text-xs text-slate-500">{{ $a->asset_name }}</div>
                                </td>
                                <td class="text-xs text-slate-600">{{ date('M d, Y', strtotime($a->assigned_date)) }}</td>
                                <td class="text-xs text-slate-600">{{ $a->due_date ? date('M d, Y', strtotime($a->due_date)) : 'Indefinite' }}</td>
                                <td>
                                    @if($a->status == 'active')
                                        <span class="badge bg-blue-100 text-blue-800">In Possession</span>
                                    @else
                                        <span class="badge bg-slate-100 text-slate-700">Returned</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($a->status == 'active')
                                        <a href="{{ route('assets.returns.create', ['assignment_id' => $a->id]) }}" class="btn-secondary text-xs px-2 py-1 text-indigo-600">
                                            Return
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-xs text-slate-400">No assets currently or previously assigned.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Returns History -->
            <div class="glass-card overflow-hidden">
                <div class="p-4 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-800">Returned Equipment History ({{ count($returns) }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Return Date</th>
                                <th>Condition</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returns as $r)
                            <tr>
                                <td>
                                    <a href="{{ route('assets.show', $r->asset_id) }}" class="font-bold text-indigo-600 hover:text-indigo-800">
                                        {{ $r->asset_tag }}
                                    </a>
                                </td>
                                <td class="text-xs text-slate-600">{{ date('M d, Y', strtotime($r->return_date)) }}</td>
                                <td>
                                    <span class="badge bg-slate-100 text-slate-700 text-[10px] uppercase">{{ $r->condition }}</span>
                                </td>
                                <td class="text-xs text-slate-500">{{ $r->notes ?: '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-xs text-slate-400">No returned items recorded.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
