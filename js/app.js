// Constants
const STORAGE_KEY = 'markdown_content';
const TITLE_KEY = 'document_title';

// Éléments DOM
const editor = document.getElementById('markdownInput');
const preview = document.getElementById('preview');
const downloadBtn = document.getElementById('downloadBtn');
const clearBtn = document.getElementById('clearBtn');
const documentTitle = document.getElementById('documentTitle');
const markdownTools = document.querySelectorAll('.markdown-tools button');
const downloadMdBtn = document.getElementById('downloadMdBtn');
const saveBtn = document.getElementById('saveBtn');
const documentId = document.getElementById('documentId');

import { downloadPdf } from "./telechargementPDF.js";
import { createLinkPopup } from "./image.js";
import { parseMarkdown } from "./conversionMarkdown.js";
import { applyTheme } from "./theme.js";
import { handleShortcuts } from "./shortcuts.js";
import { highlightAllCodeBlocks } from "./highlight-init.js";

/**
 * Sauvegarde le document sur le serveur
 */
async function saveDocument() {
    showNotification('Sauvegarde en cours...'); // Affiche immédiatement une notification
    try {
        const response = await fetch('../php/save_document.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: documentId.value,
                titre: documentTitle.textContent,
                contenu_markdown: editor.value,
                contenu_html: preview.innerHTML
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Document sauvegardé avec succès');
            if (data.id && !documentId.value) {
                documentId.value = data.id;
                // Mettre à jour l'URL avec l'ID du document
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('id', data.id);
                window.history.pushState({}, '', newUrl);
            }
        } else {
            showNotification('Erreur lors de la sauvegarde: ' + data.error, 'error');
        }
    } catch (error) {
        showNotification('Erreur lors de la sauvegarde: ' + error.message, 'error');
    }
}

/**
 * Met en place les écouteurs d'événements
 */
/**
 * Gère le titre du document
 */
function handleDocumentTitle() {
    // Charge le titre sauvegardé
    const savedTitle = localStorage.getItem(TITLE_KEY);
    if (savedTitle) {
        documentTitle.textContent = savedTitle;
    }

    // Sauvegarde le titre quand il change
    documentTitle.addEventListener('input', () => {
        localStorage.setItem(TITLE_KEY, documentTitle.textContent);
    });

    // Empêche les sauts de ligne
    documentTitle.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            documentTitle.blur();
        }
    });
}

function setupEventListeners() {
    // Mise à jour en temps réel
    editor.addEventListener('input', updatePreview);

    // Synchronisation du défilement
    editor.addEventListener('scroll', syncScroll);


    // Téléchargement
    downloadBtn.addEventListener('click', downloadPdf);
    downloadMdBtn.addEventListener('click', downloadMarkdown);

    // Effacement
    clearBtn.addEventListener('click', clearContent);


    // Outils Markdown
    markdownTools.forEach(button => {
        button.addEventListener('click', () => insertMarkdown(button.dataset.md));
    });

    // Raccourcis clavier
    editor.addEventListener('keydown', handleShortcuts);
    
    // Ajout de l'écouteur pour le bouton de sauvegarde
    saveBtn.addEventListener('click', saveDocument);

    // Ajout du raccourci clavier Ctrl+S / Cmd+S pour sauvegarder
    document.addEventListener('keydown', async function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            await saveDocument();
        }
    });
}

/**
 * Initialise l'éditeur
 */
function initEditor() {
    loadContent();
    applyTheme();
    setupEventListeners();
    handleDocumentTitle();
    }



/**
 * Met à jour la prévisualisation
 */
function updatePreview() {
    const markdown = editor.value;
    const html = parseMarkdown(markdown);
    preview.innerHTML = html;
    highlightAllCodeBlocks();
    saveToLocalStorage();
}

/**
 * Synchronise le défilement entre l'éditeur et la prévisualisation
 */
function syncScroll() {
    const percentage = editor.scrollTop / (editor.scrollHeight - editor.clientHeight);
    preview.scrollTop = percentage * (preview.scrollHeight - preview.clientHeight);
}

/**
 * Sauvegarde le contenu dans localStorage
 */
function saveToLocalStorage() {
    localStorage.setItem(STORAGE_KEY, editor.value);
}

/**
 * Charge le contenu du document (depuis la base, le localStorage ou vide)
 */
