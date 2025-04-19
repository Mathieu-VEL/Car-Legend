<?php

// Upload une image depuis un champ de formulaire
function uploadImage($fileInputName, $uploadDir, $allowedExtensions, $maxSize)
{
    try {
        // Vérifie si le fichier a été envoyé et s’il n’y a pas eu d’erreur pendant l’upload
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return null; // Si aucune image n’a été envoyée ou qu’une erreur est survenue → retour null
        }

        $file = $_FILES[$fileInputName]; // Récupération des données du fichier envoyé
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)); // Récupère l’extension du fichier en minuscule

        // Vérifie si l’extension du fichier est dans la liste des extensions autorisées (ex : jpg, png, gif)
        if (!in_array($ext, $allowedExtensions)) {
            return null; // Extension refusée → retour null
        }

        // Vérifie que la taille du fichier n’excède pas la taille maximale définie
        if ($file['size'] > $maxSize) {
            return null; // Fichier trop lourd → retour null
        }

        // Vérifie le type MIME réel du fichier (protection contre les fichiers renommés frauduleusement)
        $mimeType = mime_content_type($file['tmp_name']); // Ex : image/jpeg
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
            return null; // Type MIME non valide → retour null
        }

        // Génère un nom unique pour le fichier (évite les doublons et les conflits)
        $filename = uniqid($fileInputName . "_") . "." . $ext;

        // Construit le chemin de destination (dossier d’upload + nom du fichier)
        $destination = $uploadDir . $filename;

        // Déplace le fichier depuis le dossier temporaire vers son emplacement final
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $destination; // Si le déplacement a réussi → on retourne le chemin final
        } else {
            return null; // Sinon → on retourne null (échec de l’enregistrement)
        }
    } catch (Throwable $e) {
        // En cas d’erreur (exception), on l’enregistre dans les logs PHP
        error_log("Erreur uploadImage [$fileInputName] : " . $e->getMessage());
        return null; // Et on retourne null
    }
}


// Récupère le chemin d’un avatar (ou retourne un placeholder si le fichier est vide ou inexistant)
function getAvatarUrl($avatar)
{
    // Vérifie si la variable $avatar est vide (pas de nom de fichier fourni)
    if (empty($avatar)) {
        return "asset/images/placeholder.jpg"; // Retourne une image par défaut si aucun avatar n’est défini
    }

    // Récupère uniquement le nom du fichier (sans le chemin) au cas où $avatar contiendrait un chemin complet
    $filename = basename($avatar);

    // Construit le chemin relatif vers le dossier des photos où sont stockés les avatars
    $path = "asset/photos/" . $filename;

    // Vérifie si le fichier existe réellement sur le serveur
    return file_exists($path) ? $path : "asset/images/placeholder.jpg";
    // Si le fichier existe, on retourne son chemin ; sinon on retourne le placeholder
}
