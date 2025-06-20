<?php
session_start();
require_once 'config.php';

// Récupérer l'orientation depuis l'URL
$orientation = isset($_GET['orientation']) ? $_GET['orientation'] : 'portrait';

try {
    // Si l'utilisateur est connecté
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        
        // Créer un nouveau document avec orientation
        $stmt = $connexion->prepare('INSERT INTO documents (user_id, titre, contenu_markdown, contenu_html, orientation) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$userId, 'Sans titre', '', '', $orientation]);
        $documentId = $connexion->lastInsertId();
    } else {
        // Pour les utilisateurs non connectés, créer un document temporaire
        // avec un ID unique basé sur le timestamp
        $documentId = time() . rand(1000, 9999);
    }
    
    // Rediriger vers l'éditeur avec l'ID du document
    header('Location: index.php?id=' . $documentId . '&orientation=' . $orientation);
    exit;

} catch (PDOException $e) {
    // Log l'erreur et rediriger vers l'éditeur sans ID en cas d'erreur
    error_log("Erreur création document: " . $e->getMessage());
    header('Location: index.php?orientation=' . $orientation);
    exit;
}
?>
