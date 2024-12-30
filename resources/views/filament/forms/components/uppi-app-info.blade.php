<div class="space-y-6">
    <div class="text-center">
        <div class="flex justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
            </svg>
        </div>
        <h2 class="text-xl font-bold mb-2">Get the Uppi App</h2>
        <p class="text-gray-500 dark:text-gray-400">
            Download the Uppi app to receive push notifications for your monitors.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a target="_blank" href="https://apps.apple.com/app/uppi/id6739699410" class="flex items-center justify-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-500 gap-2">
            <svg class="w-8 h-8 mr-2" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
            </svg>
            <span>Download on the App Store</span>
        </a>

        <a target="_blank" href="https://play.google.com/store/apps/details?id=dev.uppi.app" class="flex items-center justify-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-gray-800">
            <svg class="w-8 h-8 mr-2" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z" />
            </svg>
            <span>Get it on Google Play</span>
        </a>
    </div>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            After installing the app, generate a connection code to connect it to your account.
        </p>
        <a href="{{ $personal_access_tokens_url }}" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-500 gap-2">
            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
            </svg>

            <span>Connections</span>
        </a>
    </div>

    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <h3 class="font-semibold mb-2">How it works:</h3>
        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600 dark:text-gray-300">
            <li>Download and install the Uppi app from your device's app store</li>
            <li>Register your device by generating a connection code from the "Connections" page</li>
            <li>Enter the code in the app to connect it to your account</li>
            <li>Optionally, enable push notifications to receive alerts for your monitors on Settings</li>
        </ol>
    </div>
</div>
