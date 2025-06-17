<?php
// Configurations de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'markedit_db');
define('DB_USER', 'root'); // À modifier selon la configuration
define('DB_PASS', ''); // À modifier selon la configuration

// Configuration du site
define('SITE_URL', 'http://localhost/editeur_markdown');
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/editeur_markdown/images/');

// Configuration de session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mettre à 1 si HTTPS

// Gestion des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 0); // Ne pas afficher les erreurs en production
?>
