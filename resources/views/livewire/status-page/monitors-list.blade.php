<div>
    <h2 class="font-medium text-neutral-900 text-lg mb-4 pl-1">Service Status</h2>
    <div class="flex flex-col gap-4">
        @foreach($statusPage->items()->with('monitor')->orderBy('order')->get() as $item)
            <livewire:status-page.monitor-status :item="$item" :key="$item->id" />
        @endforeach
    </div>
</div>
