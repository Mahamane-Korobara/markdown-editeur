document.addEventListener('DOMContentLoaded', function () {
    // Gestion du basculement entre les formulaires
    const switchFormLinks = document.querySelectorAll('.switch-form');

    switchFormLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetForm = this.getAttribute('data-form');

            // Récupérer tous les formulaires
            const forms = document.querySelectorAll('.form-section');

            // Masquer tous les formulaires
            forms.forEach(form => {
                if (form.classList.contains('active')) {
                    form.style.animation = 'formSlideOut 0.3s ease forwards';
                    setTimeout(() => {
                        form.classList.remove('active');
                        form.style.animation = '';
                    }, 300);
                }
            });

            // Afficher le formulaire cible
            setTimeout(() => {
                const targetElement = document.getElementById(targetForm + 'Form');
                targetElement.classList.add('active');
                targetElement.style.animation = 'formSlideIn 0.3s ease forwards';
            }, 300);
        });
    });
});