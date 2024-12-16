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
    @livewireScripts

    <script>
        const resizeObserver = new ResizeObserver(entries => {
            const height = document.body.offsetHeight;
            window.parent.postMessage({ type: 'resize', height }, '*');
        });

        // Observe the body element
        resizeObserver.observe(document.body);

        // Also trigger on Livewire updates
        document.addEventListener('livewire:initialized', () => {
            const height = document.body.offsetHeight;
            window.parent.postMessage({ type: 'resize', height }, '*');
        });
    </script>
</body>
</html>
