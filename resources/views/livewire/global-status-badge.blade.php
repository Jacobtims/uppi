<a class="flex items-center gap-1.5 w-full" href="{{ \App\Filament\Resources\MonitorResource::getUrl() }}">
    @if($isOk)
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="w-6 h-6 text-green-500 flex-shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>

        <span class="text-zinc-600 text-sm ">All monitors are okay</span>
    @else
        <div class="relative">
            <div
                class="animate-ping absolute -top-1 -right-1 -bottom-1 -left-1 bg-red-500 bg-opacity-60 rounded-full">
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="w-8 h-8 text-red-500 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
            </svg>
        </div>
        <div>
            <div class="text-zinc-600 text-xs ">Some monitors are currently experiencing issues</div>
        </div>
    @endif
</a>
