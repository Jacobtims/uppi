<div class="animate-pulse bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <div class="h-5 bg-neutral-200 rounded-lg w-36"></div>
            <div class="h-5 bg-neutral-200 rounded-lg w-24"></div>
        </div>
        <div class="grid grid-cols-30 gap-2">
            @for ($i = 0; $i < 30; $i++)
                <div class="bg-neutral-200 h-8 rounded-lg"></div>
            @endfor
        </div>
    </div>
</div>
