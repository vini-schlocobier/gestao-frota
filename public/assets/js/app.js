const mobileMenuButton = document.querySelector('[data-mobile-menu-button]');
const mobileMenuCloseButton = document.querySelector('[data-mobile-menu-close]');
const mobileSidebar = document.querySelector('[data-mobile-sidebar]');
const mobileSidebarOverlay = document.querySelector('[data-mobile-sidebar-overlay]');

const setSidebarOpen = (isOpen) => {
    if (!mobileSidebar || !mobileSidebarOverlay) {
        return;
    }

    mobileSidebar.classList.toggle('translate-x-0', isOpen);
    mobileSidebar.classList.toggle('-translate-x-full', !isOpen);
    mobileSidebarOverlay.classList.toggle('hidden', !isOpen);
    document.body.classList.toggle('overflow-hidden', isOpen);
};

if (mobileMenuButton) {
    mobileMenuButton.addEventListener('click', () => setSidebarOpen(true));
}

if (mobileMenuCloseButton) {
    mobileMenuCloseButton.addEventListener('click', () => setSidebarOpen(false));
}

if (mobileSidebarOverlay) {
    mobileSidebarOverlay.addEventListener('click', () => setSidebarOpen(false));
}

window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        setSidebarOpen(false);
    }
});
