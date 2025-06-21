document.addEventListener('DOMContentLoaded', function() {
    function isMobile() {
        return window.matchMedia('(max-width: 768px)').matches;
    }

    function hasLandscapeParam() {
        return new URLSearchParams(window.location.search).get('orientation') === 'landscape';
    }

    let toggleBtn = document.getElementById('toggleView');
    const editorWrapper = document.querySelector('.editor-wrapper');
    let isEditorView = true;

    // Sur mobile, le bouton doit toujours être visible et fonctionnel
    if (isMobile()) {
        if (!toggleBtn) {
            // Ajout dynamique si absent
            const actionsBar = document.querySelector('.actions');
            if (actionsBar) {
                toggleBtn = document.createElement('button');
                toggleBtn.id = 'toggleView';
                toggleBtn.title = "Basculer l'affichage";
                toggleBtn.innerHTML = '<i class="fa-solid fa-expand icon"></i>';
                actionsBar.appendChild(toggleBtn);
            }
        }
        if (toggleBtn) toggleBtn.style.display = '';
        if (editorWrapper) {
            editorWrapper.classList.add('fullscreen-editor');
            isEditorView = true;
            if (toggleBtn.querySelector('i')) {
                toggleBtn.querySelector('i').classList.remove('fa-compress');
                toggleBtn.querySelector('i').classList.add('fa-expand');
            }
            toggleBtn.addEventListener('click', function() {
                if (isEditorView) {
                    editorWrapper.classList.remove('fullscreen-editor');
                    editorWrapper.classList.add('fullscreen-preview');
                    if (toggleBtn.querySelector('i')) {
                        toggleBtn.querySelector('i').classList.remove('fa-expand');
                        toggleBtn.querySelector('i').classList.add('fa-compress');
                    }
                } else {
                    editorWrapper.classList.remove('fullscreen-preview');
                    editorWrapper.classList.add('fullscreen-editor');
                    if (toggleBtn.querySelector('i')) {
                        toggleBtn.querySelector('i').classList.remove('fa-compress');
                        toggleBtn.querySelector('i').classList.add('fa-expand');
                    }
                }
                isEditorView = !isEditorView;
            });
        }
        return;
    }

    // Desktop : comportement historique (affichage que si orientation=landscape)
    if (!hasLandscapeParam()) {
        if (toggleBtn) toggleBtn.style.display = 'none';
        if (editorWrapper) editorWrapper.classList.remove('fullscreen-editor', 'fullscreen-preview');
        return;
    } else {
        if (toggleBtn) toggleBtn.style.display = '';
    }
    if (editorWrapper && toggleBtn) {
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
    }
});