async function loadContent() {
    const urlParams = new URLSearchParams(window.location.search);
    const urlId = urlParams.get('id');
    const currentId = documentId.value;
    const lastId = localStorage.getItem('last_document_id');

    // Cas 1 : Un id est présent dans l'URL (document existant)
    if (urlId) {
        // Si l'id a changé, on ne garde pas le localStorage
        if (lastId !== urlId) {
            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem(TITLE_KEY);
        }
        // On sauvegarde l'id courant
        localStorage.setItem('last_document_id', urlId);
        // Charger depuis la base via AJAX
        try {
            const response = await fetch(`../php/save_document.php?id=${encodeURIComponent(urlId)}`);
            const data = await response.json();
            if (data && data.success && data.document) {
                editor.value = data.document.contenu_markdown || '';
                documentTitle.textContent = data.document.titre || 'Sans titre';
                updatePreview();
                return;
            }
        } catch (e) {
            // Si erreur, on met vide
            editor.value = '';
            documentTitle.textContent = 'Sans titre';
            updatePreview();
            return;
        }
    }

    // Cas 2 : Pas d'id (nouveau document ou brouillon)
    localStorage.setItem('last_document_id', '');
    const saved = localStorage.getItem(STORAGE_KEY);
    const savedTitle = localStorage.getItem(TITLE_KEY);
    if (saved) {
        editor.value = saved;
        if (savedTitle) {
            documentTitle.textContent = savedTitle;
        } else {
            documentTitle.textContent = 'Sans titre';
        }
        updatePreview();
    } else {
        // Texte de démonstration par défaut
        const demoText = [
            '# Présentation interactive de MarkEdit',
            '',
            '> **MarkEdit** est un éditeur Markdown moderne, pensé pour la productivité, la simplicité et la polyvalence. Découvrez toutes ses fonctionnalités en un seul document interactif !',
            '',
            '---',
            '',
            '## ✨ Fonctionnalités principales',
            '',
            '- **Édition Markdown en temps réel** avec prévisualisation instantanée',
            '- **Barre d’outils complète** : gras, italique, titres, listes, surlignage, indentation, citations, code, tableaux, liens, images…',
            '- **Raccourcis clavier** pour chaque action (voir plus bas)',
            '- **Export PDF, HTML, Markdown**',
            '- **Thème clair/sombre** personnalisable',
            '- **Responsive** : expérience optimale sur mobile, tablette et desktop',
            '- **Sauvegarde** (localStorage ou cloud) avec ctrl+s',
            '- **Gestion des images** (upload, dimensions)',
            '- **Popup d’insertion** pour tableaux, liens, images',
            '- **Historique et synchronisation** (pour les utilisateurs connectés)',
            '',
            '---',
            '',
            '## 📝 Exemples de rendu Markdown',
            '',
            '### Titres',
            '# Titre 1',
            '## Titre 2',
            '### Titre 3',
            '### Texte et mise en forme',
            '- **Gras**',
            '- *Italique*',
            '- __Souligné__',
            '- ==Surligné==',
            '',
            '### Listes',
            '- Élément de liste à puces',
            '- Deuxième élément',
            '',
            '1. Premier élément numéroté',
            '1. Deuxième élément',
            '',
            '### Citations',
            '> Ceci est une citation',
            '>> sur plusieurs lignes',
            'Exemple :',
            'Le savoir ne vaut que s’il est partagé.',
            'Chaque mot que l’on transmet peut éclairer une vie.',
            'Apprendre, c’est grandir à chaque instant.',
            'Enseigner, c’est semer des graines d’éternit',
            '',
            '### Code',
            "Code en ligne : `console.log('Hello world!');`",
            '',
            'Bloc de code multiligne :',
            '```js',
            'function test() {',
            "  console.log('Test MarkEdit');",
            '}',
            '```',
            '',
            'Bloc de code sans langage :',
            '```',
            'SELECT * FROM documents WHERE id = 1;',
            '```',
            '',
            '### Tableaux',
            '| Nom complet         | Âge  | Ville     |',
            '|---------------------|------|-----------|',
            '| Fatoumata Traoré    | 24   | Bamako    |',
            '| Issa Koné           | 30   | Ségou     |',
            '| Aminata Diallo      | 19   | Kayes     |',
            '| Abdoulaye Coulibaly | 27   | Mopti     |',
            '',
            '### Liens et images',
            '- [Lien vers Google](https://www.google.com)',
            '- ![Github](https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSbqj9Ii13d6hx5a9kyLnC5A8A96LDSaSZv_w&s)<!-- dimensions:200:200 -->',
            '- ![Image avec dimensions](../images/mahamane-removebg-preview.png)<!-- dimensions:200:200 -->',
            '',
            '### Indentation',
            '    Ligne indentée avec 4 espaces',
            '        Ligne très indentée',
            '',
            '### Saut de ligne et séparateur',
            'Ligne 1  ',
            'Ligne 2 (saut de ligne)',
            '',
            '---',
            '',
            '## ⌨️ Raccourcis clavier (Windows/Linux/Mac)',
            '',
            '| Action                | Windows/Linux         | Mac                   |',
            '|-----------------------|----------------------|-----------------------|',
            '| Gras                  | Ctrl+B               | ⌘B                    |',
            '| Italique              | Ctrl+I               | ⌘I                    |',
            '| Sauvegarder           | Ctrl+S               | ⌘S                    |',
            '| Titre 1               | Alt+H                | ⌥H                    |',
            '| Titre 2               | Alt+2                | ⌥2                    |',
            '| Titre 3               | Alt+Shift+H          | ⌥⇧H                   |',
            '| Souligné              | Alt+U                | ⌥U                    |',
            '| Surligné              | Alt+M                | ⌥M                    |',
            '| Code en ligne         | Alt+K                | ⌥K                    |',
            '| Bloc de code          | Alt+C                | ⌥C                    |',
            '| Lien                  | Alt+L                | ⌥L                    |',
            '| Image                 | Alt+Shift+L          | ⌥⇧L                   |',
            '| Tableau               | Alt+T                | ⌥T                    |',
            '| Liste à puces         | Alt+N                | ⌥N                    |',
            '| Liste numérotée       | Alt+O                | ⌥O                    |',
            '| Citation              | Alt+Q                | ⌥Q                    |',
            '',
            '---',
            '',
            '## 🎨 Thème & accessibilité',
            '',
            '- Basculez entre le **thème clair** et le **thème sombre** avec le bouton dédié.',
            '- L’interface est accessible au clavier et adaptée à tous les écrans.',
            '',
            '---',
            '',
            '## 📱 Testez la réactivité',
            '',
            'Essayez MarkEdit sur mobile, tablette, desktop, en portrait et paysage. La barre d’outils s’adapte, la barre d’actions reste accessible, et l’expérience reste optimale partout.',
            '',
            '---',
            '',
            '## 🚀 Prêt à créer ?',
            '',
            'Utilisez ce document pour tester chaque fonctionnalité : modifiez, insérez, exportez, changez de thème, testez les raccourcis… MarkEdit est là pour booster votre productivité !'
        ].join('\n');
        editor.value = demoText;
        documentTitle.textContent = 'Introduction';
        updatePreview();
    }
}

