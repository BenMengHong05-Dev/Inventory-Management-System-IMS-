@props([
    'headers' => [],
    'title' => null,
    'subtitle' => null,
    'badge' => null,
    'search' => null,
    'filters' => null,
    'actions' => null,
    'pagination' => null,
    'tableClass' => 'data-table'
])

<div {{ $attributes->merge(['class' => 'glass-card overflow-hidden']) }}>
    @if($title || isset($header) || isset($actions) || isset($search) || isset($filters))
        <div class="px-5 py-4 flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-200/70 bg-white/70 backdrop-blur-md">
            <!-- Left Side: Title, Subtitle, Badge, and Search Input -->
            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                @if($title)
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-bold text-slate-900 tracking-tight">{{ $title }}</h2>
                        @if($badge)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100/80 text-slate-600 border border-slate-200/60">{{ $badge }}</span>
                        @endif
                    </div>
                @endif
                @if($subtitle)
                    <span class="text-[11px] text-slate-400 font-medium">{{ $subtitle }}</span>
                @endif
                {{ $header ?? '' }}

                @if(isset($search))
                    <div class="relative">
                        {{ $search }}
                    </div>
                @endif
            </div>

            <!-- Right Side: Button Filters & Actions -->
            @if(isset($filters) || isset($actions))
                <div class="flex flex-wrap items-center gap-2 justify-end">
                    {{ $filters ?? '' }}
                    {{ $actions ?? '' }}
                </div>
            @endif
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="{{ $tableClass }} w-full">
            <thead>
                <tr>
                    @if(empty($headers) && isset($head))
                        {{ $head }}
                    @else
                        @foreach($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100/80">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if(isset($pagination) && $pagination)
        @if(is_object($pagination) && method_exists($pagination, 'total'))
            @if($pagination->total() > 0)
                <div class="px-5 py-3 border-t border-slate-200/70 bg-white/60 backdrop-blur-sm flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                    <div>
                        {{ app()->getLocale() == 'km' ? 'បង្ហាញ' : 'Showing' }} 
                        <span class="font-bold text-slate-700">{{ $pagination->firstItem() ?? 1 }}</span>
                        {{ app()->getLocale() == 'km' ? 'ដល់' : 'to' }}
                        <span class="font-bold text-slate-700">{{ $pagination->lastItem() ?? $pagination->count() }}</span>
                        {{ app()->getLocale() == 'km' ? 'នៃ' : 'of' }}
                        <span class="font-bold text-slate-700">{{ $pagination->total() }}</span>
                        {{ app()->getLocale() == 'km' ? 'ទិន្នន័យ' : 'entries' }}
                    </div>
                    @if($pagination->hasPages())
                        <div>
                            {{ $pagination->links() }}
                        </div>
                    @endif
                </div>
            @endif
        @elseif(!empty($pagination))
            <div class="px-5 py-3 border-t border-slate-200/70 bg-white/60 backdrop-blur-sm">
                {{ $pagination }}
            </div>
        @endif
    @endif
</div>
