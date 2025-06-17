<?php
session_start();
require_once 'mailService.php';

if (!isset($_SESSION['inscription_temp'])) {
    header('Location: ../landing.php');
    exit();
}

try {
    $donnees_inscription = $_SESSION['inscription_temp'];
    
    // Génération d'un nouveau code OTP
    $nouveau_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $nouvelle_expiration = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    // Mise à jour des données de session
    $_SESSION['inscription_temp']['code_otp'] = $nouveau_code;
    $_SESSION['inscription_temp']['code_expiration'] = $nouvelle_expiration;
    
    // Envoi du nouveau code
    envoyerCodeOTP(
        $donnees_inscription['email'],
        $donnees_inscription['nom'],
        $nouveau_code
    );
    
    $_SESSION['message'] = "Un nouveau code a été envoyé à votre adresse email.";
    
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: verificationOTP.php');
exit();
?>