/**
 * Télécharge le contenu en tant que fichier .md
 */
function downloadMarkdown() {
    // Récupère le contenu HTML rendu
    const content = preview.innerHTML;
    const blob = new Blob([content], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    
    // Utilise le titre du document pour le nom du fichier
    let filename = documentTitle.textContent.trim();
    // Si le titre est vide, utilise 'sans-titre'
    if (!filename || filename === 'Sans titre') {
        filename = 'sans-titre';
    }
    // Nettoie le nom de fichier et ajoute l'extension
    filename = filename.replace(/[^a-z0-9]/gi, '-').toLowerCase() + '.html';
    
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
}

/**
 * Efface le contenu et réinitialise le titre
 */
function clearContent() {
    if (confirm('Voulez-vous vraiment effacer tout le contenu ?')) {
        editor.value = '';
        updatePreview();
        localStorage.removeItem(STORAGE_KEY);
        // Réinitialise aussi le titre
        documentTitle.textContent = 'Sans titre';
        localStorage.removeItem(TITLE_KEY);
    }
}





/**
 * Insère du Markdown à la position du curseur
 */
function insertMarkdown(markdown) {
    const start = editor.selectionStart;
    const end = editor.selectionEnd;
    const text = editor.value;
    const selectedText = text.substring(start, end);
    
    // Cas spécial pour le tableau
    if (markdown === 'table') {
        createTablePopup();
        return;
    }

    // Cas spécial pour l'indentation
    if (markdown === 'indent') {
        let lines = selectedText 
            ? selectedText.split('\n') 
            : [text.substring(text.lastIndexOf('\n', start) + 1, text.indexOf('\n', start) > -1 ? text.indexOf('\n', start) : text.length)];
        
        const indentedLines = lines.map(line => '  ' + line).join('\n');
        insertAtCursor(indentedLines, start, end, text);
        return;
    }

    // Cas spécial pour les liens
    if (markdown === 'link') {
        createLinkPopup(false)
            .then(linkMarkdown => {
                insertAtCursor(linkMarkdown, start, end, text);
            })
            .catch(() => {});
        return;
    }
    
    // Cas spécial pour les images
    if (markdown === 'image') {
        createLinkPopup(true)
            .then(imageMarkdown => {
                insertAtCursor(imageMarkdown, start, end, text);
            })
            .catch(() => {});
        return;
    }


    // Cas spécial pour les citations (blockquotes)
    if (markdown === 'quote') {
        let lines = selectedText 
            ? selectedText.split('\n') 
            : [text.substring(text.lastIndexOf('\n', start) + 1, text.indexOf('\n', start) > -1 ? text.indexOf('\n', start) : text.length)];
        
        const quotedLines = lines.map(line => '> ' + line).join('\n');
        insertAtCursor(quotedLines, start, end, text);
        return;
    }
    
    let insertion = markdown;
    if (selectedText) {
        if (markdown.includes('```')) {
            // Pour les blocs de code
            insertion = '```' + selectedText + '```';
        } else if (markdown.includes('`')) {
            // Pour le code inline
            insertion = '`' + selectedText + '`';
        } else if (markdown.startsWith('#')) {
            // Pour les titres
            insertion = markdown + selectedText;
        } else if (markdown.startsWith('-')) {
            // Pour les listes
            insertion = '- ' + selectedText;
        } else if (markdown.includes('==')) {
            // Pour le code surligné
            insertion = '==' + selectedText + '==';
        } else if (markdown.includes('__')) {
            // Pour le soulignement
            insertion = '__' + selectedText + '__';
        } else {
            // Pour le gras et l'italique
            insertion = markdown.replace('texte', selectedText);
        }
    }

    editor.value = text.substring(0, start) + insertion + text.substring(end);
    editor.focus();
    
    // Positionne le curseur au bon endroit si pas de sélection
    if (!selectedText) {
        let newCursorPos = start;
        if (markdown.includes('~~~')) {
            newCursorPos = start + 4; // Après ~~~\n
        } else if (markdown.includes('texte')) {
            newCursorPos = start + 2; // Après ** ou *
        } else if (markdown.startsWith('#')) {
            newCursorPos = start + markdown.length; // Après # + espace
        } else if (markdown.startsWith('-')) {
            newCursorPos = start + 2; // Après - + espace
        }
        editor.setSelectionRange(newCursorPos, newCursorPos);
    }
    
    updatePreview();
}



/**
 * Affiche une notification temporaire
 */
function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 2000);
}





