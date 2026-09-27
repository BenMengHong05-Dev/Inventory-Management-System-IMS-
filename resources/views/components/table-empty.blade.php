@props([
    'colspan' => 10,
    'icon' => 'package-x',
    'title' => null,
    'description' => null,
    'action' => null,
    'actionUrl' => null,
    'actionLabel' => null,
])

<tr>
    <td colspan="{{ $colspan }}" class="text-center py-12">
        <div class="w-14 h-14 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
            @if(str_starts_with($icon, 'bi ') || str_starts_with($icon, 'bi-'))
                <i class="{{ str_starts_with($icon, 'bi-') ? 'bi ' . $icon : $icon }} text-2xl"></i>
            @else
                <i data-lucide="{{ $icon }}" class="w-7 h-7"></i>
            @endif
        </div>
        <h4 class="text-sm font-bold text-slate-800">
            {{ $title ?? (app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យនៅឡើយទេ' : 'No Data Available') }}
        </h4>
        @if($description)
            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 mb-4">{{ $description }}</p>
        @endif
        @if($action)
            <div class="mt-3">
                {{ $action }}
            </div>
        @elseif($actionUrl && $actionLabel)
            <div class="mt-3">
                <a href="{{ $actionUrl }}" class="btn-primary">
                    <i class="bi bi-plus-lg mr-1.5"></i>
                    {{ $actionLabel }}
                </a>
            </div>
        @endif
    </td>
</tr>
