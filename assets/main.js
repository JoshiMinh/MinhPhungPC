// Combined App Scripts (Dark Mode & Scroll Position)

// Dark Mode Toggle
const switchBtn = document.getElementById('switchBtn');
const switchBtnMobile = document.getElementById('switchBtnMobile');
const theme = localStorage.getItem('theme');

if (theme === 'dark') {
    document.body.classList.add('dark-theme');
}

const toggleTheme = () => {
    document.body.classList.toggle('dark-theme');
    if (document.body.classList.contains('dark-theme')) {
        localStorage.setItem('theme', 'dark');
    } else {
        localStorage.setItem('theme', 'light');
    }
};

if (switchBtn) switchBtn.addEventListener('click', toggleTheme);
if (switchBtnMobile) switchBtnMobile.addEventListener('click', toggleTheme);

// Scroll Position Persistence
document.addEventListener("DOMContentLoaded", function() {
    const scrollPositions = JSON.parse(localStorage.getItem('scrollPositions')) || {};
    const path = window.location.pathname;

    if (scrollPositions[path]) {
        window.scrollTo(0, scrollPositions[path]);
    }

    window.addEventListener('scroll', function() {
        scrollPositions[path] = window.scrollY;
        localStorage.setItem('scrollPositions', JSON.stringify(scrollPositions));
    });
});
