<div>
    <div class="max-w-2xl mx-auto my-4 px-2">
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

    <div class="max-w-2xl mx-auto px-2">
        <livewire:status-page.overall-status :statusPage="$statusPage" />
        <livewire:status-page.monitors-list :statusPage="$statusPage" />
    </div>
</div>
