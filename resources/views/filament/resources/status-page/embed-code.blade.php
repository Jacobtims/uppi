<div class="space-y-4">
    <div>
        <h3 class="text-lg font-medium">Full Status Page</h3>
        <div class="mt-2 p-4 bg-gray-100 rounded-lg overflow-x-auto font-mono text-sm">
            &lt;script src="{{ route('embed.js', $statusPage->user) }}" async&gt;&lt;/script&gt; <br/>
            &lt;div data-uppi-status="{{ $statusPage->slug }}"&gt;&lt;/div&gt;
        </div>
    </div>

    <div>
        <h3 class="text-lg font-medium">Overview Only</h3>
        <div class="mt-2 p-4 bg-gray-100 rounded-lg overflow-x-auto font-mono text-sm">
            &lt;script src="{{ route('embed.js', $statusPage->user) }}" async&gt;&lt;/script&gt; <br/>
            &lt;div data-uppi-status="{{ $statusPage->slug }}" data-type="overview"&gt;&lt;/div&gt;
        </div>
    </div>

    <div>
        <h3 class="text-lg font-medium">Monitors Only</h3>
        <div class="mt-2 p-4 bg-gray-100 rounded-lg overflow-x-auto font-mono text-sm">
            &lt;script src="{{ route('embed.js', $statusPage->user) }}" async&gt;&lt;/script&gt; <br/>
            &lt;div data-uppi-status="{{ $statusPage->slug }}" data-type="monitors"&gt;&lt;/div&gt;
        </div>
    </div>

    <div>
        <h3 class="text-lg font-medium">Updates Only</h3>
        <div class="mt-2 p-4 bg-gray-100 rounded-lg overflow-x-auto font-mono text-sm">
            &lt;script src="{{ route('embed.js', $statusPage->user) }}" async&gt;&lt;/script&gt; <br/>
            &lt;div data-uppi-status="{{ $statusPage->slug }}" data-type="updates"&gt;&lt;/div&gt;
        </div>
    </div>
</div>