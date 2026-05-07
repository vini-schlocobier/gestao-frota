const filterToggleButton = document.querySelector('[data-toggle-filter-panel]');
const filterPanel = document.getElementById('filterPanel');
const deleteVehicleLinks = document.querySelectorAll('[data-confirm-delete]');

if (filterToggleButton && filterPanel) {
    filterToggleButton.addEventListener('click', () => {
        filterPanel.classList.toggle('hidden');
    });
}

deleteVehicleLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
        const message = link.getAttribute('data-confirm-delete') || 'Confirmar exclusao?';

        if (!window.confirm(message)) {
            event.preventDefault();
        }
    });
});
