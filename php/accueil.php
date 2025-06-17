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
                <a href="../index.php?orientation=portrait" class="choose-btn">Choisir Portrait</a>
            </div>

            <div class="orientation-card">
                <div class="orientation-icon">
                    <i class="fas fa-laptop"></i>
                </div>
                <h3>Mode Paysage</h3>
                <p>Idéal pour voir le contenu et l'aperçu côte à côte sur les grands écrans.</p>
                <a href="../index.php?orientation=landscape" class="choose-btn">Choisir Paysage</a>
            </div>
        </section>

        <section class="recent-works">
            <h2>Travaux Récents</h2>
            <div class="works-grid">
                <?php
                // TODO: Remplacer par la vraie logique de récupération des travaux récents
                $recent_works = [
                    [
                        'title' => 'Documentation Projet',
                        'description' => 'Guide d\'utilisation complet pour notre nouvelle application',
                        'date' => '15 juin 2025',
                        'image' => '../images/work-1.jpg'
                    ],
                    [
                        'title' => 'Notes de réunion',
                        'description' => 'Résumé de la réunion hebdomadaire d\'équipe',
                        'date' => '14 juin 2025',
                        'image' => '../images/work-2.jpg'
                    ],
                    [
                        'title' => 'Article de blog',
                        'description' => 'Les meilleures pratiques en développement web',
                        'date' => '13 juin 2025',
                        'image' => '../images/work-3.jpg'
                    ]
                ];

                foreach ($recent_works as $work) {
                    echo '<div class="work-card">
                        <img src="' . htmlspecialchars($work['image']) . '" alt="' . htmlspecialchars($work['title']) . '" class="work-image">
                        <div class="work-content">
                            <h3>' . htmlspecialchars($work['title']) . '</h3>
                            <p>' . htmlspecialchars($work['description']) . '</p>
                        </div>
                        <div class="work-footer">
                            <span class="work-date">' . htmlspecialchars($work['date']) . '</span>
                            <a href="#" class="work-action">Ouvrir <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>';
                }
                ?>
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
    </script>
    <script type="module" src="../js/theme.js"></script>
</body>
</html>