<div class="flex flex-col gap-2 my-4">
    @foreach($statusPage->items()->with('monitor')->orderBy('order')->get() as $item)
        <livewire:status-page.monitor-status :item="$item" :key="$item->id" />
    @endforeach
</div>
