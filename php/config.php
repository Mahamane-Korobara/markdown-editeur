<?php
// Configurations de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'markedit_db');
define('DB_USER', 'root'); // À modifier selon la configuration
define('DB_PASS', ''); // À modifier selon la configuration

// Configuration du site
define('SITE_URL', 'http://localhost/editeur_markdown');
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/editeur_markdown/images/');

// Gestion des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1); // Afficher les erreurs en développement

// Connexion à la base de données
try {
    $connexion = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    // En production, log l'erreur au lieu de l'afficher
    error_log("Erreur de connexion : " . $e->getMessage());
    die("Erreur de connexion à la base de données.");
}
?>
