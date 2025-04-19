<div class="min-h-screen bg-gradient-to-b from-neutral-50 to-white px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-7 mb-8">
            <div class="flex items-center">
                @if($statusPage->logo_url)
                    <img src="{{ Storage::url($statusPage->logo_url) }}" alt="{{ $statusPage->name }}" class="h-12 object-contain">
                @else
                    <h1 class="text-2xl font-medium text-neutral-900">{{ $statusPage->name }}</h1>
                @endif
            </div>
        </div>

        <!-- Content -->
        <div class="space-y-8">
            <livewire:status-page.overall-status :statusPage="$statusPage" />
            <livewire:status-page.monitors-list :statusPage="$statusPage" />
            <livewire:status-page.updates-list :statusPage="$statusPage" />
        </div>
    </div>
</div>
