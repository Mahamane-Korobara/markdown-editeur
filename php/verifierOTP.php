<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['inscription_temp']) || !isset($_POST['otp'])) {
    header('Location: ../landing.php');
    exit();
}

try {
    $code_saisi = $_POST['otp'];
    $donnees_inscription = $_SESSION['inscription_temp'];

    // Vérification de l'expiration
    if (strtotime($donnees_inscription['code_expiration']) < time()) {
        throw new Exception('Le code a expiré. Veuillez demander un nouveau code.');
    }

    // Vérification du code
    if ($code_saisi !== $donnees_inscription['code_otp']) {
        throw new Exception('Code incorrect. Veuillez réessayer.');
    }

    // Connexion à la base de données
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Insertion du nouvel utilisateur
    $stmt = $pdo->prepare("
        INSERT INTO users (nom, email, motdepasse, date_inscription, theme_preference) 
        VALUES (?, ?, ?, NOW(), 'light')
    ");
    
    $stmt->execute([
        $donnees_inscription['nom'],
        $donnees_inscription['email'],
        $donnees_inscription['motdepasse']
    ]);
    
    $userId = $pdo->lastInsertId();

    // Création de la session utilisateur
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_nom'] = $donnees_inscription['nom'];
    $_SESSION['user_email'] = $donnees_inscription['email'];

    // Nettoyage des données temporaires
    unset($_SESSION['inscription_temp']);

    // Redirection avec succès
    $_SESSION['message'] = "Compte vérifié avec succès ! Bienvenue sur MarkEdit.";
    header('Location: accueil.php');
    exit();

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: verificationOTP.php');
    exit();
}
?>
