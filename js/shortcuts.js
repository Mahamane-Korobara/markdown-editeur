// Gestionnaire de raccourcis clavier pour l'éditeur Markdown
export function handleShortcuts(e) {
    // Gestion des raccourcis Ctrl/Cmd standards
    if (e.ctrlKey || e.metaKey) {
        switch(e.key.toLowerCase()) {
            case 'b':
                e.preventDefault();
                insertMarkdown('**texte**');
                break;
            case 'i':
                e.preventDefault();
                insertMarkdown('*texte*');
                break;
            case 's':
                e.preventDefault();
                saveContent();
                break;
        }
    }
    // Gestion des raccourcis Alt/Option
    if (e.altKey) {
        switch(e.key.toLowerCase()) {

            // Nouveaux raccourcis avec Alt
            case 'h':  // Alt + H (Headers)
                e.preventDefault();
                if (e.shiftKey) {
                    insertMarkdown('### '); // Alt + Shift + H pour H3
                } else {
                    insertMarkdown('# '); // Alt + H pour H1
                }
                break;
            case '2':
                e.preventDefault();
                insertMarkdown('## ');
                break;

            // Formatage
            case 'u':
                e.preventDefault();
                insertMarkdown('__texte__');
                break;
            case 'k':
                e.preventDefault();
                insertMarkdown('`code`');
                break;
            case 'm':
                e.preventDefault();
                insertMarkdown('==texte==');
                break;
            case 'l':
                e.preventDefault();
                if (e.shiftKey) {
                    insertMarkdown('![alt](url_de_l_image)<!-- dimensions:100:100 -->'); // Alt + Shift + L pour image
                } else {
                    insertMarkdown('[texte](url)'); // Alt + L pour lien
                }
                break;

            // Listes
            case 'n':
                e.preventDefault();
                insertMarkdown('- ');  // Alt + N pour liste non ordonnée
                break;
            case 'o':
                e.preventDefault();
                insertMarkdown('1. '); // Alt + O pour liste ordonnée
                break;

            // Blocs spéciaux
            case 'q':
                e.preventDefault();
                insertMarkdown('> ');
                break;
            case 'c':
                e.preventDefault();
                insertMarkdown('```\ncode\n```');
                break;
            case 't':
                e.preventDefault();
                insertMarkdown('| En-tête 1 | En-tête 2 |\n|------------|------------|\n| Contenu 1  | Contenu 2  |');
                break;
        }
    }
}

// Fonction d'aide pour l'insertion de Markdown
function insertMarkdown(pattern) {
    const textarea = document.getElementById('markdownInput');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const selectedText = text.substring(start, end);

    let replacement = pattern;
    if (selectedText) {
        // Si du texte est sélectionné, on l'utilise à la place du texte par défaut
        replacement = pattern.replace('texte', selectedText)
                           .replace('code', selectedText)
                           .replace('url', selectedText);
    }

    textarea.value = text.substring(0, start) + replacement + text.substring(end);
    textarea.focus();

    // Mise à jour de la prévisualisation
    if (typeof updatePreview === 'function') {
        updatePreview();
    }
}

// Fonction de sauvegarde (à implémenter selon vos besoins)
function saveContent() {
    // Déclencher le clic sur le bouton de sauvegarde
    document.getElementById('saveBtn').click();
}

