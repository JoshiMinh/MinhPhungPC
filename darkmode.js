document.addEventListener("DOMContentLoaded", () => {
    const elements = {
        body: document.body,
        mainNavbar: document.querySelector('.navbar-main'),
        secondaryNavbar: document.querySelector('.navbar-secondary'),
        componentCards: document.querySelectorAll('.component-card'),
        searchInput: document.querySelector('.search-input'),
        searchButton: document.querySelector('.search-button'),
        icon: document.querySelector('.icon'),
        switchBtn: document.getElementById('switchBtn')
    };

    const isDarkModeEnabled = localStorage.getItem('dark-mode') === 'enabled';
    if (isDarkModeEnabled) toggleDarkMode(true);

    elements.switchBtn?.addEventListener('click', () => {
        toggleDarkMode(elements.body.classList.toggle('dark-mode'));
    });

    function toggleDarkMode(enable) {
        [elements.body, elements.mainNavbar, elements.secondaryNavbar].forEach(el =>
            el.classList.toggle('bg-dark', enable)
        );

        elements.componentCards.forEach(card => {
            card.classList.toggle('bg-dark', enable);
            card.classList.toggle('text-white', enable);
            card.classList.toggle('bg-white', !enable);
            card.classList.toggle('text-dark', !enable);
        });

        elements.searchInput?.classList.toggle('bg-dark', enable);
        elements.searchInput?.classList.toggle('text-white', enable);
        elements.searchInput?.classList.toggle('bg-light', !enable);
        elements.searchInput?.classList.toggle('text-dark', !enable);
        
        elements.searchButton?.classList.toggle('btn-dark', enable);
        elements.searchButton?.classList.toggle('btn-light', !enable);

        if (elements.icon) {
            elements.icon.classList.toggle('bi-moon', !enable);
            elements.icon.classList.toggle('bi-brightness-high', enable);
            elements.icon.style.color = enable ? 'white' : 'black';
        }

        localStorage.setItem('dark-mode', enable ? 'enabled' : 'disabled');
    }
});