<?php
session_start();
require_once 'config.php';

// Activation temporaire des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // echo "<h1>Débogage de la connexion</h1>";
    
    // // 1. Affichage des données reçues
    // echo "<h2>1. Données POST reçues :</h2>";
    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";

    // // 2. Connexion à la base de données
    // echo "<h2>2. Tentative de connexion à la base de données...</h2>";
    // $pdo = new PDO(
    //     "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
    //     DB_USER,
    //     DB_PASS,
    //     [
    //         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    //         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    //     ]
    // );
    // echo "✓ Connexion à la base de données réussie<br>";
    
    // Récupération et nettoyage des données du formulaire
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $motdepasse = $_POST['motdepasse'];
    // Ne pas logger le mot de passe en production

    // Validation des données
    if (!$email || !$motdepasse) {
        throw new Exception('Tous les champs sont obligatoires');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format d\'email invalide');
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

    // Recherche de l'utilisateur par email
    $stmt = $pdo->prepare("
        SELECT id, nom, email, motdepasse, theme_preference 
        FROM users 
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $utilisateur = $stmt->fetch();

    // Vérification si l'utilisateur existe et si le mot de passe est correct
    if (!$utilisateur) {
        error_log("Utilisateur non trouvé pour l'email : " . $email);
        throw new Exception('Email ou mot de passe incorrect');
    }
    
    if (!password_verify($motdepasse, $utilisateur['motdepasse'])) {
        error_log("Mot de passe incorrect pour l'email : " . $email);
        throw new Exception('Email ou mot de passe incorrect');
    }

    // Mise à jour de la dernière connexion
    // $stmt = $pdo->prepare("
    //     UPDATE users 
    //     SET derniere_connexion = NOW() 
    //     WHERE id = ?
    // ");
    $stmt->execute([$utilisateur['id']]);

    // Création de la session utilisateur
    $_SESSION['user_id'] = $utilisateur['id'];
    $_SESSION['user_nom'] = $utilisateur['nom'];
    $_SESSION['user_email'] = $utilisateur['email'];
    $_SESSION['theme'] = $utilisateur['theme_preference'];

    // Récupération des documents récents de l'utilisateur
    $stmt = $pdo->prepare("
        SELECT id, titre, date_modification 
        FROM documents 
        WHERE user_id = ? 
        ORDER BY date_modification DESC 
        LIMIT 5
    ");
    $stmt->execute([$utilisateur['id']]);
    $_SESSION['documents_recents'] = $stmt->fetchAll();

    // Redirection vers la page d'accueil avec un message de succès
    $_SESSION['message'] = "Connexion réussie ! Bienvenue " . $utilisateur['nom'];
    header('Location: accueil.php');
    exit();

} catch (Exception $e) {
    // En cas d'erreur, stockage du message d'erreur et redirection
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../landing.php');
    exit();
}
?>
