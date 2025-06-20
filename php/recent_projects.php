<?php
require_once 'config.php';

header('Content-Type: application/json');

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
    // On récupère les 6 projets les plus récents (tous utilisateurs)
    $stmt = $pdo->query("SELECT id, titre, date_creation, orientation FROM documents ORDER BY date_creation DESC LIMIT 20");
    $projects = $stmt->fetchAll();
    echo json_encode(['success' => true, 'projects' => $projects]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
