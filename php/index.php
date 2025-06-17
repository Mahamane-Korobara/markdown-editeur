<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarkEdit (éditeur Markdown)</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Lien CDN Font Awesome v6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>
    <!-- En-tête avec la barre d'outils -->
    <header class="toolbar">
        <div class="logo">
            <h1>MarkEdit | <span id="documentTitle" class="document-title" contenteditable="true">Sans titre</span></h1>
        </div>
        <div class="actions">
            <button id="saveBtn" title="Enregistrer">
                <i class="fa-regular fa-floppy-disk icon"></i>
            </button>
            <button id="downloadBtn" title="Télécharger PDF">
                <i class="fa-regular fa-file-pdf icon"></i>
            </button>
            <button id="downloadMdBtn" title="Télécharger .md">
                <i class="fa-regular fa-cloud-arrow-down icon"></i>
            </button>
            <button id="clearBtn" title="Effacer">
                <i class="fa-regular fa-trash-can icon"></i>
            </button>
            <button id="themeToggle" title="Changer le thème">
               <i class="fa-regular fa-sun icon"></i>
            </button>
        </div>
    </header>

    <!-- Conteneur principal -->
    <main class="editor-container">
        <!-- Barre d'outils Markdown -->
        <div class="markdown-tools">
            <button data-md="**texte**">
                <i class="fa-solid fa-bold"></i>
            </button>
            <button data-md="*texte*">
                <i class="fa-solid fa-italic"></i>
            </button>
            <button data-md="# ">
                <i class="fa-solid fa-heading">1</i>
            </button>
            <button data-md="## ">
                <i class="fa-solid fa-heading">2</i>
            </button>
            <button data-md="### ">
                <i class="fa-solid fa-heading">3</i>
            </button>
            <button data-md="- " title="Liste non ordonnée">
                <i class="fa-solid fa-list-ul"></i>
            </button>
            <button data-md="1. " title="Liste ordonnée">
                <i class="fa-solid fa-list-ol"></i>
            </button>
            <button data-md="==" title="Surligner">
                <i class="fa-solid fa-highlighter"></i>
            </button>
            <button data-md="indent" title="Indenter">
                <i class="fa-solid fa-indent"></i>
            </button>
            <button data-md="quote" title="Citation">
                <i class="fa-solid fa-quote-left"></i>
            </button>
            <button data-md="```\ncode\n```" title="Bloc de code multiligne">
                <i class="fa-solid fa-code-compare"></i>
            </button>
            <button data-md="`code`" title="Code en ligne">
                <i class="fa-solid fa-code"></i>
            </button>
            <button data-md="__texte__" title="Souligner">
                <i class="fa-solid fa-underline"></i>
            </button>
            <button data-md="table" title="Insérer un tableau">
                <i class="fa-solid fa-table"></i>
            </button>
            <button data-md="link" title="Insérer un lien">
                <i class="fa-solid fa-link"></i>
            </button>
            <button data-md="image" title="Insérer une image">
                <i class="fa-regular fa-image"></i>
            </button>
        </div>

        <!-- Conteneur de l'éditeur -->
        <div class="editor-wrapper">
            <div class="editor-column">
                <div class="column-header">Markdown</div>
                <textarea id="markdownInput" class="editor" spellcheck="false"></textarea>
            </div>
            <div class="preview-column">
                <div class="column-header">Prévisualisation</div>
                <div id="preview" class="preview"></div>
            </div>
        </div>
    </main>

    <script type="module" src="../js/markdownParser.js"></script>
    <script type="module" src="../js/telechargementPDF.js"></script>
    <script type="module" src="../js/app.js"></script>
    <script type="module" src="../js/theme.js"></script>
</body>
</html>
