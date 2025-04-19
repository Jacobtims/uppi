<div wire:poll.30s class="bg-white rounded-xl shadow-sm border border-neutral-100 p-7 relative overflow-hidden">
    <!-- Subtle pattern background -->
    @if($isOperational)
        <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(#22c55e_1px,transparent_1px)] filter blur-lg [background-size:24px_24px]"></div>
        <div class="absolute right-0 top-0 h-20 w-40 bg-gradient-to-bl from-green-50 to-transparent rounded-bl-[100px]"></div>
        <div class="absolute right-1/4 bottom-1/3 h-16 w-16 bg-gradient-to-tl from-green-50/70 to-transparent rounded-full blur-md"></div>
    @else
        <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(#ef4444_1px,transparent_1px)] filter blur-lg [background-size:16px_16px]"></div>
        <div class="absolute right-0 top-0 h-20 w-40 bg-gradient-to-bl from-red-50 to-transparent rounded-bl-[100px]"></div>
        <div class="absolute left-0 bottom-0 h-24 w-32 bg-gradient-to-tr from-red-50 to-transparent rounded-tr-[80px]"></div>
        <div class="absolute left-1/3 top-1/4 h-16 w-16 bg-gradient-to-br from-red-50/70 to-transparent rounded-full blur-md"></div>
    @endif

    <div class="flex flex-col items-center py-8 relative z-10">
        @if($isOperational)
            <div class="bg-green-50 rounded-full p-5 mb-5 shadow-inner shadow-green-100">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-14 text-green-600">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="px-4 py-2 bg-green-50 rounded-full text-green-600 text-sm font-medium mb-4">All Systems Operational</div>
            <h1 class="text-xl font-semibold text-neutral-800 mb-3">Everything is working properly</h1>
            <p class="text-neutral-500 max-w-md text-center">All services are running optimally with no detected issues</p>
        @else
            <div class="bg-red-50 rounded-full p-5 mb-5 shadow-inner shadow-red-100">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-14 text-red-600">
                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="px-4 py-2 bg-red-50 rounded-full text-red-600 text-sm font-medium mb-4">System Disruptions Detected</div>
            <h1 class="text-xl font-semibold text-neutral-800 mb-3">Some services are affected</h1>
            <p class="text-neutral-500 max-w-md text-center">You may experience issues with some of our services</p>
        @endif
    </div>
</div>
