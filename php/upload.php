<?php
header('Content-Type: application/json');

// Configuration
$uploadDir = '../images/';
$response = array();

// Crée le dossier images s'il n'existe pas
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $fileName = basename($file['name']);
        $targetPath = $uploadDir . $fileName;
        
        // Vérifie si c'est une vraie image
        $check = getimagesize($file['tmp_name']);
        if ($check !== false) {
            // Déplace le fichier uploadé
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $response['success'] = true;
                $response['path'] = 'images/' . $fileName;  // Chemin relatif sans ../
            } else {
                $response['success'] = false;
                $response['error'] = 'Erreur lors du déplacement du fichier.';
            }
        } else {
            $response['success'] = false;
            $response['error'] = 'Le fichier n\'est pas une image.';
        }
    } else {
        $response['success'] = false;
        $response['error'] = 'Aucun fichier n\'a été envoyé.';
    }
} else {
    $response['success'] = false;
    $response['error'] = 'Méthode non autorisée.';
}

echo json_encode($response);
