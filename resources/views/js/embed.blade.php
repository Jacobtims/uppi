(function() {
    const UPPI_BASE_URL = '{{ config('app.url') }}';
    const UPPI_USER_ID = '{{ $user->id }}';

    function initStatusPageEmbed() {
        document.querySelectorAll('[data-uppi-status]').forEach(container => {
            const slug = container.getAttribute('data-uppi-status');
            const type = container.getAttribute('data-type') || 'all';

            // Create and configure iframe
            const iframe = document.createElement('iframe');
            iframe.style.width = '100%';
            iframe.style.border = 'none';
            iframe.style.overflow = 'hidden';
            iframe.setAttribute('scrolling', 'no');
            iframe.src = `${UPPI_BASE_URL}/s/${slug}/embed?type=${type}`;

            // Add iframe to container
            container.appendChild(iframe);

            // Listen for resize messages
            window.addEventListener('message', function(e) {
                if (e.origin !== UPPI_BASE_URL) return;

                if (e.data.type === 'resize' && iframe.contentWindow === e.source) {
                    iframe.style.height = `${e.data.height}px`;
                }
            });
        });
    }

    // Run on load and when DOM changes
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStatusPageEmbed);
    } else {
        initStatusPageEmbed();
    }

    // Watch for new elements being added
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1 && node.matches('[data-uppi-status]')) {
                    initStatusPageEmbed();
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
})();
