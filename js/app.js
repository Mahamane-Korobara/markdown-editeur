// Constants
const STORAGE_KEY = 'markdown_content';
const TITLE_KEY = 'document_title';

// Éléments DOM
const editor = document.getElementById('markdownInput');
const preview = document.getElementById('preview');
const saveBtn = document.getElementById('saveBtn');
const downloadBtn = document.getElementById('downloadBtn');
const clearBtn = document.getElementById('clearBtn');
const documentTitle = document.getElementById('documentTitle');
const markdownTools = document.querySelectorAll('.markdown-tools button');
const downloadMdBtn = document.getElementById('downloadMdBtn');

import { downloadPdf } from "./telechargementPDF.js";
import { createLinkPopup } from "./image.js";
import { parseMarkdown } from "./conversionMarkdown.js";
import { applyTheme } from "./theme.js";

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

    // Sauvegarde
    saveBtn.addEventListener('click', saveContent);

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
 * Charge le contenu depuis localStorage
 */
function loadContent() {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) {
        editor.value = saved;
        updatePreview();
    }
}

/**
 * Sauvegarde explicite du contenu
 */
function saveContent() {
    saveToLocalStorage();
    showNotification('Contenu sauvegardé');
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
 * Gère les raccourcis clavier
 */
function handleShortcuts(e) {
    if (e.ctrlKey || e.metaKey) {
        switch(e.key.toLowerCase()) {
            case 's':
                e.preventDefault();
                saveContent();
                break;
            case 'b':
                e.preventDefault();
                insertMarkdown('**texte**');
                break;
            case 'i':
                e.preventDefault();
                insertMarkdown('*texte*');
                break;
        }
    }
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

// Fonction pour échapper le HTML dans les blocs de code
// function escapeHtml(unsafe) {
//     return unsafe
//         .replace(/&/g, "&amp;")
//         .replace(/</g, "&lt;")
//         .replace(/>/g, "&gt;")
//         .replace(/"/g, "&quot;")
//         .replace(/'/g, "&#039;");
// }



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
