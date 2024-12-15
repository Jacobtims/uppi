<div>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow rounded-lg border border-neutral-100 p-4">
            <div class="flex items-center justify-between">
                @if($statusPage->logo_url)
                    <img src="{{ Storage::url($statusPage->logo_url) }}" alt="{{ $statusPage->name }}" class="h-8">
                @else
                    <h1 class="text-xl font-bold">{{ $statusPage->name }}</h1>
                @endif
            </div>
        </div>
    </div>

    <livewire:status-page.overall-status :statusPage="$statusPage" />

    <div class="max-w-2xl mx-auto flex flex-col gap-2">
        @foreach($statusPage->items()->with('monitor')->orderBy('order')->get() as $item)
            <livewire:status-page.monitor-status :item="$item" :key="$item->id" />
        @endforeach
    </div>
</div>
