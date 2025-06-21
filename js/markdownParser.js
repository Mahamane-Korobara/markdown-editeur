import { highlightAllCodeBlocks } from './highlight-init.js';
/**
 * Règles de conversion Markdown vers HTML
 */
const markdownRules = [
    // Titres
    {
        pattern: /^(#{1,6})\s(.+)$/gm,
        replace: (match, hashes, content) => `<h${hashes.length}>${content.trim()}</h${hashes.length}>`
    },
    
    // Blockquotes avec >
    {
        pattern: /^>\s(.+)$/gm,
        replace: '<blockquote>$1</blockquote>'
    },

    // Indentation simple
    {
        pattern: /^(\s{4,}|\t+)(.+)$/gm,
        replace: (match, indent, content) => {
            const spaces = indent.replace(/\t/g, '    ').length;
            const padding = spaces * 20; // 20px pour chaque niveau d'indentation
            return `<div style="padding-left: ${padding}px;">${content}</div>`;
        }
    },
    
    // Surlignage
    {
        pattern: /==([^=]+)==/g,
        replace: '<mark>$1</mark>'
    },

    // Listes ordonnées
    {
        pattern: /^(\d+)\.\s+(.+)$/gm,
        replace: '<li>$2</li>'
    },

    // Listes non ordonnées
    {
        pattern: /^[-*]\s(.+)$/gm,
        replace: '<li>$1</li>'
    },
    
    // Gestion des listes (transformation des li consécutifs en ol ou ul)
    {
        pattern: /(?:^|\n)(<li>.*<\/li>)(?:\n|$)/g,
        replace: (match, list) => {
            // Si la liste commence par un chiffre, c'est une liste ordonnée
            if (match.match(/^\d/)) {
                return `<ol>${list}</ol>`;
            }
            return `<ul>${list}</ul>`;
        }
    },
    
    // Code blocks avec langage optionnel
    {
        pattern: /^```([a-z]*)\n([\s\S]*?)```$/gm,
        replace: (match, lang, code) => {
            const language = lang ? ` class="language-${lang}"` : ' class="language-plaintext"';
            return `<pre><code${language}>${escapeHtml(code.trimEnd())}</code></pre>`;
        }
    },
    
    // Inline code
    {
        pattern: /(?<!`)`([^`\n]+)`(?!`)/g,
        replace: (match, code) => `<code>${escapeHtml(code)}</code>`
    },
    
    // Gras
    {
        pattern: /\*\*([^*]+)\*\*/g,
        replace: '<strong>$1</strong>'
    },
    
    // Italique
    {
        pattern: /\*([^*]+)\*/g,
        replace: '<em>$1</em>'
    },
    
    // Liens
    {
        pattern: /\[([^\]]+)\]\(([^)]+)\)/g,
        replace: '<a href="$2" target="_blank">$1</a>'
    },
    
    // Paragraphes
    {
        pattern: /^([^<].*)\n$/gm,
        replace: '<p>$1</p>'
    }
];

/**
 * Échappe les caractères HTML spéciaux
 */
function escapeHtml(text) {
    const htmlEntities = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    };
    return text.replace(/[&<>"']/g, char => htmlEntities[char]);
}

/**
 * Gère la conversion des listes
 */
function handleLists(text) {
    const listPattern = /^[-*]\s.+(?:\n[-*]\s.+)*$/gm;
    return text.replace(listPattern, match => `<ul>\n${match}\n</ul>`);
}

/**
 * Convertit le texte Markdown en HTML
 */
function parseMarkdown(markdown) {
    if (!markdown) return '';
    let html = markdown + '\n';
    html = handleLists(html);
    markdownRules.forEach(({ pattern, replace }) => {
        html = html.replace(pattern, replace);
    });
    // Nettoyage final
    setTimeout(() => highlightAllCodeBlocks(), 0); // Coloration syntaxique après rendu
    return html.trim();
}

// Export des fonctions
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { parseMarkdown, escapeHtml };
} else {
    window.parseMarkdown = parseMarkdown;
    window.escapeHtml = escapeHtml;
}
