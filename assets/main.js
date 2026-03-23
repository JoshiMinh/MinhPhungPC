// Combined App Scripts (Dark Mode & Scroll Position)

// Dark Mode Toggle
const switchBtn = document.getElementById('switchBtn');
const switchBtnMobile = document.getElementById('switchBtnMobile');
const theme = localStorage.getItem('theme');

if (theme === 'dark') {
    document.body.classList.add('dark-theme');
}

function toggleTheme() {
    const isDark = document.body.classList.toggle('dark-theme');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

switchBtn?.addEventListener('click', toggleTheme);
switchBtnMobile?.addEventListener('click', toggleTheme);

// Scroll Position Persistence
document.addEventListener('DOMContentLoaded', () => {
    const scrollPositions = JSON.parse(localStorage.getItem('scrollPositions')) || {};
    const path = window.location.pathname;

    if (scrollPositions[path]) {
        window.scrollTo(0, scrollPositions[path]);
    }

    window.addEventListener('scroll', () => {
        scrollPositions[path] = window.scrollY;
        localStorage.setItem('scrollPositions', JSON.stringify(scrollPositions));
    });
});
