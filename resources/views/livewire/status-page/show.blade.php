<div>
    <div class="max-w-2xl mx-auto my-4 px-2">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-neutral-100 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                @if($statusPage->logo_url)
                    <img src="{{ Storage::url($statusPage->logo_url) }}" alt="{{ $statusPage->name }}" class="h-8">
                @else
                    <h1 class="text-xl font-bold dark:text-white">{{ $statusPage->name }}</h1>
                @endif
            </div>
        </div>
    </div>

    <livewire:status-page.overall-status :statusPage="$statusPage" />

    <div class="max-w-2xl mx-auto flex flex-col gap-2 my-4 px-2">
        @foreach($statusPage->items()->with('monitor')->orderBy('order')->get() as $item)
            <livewire:status-page.monitor-status :item="$item" :key="$item->id" />
        @endforeach
    </div>
</div>
