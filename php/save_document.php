<?php
// --- Bloc GET en premier pour permettre la récupération sans session ni POST ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    require_once 'config.php';
    $id = $_GET['id'];
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        $stmt = $pdo->prepare("SELECT id, titre, contenu_markdown, contenu_html FROM documents WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $document = $stmt->fetch();
        if ($document) {
            echo json_encode(['success' => true, 'document' => $document]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Document introuvable']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

require_once 'config.php';
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

// Récupération des données POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['titre']) || !isset($data['contenu_markdown']) || !isset($data['contenu_html'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Vérification si le document existe déjà
    $document_id = isset($data['id']) ? $data['id'] : null;
    
    if ($document_id) {
        // Mise à jour du document existant
        $stmt = $pdo->prepare("
            UPDATE documents 
            SET titre = :titre, 
                contenu_markdown = :contenu_markdown, 
                contenu_html = :contenu_html 
            WHERE id = :id AND user_id = :user_id
        ");
        
        $result = $stmt->execute([
            ':titre' => $data['titre'],
            ':contenu_markdown' => $data['contenu_markdown'],
            ':contenu_html' => $data['contenu_html'],
            ':id' => $document_id,
            ':user_id' => $_SESSION['user_id']
        ]);
    } else {
        // Création d'un nouveau document
        $stmt = $pdo->prepare("
            INSERT INTO documents (user_id, titre, contenu_markdown, contenu_html) 
            VALUES (:user_id, :titre, :contenu_markdown, :contenu_html)
        ");
        
        $result = $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':titre' => $data['titre'],
            ':contenu_markdown' => $data['contenu_markdown'],
            ':contenu_html' => $data['contenu_html']
        ]);
        
        $document_id = $pdo->lastInsertId();
    }

    // Sauvegarde dans l'historique
    $stmt = $pdo->prepare("
        INSERT INTO historique_modifications (document_id, user_id, contenu_markdown) 
        VALUES (:document_id, :user_id, :contenu_markdown)
    ");
    
    $stmt->execute([
        ':document_id' => $document_id,
        ':user_id' => $_SESSION['user_id'],
        ':contenu_markdown' => $data['contenu_markdown']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Document sauvegardé avec succès',
        'id' => $document_id
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la sauvegarde : ' . $e->getMessage()]);
}