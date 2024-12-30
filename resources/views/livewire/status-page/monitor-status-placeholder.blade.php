<div class="animate-pulse">
    <div class="flex flex-col space-y-2">
        <div class="flex items-center justify-between">
            <div class="h-5 bg-gray-200 rounded w-32"></div>
            <div class="h-5 bg-gray-200 rounded w-24"></div>
        </div>
        <div class="grid grid-cols-30 gap-1">
            @for ($i = 0; $i < 30; $i++)
                <div class="aspect-square bg-gray-200 rounded shadow-sm border border-gray-300"></div>
            @endfor
        </div>
    </div>
</div>
