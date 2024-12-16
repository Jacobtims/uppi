<div class="bg-white shadow rounded-lg border border-neutral-100 p-4" wire:poll.30s>
    <div class="flex items-center gap-2 mb-4">
        @if($item->is_showing_favicon && $item->is_enabled)
            <img src="{{ URL::signedRoute('icon', ['statusPageItem' => $item]) }}"
                 alt="{{ $item->name }}"
                 class="w-6">
        @endif
        <p class="font-bold">{{ $item->name }}</p>
        <div class="flex items-center gap-1 ml-auto text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" @class([
                'size-5',
                'text-green-500' => $item->monitor->status === \App\Enums\Checks\Status::OK,
                'text-red-500 rotate-180' => $item->monitor->status === \App\Enums\Checks\Status::FAIL,
                'text-yellow-500' => $item->monitor->status === \App\Enums\Checks\Status::UNKNOWN,
            ])>
                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm.53 5.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72v5.69a.75.75 0 0 0 1.5 0v-5.69l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z" clip-rule="evenodd" />
            </svg>
            {{ $item->monitor->status->label() }}
        </div>
    </div>
    <div class="grid grid-flow-col justify-stretch gap-1">
        @foreach($dates as $index => $date)
            <div @class([
                    'h-8 rounded cursor-help',
                    'bg-green-500' => $statuses[$index] === true,
                    'bg-red-500' => $statuses[$index] === false,
                    'bg-gray-200' => $statuses[$index] === null,
                ])
                data-tippy-content="<strong>{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</strong><br>{{ $statuses[$index] === true ? '✓ Operational' : ($statuses[$index] === false ? '✕ Down' : 'No data for this day') }}"
            ></div>
        @endforeach
    </div>
</div>
