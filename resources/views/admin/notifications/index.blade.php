@extends('layouts.app')

@section('title', t('notifications') . ' - IMS')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/20">
                <i class="bi bi-bell text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">{{ t('notifications') }}</h1>
                <p class="text-xs sm:text-sm text-slate-500">{{ t('recent_alerts') }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.notifications.mark_all_read') }}">
            @csrf
            <button type="submit" class="btn-secondary text-xs flex items-center gap-1.5">
                <i class="bi bi-check2-all"></i> {{ t('mark_all_read') ?? 'Mark All as Read' }}
            </button>
        </form>
    </div>

    <div class="card divide-y divide-slate-100 overflow-hidden">
        @forelse($notifications as $n)
        <div class="p-4 flex items-start justify-between gap-4 transition hover:bg-slate-50/50 {{ $n->is_read ? 'bg-white' : 'bg-indigo-50/20' }}">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-lg shrink-0 {{ $n->is_read ? 'bg-slate-100 text-slate-500' : 'bg-indigo-100 text-indigo-600' }}">
                    @if($n->type == 'low_stock')
                        <i class="bi bi-exclamation-triangle text-amber-500"></i>
                    @elseif($n->type == 'purchase_order')
                        <i class="bi bi-cart text-blue-500"></i>
                    @elseif($n->type == 'maintenance')
                        <i class="bi bi-wrench text-rose-500"></i>
                    @else
                        <i class="bi bi-bell text-indigo-500"></i>
                    @endif
                </div>
                <div>
                    <div class="font-bold text-sm text-slate-800">{{ $n->title }}</div>
                    <div class="text-xs text-slate-600 mt-0.5">{{ $n->message }}</div>
                    <div class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-2">
                        <span><i class="bi bi-clock"></i> {{ date('Y-m-d H:i', strtotime($n->created_at)) }}</span>
                        @if($n->link)
                            <a href="{{ $n->link }}" class="text-indigo-600 hover:underline font-semibold flex items-center gap-1">
                                {{ t('view_details') }} &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @if(!$n->is_read)
            <form method="POST" action="{{ route('admin.notifications.mark_read', $n->id) }}">
                @csrf
                <button type="submit" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition" title="{{ t('mark_read') ?? 'Mark as Read' }}">
                    <i class="bi bi-check-lg text-lg"></i>
                </button>
            </form>
            @endif
        </div>
        @empty
        <div class="p-10 text-center text-slate-400">
            <i class="bi bi-bell-slash text-3xl mb-2 block text-slate-300"></i>
            {{ t('no_notifications') }}
        </div>
        @endforelse
    </div>

    @if(method_exists($notifications, 'links'))
    <div class="p-4 bg-white rounded-xl border border-slate-100 shadow-sm">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
