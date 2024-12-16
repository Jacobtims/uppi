import './bootstrap';
import tippy from 'tippy.js';

document.addEventListener('livewire:initialized', () => {
    tippy('[data-tippy-content]', {
        theme: 'light',
        placement: 'top',
        allowHTML: true,
    });
});
