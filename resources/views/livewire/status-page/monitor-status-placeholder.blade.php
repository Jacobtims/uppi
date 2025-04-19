<div class="animate-pulse bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <div class="h-5 bg-neutral-200 rounded-lg w-36"></div>
            <div class="h-5 bg-neutral-200 rounded-lg w-24"></div>
        </div>
        <div class="grid grid-cols-30 gap-2">
            @php
                $totalDays = 30;
                $lastSevenDaysStart = $totalDays - 7;
                $lastFourteenDaysStart = $totalDays - 14;
            @endphp
            
            @for ($i = 0; $i < $totalDays; $i++)
                <div @class([
                    'bg-neutral-200 h-8 rounded-lg',
                    'hidden lg:block' => $i < 0,
                    'hidden md:block lg:block' => $i < $lastFourteenDaysStart && $i >= 0,
                    'hidden sm:block md:block lg:block' => $i < $lastSevenDaysStart && $i >= $lastFourteenDaysStart,
                ])></div>
            @endfor
        </div>
        <div class="mt-1 flex justify-end">
            <div class="h-4 bg-neutral-200 rounded w-28"></div>
        </div>
    </div>
</div>
