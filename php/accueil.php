<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur MarkEdit</title>
    <link rel="stylesheet" href="../css/accueil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="welcome-header">
        <h1>Bienvenue sur MarkEdit</h1>
        <p>Choisissez votre orientation préférée et commencez à créer</p>
    </div>

    <div class="container">
        <section class="orientation-choice">
            <div class="orientation-card">
                <div class="orientation-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Mode Portrait</h3>
                <p>Parfait pour les documents longs et la lecture confortable sur les appareils mobiles.</p>
                <a href="nouveau_document.php?orientation=portrait" class="choose-btn">Choisir Portrait</a>
            </div>

            <div class="orientation-card">
                <div class="orientation-icon">
                    <i class="fas fa-laptop"></i>
                </div>
                <h3>Mode Paysage</h3>
                <p>Idéal pour voir le contenu et l'aperçu côte à côte sur les grands écrans.</p>
                <a href="nouveau_document.php?orientation=landscape" class="choose-btn">Choisir Paysage</a>
            </div>
        </section>

        <section class="recent-works">
            <h2>Travaux Récents</h2>
            <div class="works-grid" id="recentProjectsContainer">
                <!-- Les projets récents seront injectés ici par JS -->
            </div>
        </section>
    </div>

    <script>
        // Animation des cartes au chargement
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.orientation-card, .work-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });

        // Chargement dynamique des projets récents
        fetch('../php/recent_projects.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('recentProjectsContainer');
                if (data.success && data.projects.length > 0) {
                    data.projects.forEach(project => {
                        const card = document.createElement('div');
                        card.className = 'work-card';
                        card.innerHTML = `
                            <div class="work-content">
                                <h3>${project.titre ? project.titre : 'Sans titre'}</h3>
                            </div>
                            <div class="work-footer">
                                <span class="work-date">${project.date_creation ? project.date_creation : ''}</span>
                                <a href="index.php?id=${project.id}${project.orientation ? `&orientation=${project.orientation}` : ''}" target="_blank" class="work-action">Ouvrir <i class="fas fa-arrow-right"></i></a>
                            </div>
                        `;
                        container.appendChild(card);
                    });
                } else {
                    container.innerHTML = '<p style="text-align:center;color:#888">Aucun projet récent trouvé.</p>';
                }
            })
            .catch(() => {
                document.getElementById('recentProjectsContainer').innerHTML = '<p style="text-align:center;color:#888">Erreur lors du chargement des projets.</p>';
            });
    </script>
    <script type="module" src="../js/theme.js"></script>
</body>
</html>