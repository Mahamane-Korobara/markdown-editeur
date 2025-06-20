// Fonction pour télécharger en PDF
async function downloadPdf() {
    console.log('downloadPdf appelé');
    // Créer une copie du contenu de prévisualisation pour le PDF
    const pdfContent = document.createElement('div');
    pdfContent.innerHTML = preview.innerHTML;
    
    // Appliquer les styles actuels
    const styles = `
        <style>
            * {
                font-family: 'Segoe UI', system-ui, sans-serif;
                box-sizing: border-box;
            }
            
            body {
                color: ${getComputedStyle(document.body).getPropertyValue('--text-primary')};
                background-color: ${getComputedStyle(document.body).getPropertyValue('--bg-primary')};
                line-height: 1.6;
            }
            
            /* Titres */
            h1, h2, h3 {
                margin-top: 2rem;
                margin-bottom: 1rem;
                color: ${getComputedStyle(document.body).getPropertyValue('--text-primary')};
                font-weight: 600;
            }
            
            h1 { font-size: 2em; }
            h2 { font-size: 1.5em; }
            h3 { font-size: 1.25em; }
            
            /* Paragraphes et listes */
            p {
                margin: 1rem 0;
                text-align: justify;
            }
            
            ul, ol {
                margin: 1rem 0;
                padding-left: 2rem;
            }
            
            li {
                margin: 0.5rem 0;
            }
            
            /* Code */
            pre {
                background-color: ${getComputedStyle(document.body).getPropertyValue('--bg-secondary')};
                padding: 1rem;
                border-radius: 4px;
                overflow-x: auto;
                white-space: pre-wrap;
                word-wrap: break-word;
                margin: 1rem 0;
                font-size: 0.9em;
                line-height: 0;
            }
            
            code {
                font-family: 'Consolas', monospace;
                background-color: ${getComputedStyle(document.body).getPropertyValue('--bg-secondary')};
                padding: 0.2rem 0.4rem;
                border-radius: 3px;
                font-size: 0.9em;
                line-height: 0;
            }
            
            pre code {
                padding: 0;
                background-color: transparent;
            }
            
            /* Images */
            img {
                max-width: 100%;
                height: auto;
                margin-top: 1rem;
                display: block;
            }
            
            p {
                color: black;
            }
            
            /* Liens */
            a {
                color: ${getComputedStyle(document.body).getPropertyValue('--accent-color')};
                text-decoration: none;
            }
            
            a:hover {
                text-decoration: underline;
            }
            
            /* Tableaux */
            table {
                border-collapse: collapse;
                width: 100%;
                margin: 1.5rem 0;
                font-size: 0.9em;
            }

            th, td {
                border: 1px solid ${getComputedStyle(document.body).getPropertyValue('--border-color')};
                padding: 0.75rem;
                text-align: left;
            }

            th {
                background-color: ${getComputedStyle(document.body).getPropertyValue('--bg-secondary')};
                color: ${getComputedStyle(document.body).getPropertyValue('--text-primary')};
                font-weight: bold;
            }

            tr:nth-child(even) {
                background-color: ${getComputedStyle(document.body).getPropertyValue('--bg-secondary')};
            }

            /* Style pour les citations et indentation */
            blockquote {
                margin: 1rem 0;
                padding-left: 1rem;
                border-left: 4px solid ${getComputedStyle(document.body).getPropertyValue('--accent-color')};
                color: ${getComputedStyle(document.body).getPropertyValue('--text-secondary')};
                background-color: ${getComputedStyle(document.body).getPropertyValue('--bg-secondary')};
                padding: 1rem;
                border-radius: 0 4px 4px 0;
                font-style: italic;
            }

            /* Style pour les divs indentés */
            
            /* Style pour le surlignage */
            mark {
                background-color: #ffeb3b;
                padding: 0 4px;
                border-radius: 2px;
            }

            /* Style pour le texte en gras et italique */
            strong {
                font-weight: 600;
                color: ${getComputedStyle(document.body).getPropertyValue('--text-primary')};
            }
            
            em {
                font-style: italic;
                color: ${getComputedStyle(document.body).getPropertyValue('--text-primary')};
            }
        </style>
    `;
    
    // Détecter l'orientation du document
    let orientation = 'portrait';
    let docOrientation = null;
    const orientationMeta = document.getElementById('documentOrientation');
    if (orientationMeta) {
        docOrientation = orientationMeta.value || orientationMeta.getAttribute('value');
    }
    if (docOrientation && (docOrientation === 'landscape' || docOrientation === 'portrait')) {
        orientation = docOrientation;
    } else {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('orientation')) {
            orientation = urlParams.get('orientation') === 'landscape' ? 'landscape' : 'portrait';
        }
    }
    // Configuration pour html2pdf (simplifiée pour éviter les conflits)
    console.log('Orientation PDF:', orientation);
    const opt = {
        margin: [15, 15],
        filename: `${documentTitle.textContent.trim() || 'sans-titre'}.pdf`,
        jsPDF: {
            unit: 'mm',
            format: orientation === 'landscape' ? [297, 210] : 'a4',
            orientation: orientation
        }
    };
    // Créer un conteneur temporaire pour le PDF
    const tempContainer = document.createElement('div');
    tempContainer.innerHTML = styles + pdfContent.innerHTML;
    document.body.appendChild(tempContainer);
    
    try {
        // Générer et télécharger le PDF
        await html2pdf().set(opt).from(tempContainer).save();
        showNotification('PDF généré avec succès');
    } catch (error) {
        console.error('Erreur lors de la génération du PDF:', error);
        showNotification('Erreur lors de la génération du PDF');
    } finally {
        // Nettoyer le conteneur temporaire
        document.body.removeChild(tempContainer);
    }
}

export { downloadPdf };