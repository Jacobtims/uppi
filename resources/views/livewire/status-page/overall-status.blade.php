<div wire:poll.30s class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-7">
    <div class="flex flex-col items-center py-8">
        @if($isOperational)
            <div class="bg-green-50 rounded-full p-5 mb-5 shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-14 text-green-600">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                </svg>
            </div>
            <h1 class="text-2xl font-medium text-neutral-900">All services healthy</h1>
            <p class="mt-2 text-neutral-500">All services are operational and performing as expected</p>
        @else
            <div class="bg-red-50 rounded-full p-5 mb-5 shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-14 text-red-600">
                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" />
                </svg>
            </div>
            <h1 class="text-2xl font-medium text-neutral-900">Some services are experiencing disruptions</h1>
            <p class="mt-2 text-neutral-500">During this time, you may experience degraded service or intermittent issues from some of our services</p>
        @endif
    </div>
</div>
