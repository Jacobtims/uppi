import './bootstrap';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

document.addEventListener('livewire:initialized', () => {
    tippy('[data-tippy-content]', {
        placement: 'top',
        allowHTML: true,
    });
});
