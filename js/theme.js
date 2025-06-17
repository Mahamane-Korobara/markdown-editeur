const themeToggle = document.getElementById('themeToggle');
let isDark = localStorage.getItem('theme') === 'dark';


/**
 * Bascule entre les thèmes clair et sombre
 */
function toggleTheme() {
    isDark = !isDark;
    applyTheme();
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}
/**
 * Applique le thème actuel
 */
function applyTheme() {
    document.body.setAttribute('data-theme', isDark ? 'dark' : 'light');

    // Change l'icône en fonction du thème
    const themeIcon = themeToggle.querySelector('i');
    if (isDark) {
        themeIcon.className = 'fa-regular fa-moon icon';
    } else {
        themeIcon.className = 'fa-regular fa-sun icon';
    }
}
// Changement de thème
themeToggle.addEventListener('click', toggleTheme);


export { applyTheme, toggleTheme };
