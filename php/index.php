<?php
require_once 'config.php';
session_start();

// Récupération du document si un ID est fourni
$document = null;
if (isset($_GET['id'])) {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

        $stmt = $pdo->prepare("
            SELECT id, titre, contenu_markdown, contenu_html 
            FROM documents 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $_GET['id']]);
        $document = $stmt->fetch();
    } catch (PDOException $e) {
        // Gestion silencieuse de l'erreur
        error_log("Erreur lors du chargement du document : " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarkEdit (éditeur Markdown)</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/shortcuts.css">
    <!-- Lien CDN Font Awesome v6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
</head>
<body>
    <!-- En-tête avec la barre d'outils -->
    <header class="toolbar">
        <div class="logo">
            <h1>MarkEdit | <span id="documentTitle" class="document-title" contenteditable="true"><?php echo isset($document) ? htmlspecialchars($document['titre']) : 'Sans titre'; ?></span></h1>
            <input type="hidden" id="documentId" value="<?php echo isset($document) ? htmlspecialchars($document['id']) : ''; ?>">
            <?php
            $orientation = 'portrait';
            if (isset($document) && isset($document['orientation'])) {
                $orientation = $document['orientation'];
            } elseif (isset($_GET['orientation']) && in_array($_GET['orientation'], ['portrait', 'landscape'])) {
                $orientation = $_GET['orientation'];
            }
            ?>
            <input type="hidden" id="documentOrientation" value="<?php echo htmlspecialchars($orientation); ?>">
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
            <button id="toggleView" title="Basculer l'affichage">
                <i class="fa-solid fa-expand icon"></i>
            </button>
            <button id="shortcutsBtn" title="Raccourcis clavier">
                <i class="fa-solid fa-keyboard icon"></i>
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
                <textarea id="markdownInput" class="editor" spellcheck="false"><?php echo isset($document) ? htmlspecialchars($document['contenu_markdown']) : ''; ?></textarea>
            </div>
            <div class="preview-column">
                <div class="column-header">Prévisualisation</div>
                <div id="preview" class="preview"><?php echo isset($document) ? $document['contenu_html'] : ''; ?></div>
            </div>
        </div>
    </main>

    <!-- Liste des raccourcis clavier -->
    <div id="shortcuts-help" class="shortcuts-help">
        <h3>Raccourcis clavier</h3>
        
        <div class="shortcuts-os-section">
            <h4>Windows / Linux</h4>
            <ul>
                <!-- Raccourcis standards -->
                <li><kbd>Ctrl</kbd> + <kbd>B</kbd> : Gras</li>
                <li><kbd>Ctrl</kbd> + <kbd>I</kbd> : Italique</li>
                <li><kbd>Ctrl</kbd> + <kbd>S</kbd> : Sauvegarder</li>
            </ul>
            <h5>Mise en forme</h5>
            <ul>
                <li><kbd>Alt</kbd> + <kbd>H</kbd> : Titre 1</li>
                <li><kbd>Alt</kbd> + <kbd>2</kbd> : Titre 2</li>
                <li><kbd>Alt</kbd> + <kbd>Shift</kbd> + <kbd>H</kbd> : Titre 3</li>
                <li><kbd>Alt</kbd> + <kbd>U</kbd> : Souligné</li>
                <li><kbd>Alt</kbd> + <kbd>M</kbd> : Surligner</li>
            </ul>
            <h5>Insertions</h5>
            <ul>
                <li><kbd>Alt</kbd> + <kbd>K</kbd> : Code en ligne</li>
                <li><kbd>Alt</kbd> + <kbd>C</kbd> : Bloc de code</li>
                <li><kbd>Alt</kbd> + <kbd>L</kbd> : Lien</li>
                <li><kbd>Alt</kbd> + <kbd>Shift</kbd> + <kbd>L</kbd> : Image</li>
                <li><kbd>Alt</kbd> + <kbd>T</kbd> : Tableau</li>
            </ul>
            <h5>Listes et Citations</h5>
            <ul>
                <li><kbd>Alt</kbd> + <kbd>N</kbd> : Liste à puces</li>
                <li><kbd>Alt</kbd> + <kbd>O</kbd> : Liste numérotée</li>
                <li><kbd>Alt</kbd> + <kbd>Q</kbd> : Citation</li>
            </ul>
        </div>

        <div class="shortcuts-os-section">
            <h4>Mac</h4>
            <ul>
                <!-- Raccourcis standards -->
                <li><kbd>⌘</kbd> + <kbd>B</kbd> : Gras</li>
                <li><kbd>⌘</kbd> + <kbd>I</kbd> : Italique</li>
                <li><kbd>⌘</kbd> + <kbd>S</kbd> : Sauvegarder</li>
            </ul>
            <h5>Mise en forme</h5>
            <ul>
                <li><kbd>⌥</kbd> + <kbd>H</kbd> : Titre 1</li>
                <li><kbd>⌥</kbd> + <kbd>2</kbd> : Titre 2</li>
                <li><kbd>⌥</kbd> + <kbd>⇧</kbd> + <kbd>H</kbd> : Titre 3</li>
                <li><kbd>⌥</kbd> + <kbd>U</kbd> : Souligné</li>
                <li><kbd>⌥</kbd> + <kbd>M</kbd> : Surligner</li>
            </ul>
            <h5>Insertions</h5>
            <ul>
                <li><kbd>⌥</kbd> + <kbd>K</kbd> : Code en ligne</li>
                <li><kbd>⌥</kbd> + <kbd>C</kbd> : Bloc de code</li>
                <li><kbd>⌥</kbd> + <kbd>L</kbd> : Lien</li>
                <li><kbd>⌥</kbd> + <kbd>⇧</kbd> + <kbd>L</kbd> : Image</li>
                <li><kbd>⌥</kbd> + <kbd>T</kbd> : Tableau</li>
            </ul>
            <h5>Listes et Citations</h5>
            <ul>
                <li><kbd>⌥</kbd> + <kbd>N</kbd> : Liste à puces</li>
                <li><kbd>⌥</kbd> + <kbd>O</kbd> : Liste numérotée</li>
                <li><kbd>⌥</kbd> + <kbd>Q</kbd> : Citation</li>
            </ul>
        </div>
    </div>

    <script type="module" src="../js/shortcutsHelp.js"></script>
    <script type="module" src="../js/shortcuts.js"></script>
    <script type="module" src="../js/markdownParser.js"></script>
    <script type="module" src="../js/telechargementPDF.js"></script>
    <script type="module" src="../js/app.js"></script>
    <script type="module" src="../js/theme.js"></script>
    <script type="module" src="../js/toggleView.js"></script>
</body>
</html>
