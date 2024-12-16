<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $statusPage->name }} - Status</title>
    @vite('resources/css/app.css')
    <meta name="robots" content="noindex,nofollow">
    @livewireStyles
</head>
<body>
    <div class="pb-1">
        @if($type === 'overview')
            <livewire:status-page.overall-status :statusPage="$statusPage" />
        @elseif($type === 'monitors')
            <livewire:status-page.monitors-list :statusPage="$statusPage" />
        @else
            <div>
                <livewire:status-page.overall-status :statusPage="$statusPage" />
                <livewire:status-page.monitors-list :statusPage="$statusPage" />
            </div>
        @endif
    </div>
    @livewireScripts

    <script>
        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Debounced resize handler
        const sendResizeMessage = debounce(() => {
            const height = document.body.offsetHeight;
            window.parent.postMessage({ type: 'resize', height }, '*');
        }, 100); // 100ms debounce

        const resizeObserver = new ResizeObserver(() => {
            sendResizeMessage();
        });

        // Observe the body element
        resizeObserver.observe(document.body);

        // Also trigger on Livewire updates
        document.addEventListener('livewire:initialized', sendResizeMessage);
    </script>
</body>
</html>
