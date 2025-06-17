<?php
session_start();
require_once 'config.php';
require_once 'mailService.php';

try {
    // Récupération et nettoyage des données du formulaire
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $motdepasse = $_POST['motdepasse'];

    // Validation des données
    if (!$nom || !$email || !$motdepasse) {
        throw new Exception('Tous les champs sont obligatoires');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format d\'email invalide');
    }

    // if (strlen($motdepasse) < 8) {
    //     throw new Exception('Le mot de passe doit contenir au moins 8 caractères');
    // }

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

    // Vérification si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        throw new Exception('Cet email est déjà utilisé');
    }

    // Hashage du mot de passe
    $motdepasse_hash = password_hash($motdepasse, PASSWORD_DEFAULT);

    // Génération du code OTP
    $code_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $code_expiration = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    // Stockage temporaire des données d'inscription
    $_SESSION['inscription_temp'] = [
        'nom' => $nom,
        'email' => $email,
        'motdepasse' => $motdepasse_hash,
        'code_otp' => $code_otp,
        'code_expiration' => $code_expiration
    ];

    // Envoi du code OTP par email
    envoyerCodeOTP($email, $nom, $code_otp);

    // Redirection vers la page de vérification OTP
    header('Location: verificationOTP.php');
    exit();

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../landing.php');
    exit();
}
?>
