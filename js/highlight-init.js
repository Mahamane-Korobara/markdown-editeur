// Initialisation de highlight.js
// Utilisation du CDN : highlight.js doit être chargé dans le <head> du HTML
// <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
// <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">

export function highlightAllCodeBlocks() {
    if (window.hljs) {
        document.querySelectorAll('pre code').forEach(block => {
            window.hljs.highlightElement(block);
            // Correction stricte : on force le style du projet
            block.style.lineHeight = '1.7';
            block.style.fontSize = '1.05rem';
            block.style.fontFamily = "'Consolas', monospace";
        });
    }
}

// Fonction utilitaire pour charger dynamiquement le thème highlight.js
function setHighlightTheme(theme) {
    const id = 'hljs-theme-style';
    let link = document.getElementById(id);
    if (!link) {
        link = document.createElement('link');
        link.rel = 'stylesheet';
        link.id = id;
        document.head.appendChild(link);
    }
    // Choix du thème highlight.js selon le mode
    if (theme === 'dark') {
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css';
    } else {
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css';
    }
}

// Détecte le thème courant au chargement
function getCurrentTheme() {
    return document.body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
}

// Applique le thème highlight.js au chargement
setHighlightTheme(getCurrentTheme());

// Surveille les changements de thème (nécessite que applyTheme soit appelé lors du toggle)
window.addEventListener('DOMContentLoaded', () => {
    // Si le thème change via applyTheme, on écoute les mutations d'attributs sur body
    const observer = new MutationObserver(() => {
        setHighlightTheme(getCurrentTheme());
        highlightAllCodeBlocks(); // Pour réappliquer le style si besoin
    });
    observer.observe(document.body, { attributes: true, attributeFilter: ['data-theme'] });
});

// Pour usage direct dans le navigateur (si besoin)
window.highlightAllCodeBlocks = highlightAllCodeBlocks;
