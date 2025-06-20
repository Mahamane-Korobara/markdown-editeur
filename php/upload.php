<?php
require_once 'session_config.php';
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$is_logged_in = isset($_SESSION['user_id']);

// Configuration
$uploadDir = '../images/';
$response = array();

// Crée le dossier images s'il n'existe pas
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Vérification de la requête POST et du type de contenu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cas image distante (URL)
    if (!empty($_POST['image_url'])) {
        $url = $_POST['image_url'];
        $documentId = isset($_POST['document_id']) ? $_POST['document_id'] : null;
        $userId = $is_logged_in ? $_SESSION['user_id'] : null;
        if ($documentId && $userId) {
            try {
                $nomFichier = basename(parse_url($url, PHP_URL_PATH));
                $stmt = $connexion->prepare('INSERT INTO images (document_id, user_id, nom_fichier, chemin) VALUES (?, ?, ?, ?)');
                $stmt->execute([$documentId, $userId, $nomFichier, $url]);
                $response['success'] = true;
                $response['image_id'] = $connexion->lastInsertId();
            } catch (PDOException $e) {
                $response['success'] = false;
                $response['error'] = "Erreur lors de l'enregistrement de l'image distante dans la base de données";
            }
        } else {
            $response['success'] = true;
            $response['warning'] = "Image distante non associée à un document ou utilisateur";
        }
        ob_clean();
        echo json_encode($response);
        exit;
    }

    // Vérifier si des données ont été envoyées
    if (!empty($_FILES) || !empty($_POST)) {
        if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $fileName = uniqid() . '_' . basename($file['name']); // Nom de fichier unique
        $targetPath = $uploadDir . $fileName;
        
        // Vérifie si c'est une vraie image
        $check = getimagesize($file['tmp_name']);
        if ($check !== false) {
            // Déplace le fichier uploadé
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $response['success'] = true;
                $response['path'] = '../images/' . $fileName;

                // Si l'utilisateur est connecté, on enregistre dans la base de données
                if ($is_logged_in) {
                    try {
                        $userId = $_SESSION['user_id'];
                        $documentId = isset($_POST['document_id']) ? $_POST['document_id'] : null;
                        
                        if ($documentId) {
                            // Insertion dans la base de données
                            $stmt = $connexion->prepare('INSERT INTO images (document_id, user_id, nom_fichier, chemin) VALUES (?, ?, ?, ?)');
                            $stmt->execute([$documentId, $userId, $fileName, $targetPath]);
                            
                            $response['image_id'] = $connexion->lastInsertId();
                        } else {
                            $response['warning'] = 'Image enregistrée mais non associée à un document';
                        }
                    } catch (PDOException $e) {
                        $response['warning'] = 'Image enregistrée mais non sauvegardée dans votre compte';
                        // Pour le débogage (à commenter en production) :
                        // $response['debug'] = $e->getMessage();
                    }
                } else {
                    $response['info'] = 'Connectez-vous pour sauvegarder vos images dans votre compte';
                }
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
            $response['error'] = 'Aucun fichier image n\'a été envoyé.';
        }
    } else {
        $response['success'] = false;
        $response['error'] = 'Aucune donnée n\'a été envoyée.';
    }
} else {
    $response['success'] = false;
    $response['error'] = 'Méthode non autorisée. Utilisez POST pour uploader une image.';
}

// Assurer que les erreurs PHP n'interfèrent pas avec la réponse JSON
ob_clean();
echo json_encode($response);
exit;
