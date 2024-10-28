document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const mainNavbar = document.querySelector('.navbar-main');
    const secondaryNavbar = document.querySelector('.navbar-secondary');
    const componentCards = document.querySelectorAll('.component-card');
    const searchInput = document.querySelector('.search-input');
    const searchButton = document.querySelector('.search-button');
    const icon = document.querySelector('.icon');

    if (localStorage.getItem('dark-mode') === 'enabled') enableDarkMode();

    document.getElementById('switchBtn').onclick = () => {
        const isDarkMode = body.classList.toggle('dark-mode');
        mainNavbar.classList.toggle('dark-mode');
        secondaryNavbar.classList.toggle('dark-mode');

        componentCards.forEach(card => {
            card.classList.toggle('bg-dark', isDarkMode);
            card.classList.toggle('text-white', isDarkMode);
            card.classList.toggle('bg-white', !isDarkMode);
            card.classList.toggle('text-dark', !isDarkMode);
        });

        searchInput.classList.toggle('dark-mode', isDarkMode);
        searchButton.classList.toggle('dark-mode', isDarkMode);

        icon.classList.toggle('bi-moon', !isDarkMode);
        icon.classList.toggle('bi-brightness-high', isDarkMode);
        icon.style.color = isDarkMode ? 'white' : 'black';

        localStorage.setItem('dark-mode', isDarkMode ? 'enabled' : 'disabled');
    };

    function enableDarkMode() {
        body.classList.add('dark-mode');
        mainNavbar.classList.add('dark-mode');
        secondaryNavbar.classList.add('dark-mode');

        componentCards.forEach(card => {
            card.classList.add('bg-dark', 'text-white');
            card.classList.remove('bg-white', 'text-dark');
        });

        searchInput.classList.add('dark-mode');
        searchButton.classList.add('dark-mode');

        icon.classList.remove('bi-moon');
        icon.classList.add('bi-brightness-high');
        icon.style.color = 'white';
    }
});