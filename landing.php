<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarkEdit - Éditeur Markdown Moderne</title>
    <link rel="stylesheet" href="css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <div class="logo">
            <i class="fas fa-pen-fancy"></i>
            <span>MarkEdit</span>
        </div>
        <nav>
            <a href="#fonctionalite">Fonctionnalités</a>
            <a href="#demo">Démonstration</a>
            <a href="#about">À propos</a>
            <a href="php/index.php" class="cta-button">Lancer l'éditeur</a>

            <button id="themeToggle" title="Changer le thème">
               <i class="fa-regular fa-sun icon"></i>
            </button>
        </nav>

    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Transformez vos idées en contenu structuré</h1>
                <p>Un éditeur Markdown moderne, intuitif et puissant pour tous vos besoins de rédaction</p>
                <div class="cta-group">
                    <a href="php/index.php" class="primary-button">
                        <i class="fas fa-edit"></i>
                        Commencer à écrire
                    </a>
                    <a href="#demo" class="secondary-button">
                        <i class="fas fa-play"></i>
                        Voir la démo
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <div class="slider-container">
                    <img src="images/black.png" alt="Version sombre de l'éditeur" class="preview-image active">
                    <img src="images/white.png" alt="Version claire de l'éditeur" class="preview-image">
                </div>
            </div>
        </section>

        <section id="fonctionalite" class="fonctionalite">
            <h2>Fonctionnalités principales</h2>
            <div class="fonctionalite-grid">
                <div class="feature-card">
                    <i class="fas fa-sync-alt"></i>
                    <h3>Aperçu en temps réel</h3>
                    <p>Visualisez vos modifications instantanément pendant la rédaction</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-image"></i>
                    <h3>Gestion des images</h3>
                    <p>Importez facilement des images locales ou distantes</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-table"></i>
                    <h3>Tables et listes</h3>
                    <p>Créez des tableaux et des listes structurés sans effort</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-paint-brush"></i>
                    <h3>Thèmes personnalisables</h3>
                    <p>Adaptez l'interface à vos préférences</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-file-export"></i>
                    <h3>Export multiformat</h3>
                    <p>Exportez en HTML, PDF ou Markdown</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-keyboard"></i>
                    <h3>Raccourcis clavier</h3>
                    <p>Optimisez votre flux de travail avec des raccourcis</p>
                </div>
            </div>
        </section>

        <section id="demo" class="demo">
            <h2>Découvrez la puissance de MarkEdit</h2>
            <div class="demo-container">
                <div class="demo-video">
                    <video controls poster="images/white.png">
                        <source src="videos/markedit.webm" type="video/webm">
                        Votre navigateur ne supporte pas la lecture de vidéos.
                    </video>
                </div>
                <div class="demo-fonctionalite">
                    <div class="demo-feature">
                        <i class="fas fa-check"></i>
                        <span>Interface intuitive</span>
                    </div>
                    <div class="demo-feature">
                        <i class="fas fa-check"></i>
                        <span>Barre d'outils complète</span>
                    </div>
                    <div class="demo-feature">
                        <i class="fas fa-check"></i>
                        <span>Support multiplateforme</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="signup-section">
            <div class="signup-content">
                <div class="signup-text">
                    <h2>Gardez vos créations à portée de main</h2>
                    <p class="signup-description">Créez votre espace personnel et donnez vie à vos idées</p>
                    <div class="benefits">
                        <div class="benefit-item">
                            <i class="fas fa-compass"></i>
                            <div class="benefit-text">
                                <h3>Choix d'orientation</h3>
                                <p>Personnalisez votre expérience d'écriture avec des modèles et des styles adaptés à vos besoins</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <i class="fas fa-history"></i>
                            <div class="benefit-text">
                                <h3>Historique illimité</h3>
                                <p>Accédez à tous vos projets, où que vous soyez</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div class="benefit-text">
                                <h3>Synchronisation cloud</h3>
                                <p>Vos documents toujours à jour sur tous vos appareils</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="signup-form">
                    <div class="form-container">
                        <div id="signupForm" class="form-section active">
                            <h3>Rejoignez MarkEdit gratuitement</h3>
                            <form action="php/traitementInscription.php" method="post" class="animated-form">
                                <div class="form-group">
                                    <input type="text" name="nom" placeholder="Votre nom" required>
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="form-group">
                                    <input type="email" name="email" placeholder="Votre email" required>
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="motdepasse" placeholder="Choisir un mot de passe" required>
                                    <i class="fas fa-lock"></i>
                                </div>
                                <button type="submit" class="signup-button">
                                    <span>Créer mon compte</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </form>
                            <div class="form-footer">
                                <p>Déjà membre ? <a href="#" class="switch-form" data-form="login">Connexion</a></p>
                            </div>
                        </div>

                        <div id="loginForm" class="form-section">
                            <h3>Connexion à MarkEdit</h3>
                            <form action="php/traitementConnexion.php" method="post" class="animated-form">
                                <div class="form-group">
                                    <input type="email" name="email" placeholder="Votre email" required>
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="motdepasse" placeholder="Votre mot de passe" required>
                                    <i class="fas fa-lock"></i>
                                </div>
                                <button type="submit" class="signup-button">
                                    <span>Se connecter</span>
                                    <i class="fas fa-sign-in-alt"></i>
                                </button>
                            </form>
                            <div class="form-footer">
                                <p>Pas encore de compte ? <a href="#" class="switch-form" data-form="signup">S'inscrire</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="cta-content">
                <h2>Prêt à commencer ?</h2>
                <p>Rejoignez des milliers d'utilisateurs qui font confiance à MarkEdit</p>
                <a href="php/index.php" class="secondary-button">
                    <i class="fas fa-rocket"></i>
                    Lancer l'éditeur
                </a>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>MarkEdit</h3>
                <p>Un éditeur Markdown moderne pour tous vos besoins de rédaction</p>
            </div>
            <div class="footer-section">
                <h3>Liens rapides</h3>
                <a href="#fonctionalite">Fonctionnalités</a>
                <a href="#demo">Démonstration</a>
                <a href="#about">À propos</a>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <a href="mailto:korobaramahamane311@gmail.com">korobaramahamane311@gmail.com</a>
                <div class="social-links">
                    <a href="https://github.com/Mahamane-Korobara" target="_blank"><i class="fab fa-github"></i></a>
                    <a href="https://www.linkedin.com/in/korobara-mahamane-9880a7298/" target="_blank"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> MarkEdit. Tous droits réservés.</p>
        </div>
    </footer>
    <script src="js/slideImage.js"></script>
    <script type="module" src="js/theme.js"></script> 
    <script type="module" src="js/gestionForm.js"></script>
</body>
</html>
