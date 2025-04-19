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
                'bg-green-600' => $item->monitor->status === \App\Enums\Checks\Status::OK,
                'bg-red-600' => $item->monitor->status === \App\Enums\Checks\Status::FAIL,
                'bg-yellow-600' => $item->monitor->status === \App\Enums\Checks\Status::UNKNOWN,
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
        @php
            $daysCount = count($dates);
            $lastSevenDaysStart = $daysCount - 7;
            $lastFourteenDaysStart = $daysCount - 14;
            $lastThirtyDaysStart = $daysCount - 30;
        @endphp
        
        @foreach($dates as $index => $date)
            <div
                @class([
                    'relative h-8 rounded-lg',
                    'hidden lg:block' => $index < $lastThirtyDaysStart,
                    'hidden md:block lg:block' => $index < $lastFourteenDaysStart && $index >= $lastThirtyDaysStart,
                    'hidden sm:block md:block lg:block' => $index < $lastSevenDaysStart && $index >= $lastFourteenDaysStart,
                    'bg-green-100 border border-green-200' => $statuses[$index] === true,
                    'bg-red-100 border border-red-200' => $statuses[$index] === false,
                    'bg-neutral-100 border border-neutral-200' => $statuses[$index] === null,
                ])
                x-data="{ open: false }"
                @mouseenter="open = true"
                @mouseleave="open = false"
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
    <div class="mt-3 text-xs text-neutral-500 flex justify-between">
        <span class="sm:hidden">Last 7 days</span>
        <span class="hidden sm:block md:hidden">Last 14 days</span>
        <span class="hidden md:block lg:hidden">Last 30 days</span>
        <span class="hidden lg:block">Last 30 days</span>
        <span>{{ \Carbon\Carbon::now()->subDays(29)->format('M j') }} - {{ \Carbon\Carbon::now()->format('M j') }}</span>
    </div>
</div>
