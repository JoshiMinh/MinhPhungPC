document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const mainNavbar = document.querySelector('.navbar-main');
    const secondaryNavbar = document.querySelector('.navbar-secondary');
    const componentCards = document.querySelectorAll('.component-card');
    const searchInput = document.querySelector('.search-input');
    const searchButton = document.querySelector('.search-button');
    const icon = document.querySelector('.icon');
    const switchBtn = document.getElementById('switchBtn');
    const isDarkModeEnabled = localStorage.getItem('dark-mode') === 'enabled';

    if (isDarkModeEnabled) toggleDarkMode(true);

    switchBtn.onclick = () => toggleDarkMode(body.classList.toggle('dark-mode'));

    function toggleDarkMode(enable) {
        [body, mainNavbar, secondaryNavbar].forEach(el => 
            el.classList.toggle('dark-mode', enable)
        );

        componentCards.forEach(card => {
            card.classList.toggle('bg-dark', enable);
            card.classList.toggle('text-white', enable);
            card.classList.toggle('bg-white', !enable);
            card.classList.toggle('text-dark', !enable);
        });

        searchInput.classList.toggle('dark-mode', enable);
        searchButton.classList.toggle('dark-mode', enable);

        icon.classList.toggle('bi-moon', !enable);
        icon.classList.toggle('bi-brightness-high', enable);
        icon.style.color = enable ? 'white' : 'black';

        localStorage.setItem('dark-mode', enable ? 'enabled' : 'disabled');
    }
});