<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-gray-100 rounded-lg">
            <p class="text-sm text-red-700">
                Warning: This action cannot be undone. All your data, including monitors, alerts, and settings will be permanently deleted.
            </p>
            <p class="mt-2 text-sm text-red-700">
                Please enter your current password to confirm the deletion of your account.
            </p>
        </div>

        <form class="space-y-6">
            {{ $this->form }}

            <div class="flex justify-end">
                {{ $this->deleteAction }}
            </div>
        </form>
    </div>

    <x-filament-actions::modals />

</x-filament-panels::page>
