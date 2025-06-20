document.addEventListener('DOMContentLoaded', function() {
    // Fonction utilitaire pour vérifier le paramètre d'URL
    function hasLandscapeParam() {
        return new URLSearchParams(window.location.search).get('orientation') === 'landscape';
    }

    const toggleBtn = document.getElementById('toggleView');
    const editorWrapper = document.querySelector('.editor-wrapper');
    let isEditorView = true;

    // Afficher ou masquer le bouton toggle selon le paramètre
    if (!hasLandscapeParam()) {
        toggleBtn.style.display = 'none';
        // On s'assure que l'affichage est normal
        editorWrapper.classList.remove('fullscreen-editor', 'fullscreen-preview');
        return;
    } else {
        toggleBtn.style.display = '';
    }

    // Par défaut, on commence en mode édition plein écran
    editorWrapper.classList.add('fullscreen-editor');
    isEditorView = true;
    toggleBtn.querySelector('i').classList.remove('fa-compress');
    toggleBtn.querySelector('i').classList.add('fa-expand');

    toggleBtn.addEventListener('click', function() {
        if (isEditorView) {
            editorWrapper.classList.remove('fullscreen-editor');
            editorWrapper.classList.add('fullscreen-preview');
            toggleBtn.querySelector('i').classList.remove('fa-expand');
            toggleBtn.querySelector('i').classList.add('fa-compress');
        } else {
            editorWrapper.classList.remove('fullscreen-preview');
            editorWrapper.classList.add('fullscreen-editor');
            toggleBtn.querySelector('i').classList.remove('fa-compress');
            toggleBtn.querySelector('i').classList.add('fa-expand');
        }
        isEditorView = !isEditorView;
    });
});