// Fonction pour créer la popup du tableau
function createTablePopup() {
    const overlay = document.createElement('div');
    overlay.className = 'popup-overlay';
    
    const popup = document.createElement('div');
    popup.className = 'table-popup';
    popup.innerHTML = `
        <h3>Créer un tableau</h3>
        <div class="input-group">
            <div class="input-field">
                <label for="rows">Nombre de lignes</label>
                <input type="number" id="rows" min="1" max="10" value="3">
            </div>
            <div class="input-field">
                <label for="cols">Nombre de colonnes</label>
                <input type="number" id="cols" min="1" max="10" value="3">
            </div>
        </div>
        <div class="buttons">
            <button class="cancel">Annuler</button>
            <button class="confirm">Créer</button>
        </div>
    `;

    document.body.appendChild(overlay);
    document.body.appendChild(popup);

    // Gestionnaires d'événements
    popup.querySelector('.cancel').addEventListener('click', () => {
        overlay.remove();
        popup.remove();
    });

    popup.querySelector('.confirm').addEventListener('click', () => {
        const rows = parseInt(popup.querySelector('#rows').value);
        const cols = parseInt(popup.querySelector('#cols').value);
        insertTable(rows, cols);
        overlay.remove();
        popup.remove();
    });
}

// Fonction pour insérer le tableau en Markdown
function insertTable(rows, cols) {
    let tableMarkdown = '\n';
    
    // Entêtes
    tableMarkdown += '|';
    for (let i = 0; i < cols; i++) {
        tableMarkdown += ` Colonne ${i + 1} |`;
    }
    tableMarkdown += '\n|';
    
    // Séparateur
    for (let i = 0; i < cols; i++) {
        tableMarkdown += '---|';
    }
    tableMarkdown += '\n';
    
    // Lignes
    for (let row = 0; row < rows; row++) {
        tableMarkdown += '|';
        for (let col = 0; col < cols; col++) {
            tableMarkdown += '   |';
        }
        tableMarkdown += '\n';
    }
    
    // Insérer le tableau à la position du curseur
    const cursorPos = editor.selectionStart;
    editor.value = editor.value.slice(0, cursorPos) + tableMarkdown + editor.value.slice(cursorPos);
    editor.focus();
    updatePreview();
}





// Fonction utilitaire pour insérer du texte à la position du curseur
function insertAtCursor(insertion, start, end, text) {
    editor.value = text.substring(0, start) + insertion + text.substring(end);
    editor.focus();
    const newCursorPos = start + insertion.length;
    editor.setSelectionRange(newCursorPos, newCursorPos);
    updatePreview();
}

// Initialisation de l'éditeur au chargement de la page
document.addEventListener('DOMContentLoaded', initEditor);
