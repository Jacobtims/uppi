<div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6" wire:poll.30s id="msts-{{ $item->id }}">
    <div class="flex items-center gap-3 mb-6">
        @if($item->is_showing_favicon && $item->is_enabled)
            <img src="{{ URL::signedRoute('icon', ['statusPageItem' => $item]) }}"
                 alt="{{ $item->name }}"
                 class="w-6 h-6 object-contain">
        @endif
        <p class="font-medium text-neutral-900 text-base">{{ $item->name }}</p>
        <div class="flex items-center gap-2 ml-auto">
            <span @class([
                'flex items-center justify-center rounded-full w-3 h-3',
                'bg-green-500' => $item->monitor->status === \App\Enums\Checks\Status::OK,
                'bg-red-500' => $item->monitor->status === \App\Enums\Checks\Status::FAIL,
                'bg-yellow-500' => $item->monitor->status === \App\Enums\Checks\Status::UNKNOWN,
            ])></span>
            <span @class([
                'text-sm font-medium',
                'text-green-600' => $item->monitor->status === \App\Enums\Checks\Status::OK,
                'text-red-600' => $item->monitor->status === \App\Enums\Checks\Status::FAIL,
                'text-yellow-600' => $item->monitor->status === \App\Enums\Checks\Status::UNKNOWN,
            ])>{{ $item->monitor->status->label() }}</span>
        </div>
    </div>
    <div class="grid grid-flow-col justify-stretch gap-2">
        @foreach($dates as $index => $date)
            <div
                x-data="{ open: false }"
                @mouseenter="open = true"
                @mouseleave="open = false"
                @class([
                    'relative h-8 rounded-lg',
                    'bg-green-100 border border-green-200' => $statuses[$index] === true,
                    'bg-red-100 border border-red-200' => $statuses[$index] === false,
                    'bg-neutral-100 border border-neutral-200' => $statuses[$index] === null,
                ])
            >
                <div class="h-full flex items-end">
                    @if($statuses[$index] === true)
                        <div class="w-full h-full bg-green-500 rounded-lg opacity-60"></div>
                    @elseif($statuses[$index] === false)
                        <div class="w-full h-full bg-red-500 rounded-lg opacity-60"></div>
                    @endif
                </div>
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 px-3 py-2 rounded-lg bg-neutral-800 text-white text-xs whitespace-nowrap z-10 shadow-lg"
                >
                    <div class="font-medium mb-0.5">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</div>
                    <div>{{ $statuses[$index] === true ? '✓ Operational' : ($statuses[$index] === false ? '✕ Disruption' : 'No data available') }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
