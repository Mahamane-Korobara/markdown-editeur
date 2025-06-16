
/**
 * Convertit le texte Markdown en HTML
 */
function parseMarkdown(markdown) {
    // Implémentation simple des règles de conversion Markdown
    let html = markdown
        // Gestion de l'indentation visuelle
        .replace(/^(\s{2,})(.+)$/gm, function(match, indent, content) {
            const paddingLeft = indent.length * 10; // 10px par niveau d'indentation
            return `<div style="padding-left: ${paddingLeft}px">${content}</div>`;
        })
        .replace(/^### (.*$)/gm, '<h3>$1</h3>')
        .replace(/^## (.*$)/gm, '<h2>$1</h2>')
        .replace(/^# (.*$)/gm, '<h1>$1</h1>')
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/==(.*?)==/g, '<mark>$1</mark>') // Support pour le surlignage
        .replace(/__(.*?)__/g, '<u>$1</u>') // Support pour le soulignement
        .replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>')
        .replace(/`(.*?)`/g, '<code>$1</code>')
        .replace(/^(\d+)\.\s(.*)$/gm, function(match, num, content) {
            return `<li data-type="ordered" data-number="${num}">${content}</li>`;
        })
        .replace(/(<li data-type="ordered".*?<\/li>(\n|$))+/gm, function(match) {
            // Réorganise la numérotation des listes ordonnées
            let counter = 1;
            const listItems = match.replace(/\s*data-type="ordered"\s*data-number="\d+"\s*/g, function() {
                return ` value="${counter++}"`;
            });
            return '<ol>' + listItems + '</ol>';
        })
        .replace(/^\s*[-*]\s(.*)$/gm, '<li data-type="unordered">$1</li>') // Liste non ordonnée
        .replace(/(<li data-type="unordered">.*<\/li>(\n|$))+/gm, function(match) {
            return '<ul>' + match.replace(/\s*data-type="unordered"\s*/g, '') + '</ul>';
        })
        .replace(/^>(.+)$/gm, '<blockquote>$1</blockquote>')
        .replace(/!\[([^\]]*)\]\(([^)]+)\)(?:<!-- dimensions:(\d*):(\d*) -->)?/g, function(match, alt, src, width, height) {
            let attrs = `src="${src}" alt="${alt}"`;
            if (width) attrs += ` width="${width}px"`;
            if (height) attrs += ` height="${height}px"`;
            return `<img ${attrs}>`;
        })
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank">$1</a>');

    // Gestion des tableaux
    html = html.replace(/^\|(.*)\|$/gm, function(match, content) {
        // Si c'est une ligne de séparation (ex: |---|---|)
        if (content.match(/^\s*-[-\s]*\|\s*-[-\s]*\|?\s*$/)) {
            return ''; // On ignore la ligne de séparation
        }
        
        // Sinon c'est une ligne de contenu
        const cells = content.split('|').map(cell => cell.trim());
        const isHeader = html.split('\n').indexOf(match) === 0; // Première ligne = en-tête
        
        const tag = isHeader ? 'th' : 'td';
        return `<tr>${cells.map(cell => `<${tag}>${cell}</${tag}>`).join('')}</tr>`;
    });

    // Entoure les tableaux avec <table></table>
    html = html.replace(/(<tr>.*?<\/tr>(\n|$))+/g, '<table>$&</table>');        // Traitement des listes ordonnées
        let counter = 0;
        html = html.replace(/^(\d+)\.\s(.*)$/gm, function(match, num, content) {
            counter++;
            return `<li value="${counter}">${content}</li>`;
        });

        // Regroupe les éléments de liste ordonnée consécutifs
        html = html.replace(/(<li value="\d+">[^<]+<\/li>\n*)+/g, function(match) {
            counter = 0; // Réinitialise le compteur pour la prochaine liste
            return '<ol>' + match + '</ol>';
        });

        // Gestion des paragraphes (après les tableaux pour éviter les conflits)
        html = html.replace(/^(?!<[uhlt]|<li)(.+)$/gm, '<p>$1</p>');

        return html;
    }

export { parseMarkdown };