<?php
require_once(RACINE . "/modele/bd.utilisateur.php");
// Inclut le fichier contenant les fonctions liées à la gestion des utilisateurs (ajout, suppression, mise à jour...).

// Sécurité session
initialiserSessionEtVerifierConnexion();
// Appelle une fonction (supposée définie ailleurs) pour démarrer la session si besoin et vérifier que l’utilisateur est bien connecté.
// Si ce n’est pas le cas, cette fonction redirige l’utilisateur vers la page de connexion.

// ID utilisateur connecté
$idUtilisateur = $_SESSION['utilisateur']['id_utilisateur'];
// Récupère l’identifiant de l’utilisateur actuellement connecté depuis la session.

// GESTION DES ACTIONS POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Vérifie si la requête est de type POST, c’est-à-dire qu’un formulaire a été soumis.

    if (($_POST['action'] ?? '') === 'modifier') {
        // Si le formulaire POST contient un champ 'action' égal à 'modifier'...
        modifierProfil($idUtilisateur);
        // ...alors on appelle la fonction modifierProfil() en passant l’ID utilisateur.
        // Cette fonction gère la mise à jour du profil (nom, prénom, avatar).
    }

    if (($_POST['action'] ?? '') === 'supprimer') {
        // Si le formulaire POST contient un champ 'action' égal à 'supprimer'...
        supprimerCompte($idUtilisateur);
        // ...alors on appelle la fonction supprimerCompte() pour supprimer le compte utilisateur de la base de données.
    }
}


// Affichage de la bonne vue
afficherVueUtilisateur();

// Fonctions

function initialiserSessionEtVerifierConnexion()
{
    // Vérifie si aucune session n’est actuellement active
    if (session_status() === PHP_SESSION_NONE) {
        // Démarre une nouvelle session PHP
        session_start();
    }

    // Vérifie si l'utilisateur est connecté (présence de l'entrée 'utilisateur' dans la session)
    if (!isset($_SESSION['utilisateur'])) {
        // Si aucun utilisateur connecté, redirige vers la page de connexion
        header("Location: index.php?page=connexion");
        // Termine immédiatement le script pour éviter toute exécution non autorisée
        exit;
    }
}


function modifierProfil($idUtilisateur)
{
    // --- Nettoyage et sécurisation des champs envoyés depuis le formulaire ---

    // On récupère la valeur du champ 'nom' (ou une chaîne vide par défaut)
    // -> trim() supprime les espaces avant/après
    // -> htmlspecialchars() protège contre les injections XSS en encodant les caractères HTML (<, >, etc.)
    $nom = htmlspecialchars(trim($_POST["nom"] ?? ''));

    // Idem pour le champ 'prenom'
    $prenom = htmlspecialchars(trim($_POST["prenom"] ?? ''));

    // On récupère l'avatar actuel stocké en session, utile si l'utilisateur ne téléverse pas une nouvelle image
    $avatarPath = $_SESSION['utilisateur']['avatar'] ?? null;

    // --- Si l'utilisateur a envoyé un nouveau fichier pour l'avatar ---
    if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        // UPLOAD_ERR_OK = constante PHP intégrée, vaut 0 si l’upload s’est bien passé (aucune erreur)

        // pathinfo() récupère des infos sur le nom de fichier
        // PATHINFO_EXTENSION = constante pour obtenir l’extension (ex: "jpg", "png")
        // strtolower() = convertit l'extension en minuscules pour normaliser les comparaisons (ex: "JPG" devient "jpg")
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));

        // Extensions d'image autorisées dans notre système
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Taille maximale autorisée pour l'image : ici 40 Mo (40 * 1024 * 1024 octets)
        $maxSize = 40 * 1024 * 1024;

        // Vérifie si l'extension du fichier est dans la liste autorisée
        if (!in_array($ext, $allowedExtensions)) {
            // Si l'extension n'est pas acceptée, on redirige avec un message d'erreur
            redirigerAvecMessage("parametres", "Format d'image non supporté.");
        }

        // Vérifie que la taille du fichier ne dépasse pas la limite définie
        if ($_FILES['avatar']['size'] > $maxSize) {
            // $_FILES['avatar']['size'] donne la taille en octets
            redirigerAvecMessage("parametres", "Image trop lourde (max 40MB).");
        }

        // Vérifie le type MIME réel du fichier pour éviter l'envoi de faux fichiers images
        // mime_content_type() analyse le contenu réel du fichier temporaire (et pas juste son extension)
        $mimeType = mime_content_type($_FILES['avatar']['tmp_name']);

        // Liste des types MIME d'image autorisés
        $allowedMime = ['image/jpeg', 'image/png', 'image/gif'];

        // Vérifie que le fichier est bien une vraie image d’un type valide
        if (!in_array($mimeType, $allowedMime)) {
            redirigerAvecMessage("parametres", "Le fichier n'est pas une image valide.");
        }

        // --- Toutes les vérifications sont passées, on peut téléverser l'image ---

        // Répertoire de destination (relatif au projet) pour enregistrer les avatars
        $uploadDir = "asset/photos/";

        // uniqid() génère un identifiant unique basé sur l’heure, pour éviter les doublons de noms
        $filename = uniqid('avatar_') . "." . $ext;

        // On construit le chemin complet de l’image (relatif au projet)
        $avatarPath = $uploadDir . $filename;

        // move_uploaded_file() déplace le fichier temporaire (uploadé) vers le dossier final
        move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarPath);
    }

    // --- Mise à jour des données dans la base de données ---
    // Appel d’une fonction de modèle (bd.utilisateur.php) pour modifier les infos de l’utilisateur
    updateProfilUtilisateur($idUtilisateur, $nom, $prenom, $avatarPath);

    // --- Mise à jour de la session (pour afficher les infos sans se reconnecter) ---
    $_SESSION['utilisateur']['nom'] = $nom;
    $_SESSION['utilisateur']['prenom'] = $prenom;
    $_SESSION['utilisateur']['avatar'] = $avatarPath;

    // Redirection vers la page profil avec un paramètre dans l’URL pour afficher une alerte de succès
    header("Location: index.php?page=profil&maj=profil");
    exit; // Arrêt immédiat du script
}


function supprimerCompte($idUtilisateur)
{
    // Vérifie si l'utilisateur actuellement connecté a le rôle "admin"
    // Cela permet d'empêcher qu'un administrateur ne supprime son propre compte depuis l'espace utilisateur
    if ($_SESSION['utilisateur']['role'] === 'admin') {
        // Si c'est un admin, on le redirige avec un message d'erreur vers la page "profil"
        // Cela évite la suppression accidentelle du compte administrateur
        redirigerAvecMessage("profil", "Tu ne peux pas supprimer ton compte administrateur depuis ici.");
    }

    // Appel à la fonction supprimerUtilisateur() du fichier modele/bd.utilisateur.php
    // Cette fonction exécute une requête SQL DELETE pour supprimer l'utilisateur de la base
    supprimerUtilisateur($idUtilisateur);

    // Destruction complète de la session en cours
    // Cela déconnecte l'utilisateur et supprime toutes les données associées à sa session (id, nom, rôle, etc.)
    session_destroy();

    // Redirige immédiatement vers la page d’accueil avec un message GET "compte_supprime"
    // Cela permet d'afficher une confirmation visuelle à l'utilisateur
    header("Location: index.php?page=accueil&message=compte_supprime");

    // Termine le script immédiatement pour éviter toute exécution supplémentaire
    exit;
}



// Fonction pour afficher la vue utilisateur en fonction de la page demandée
function afficherVueUtilisateur()
{
    // Récupère la page appelée par l'utilisateur via GET, avec un fallback 'profil' si non défini
    $pageAppelante = $_GET['page'] ?? 'profil';  // Si 'page' n'est pas définie, on utilise 'profil' par défaut

    // Vérifie si la page demandée est 'parametres'
    if ($pageAppelante === "parametres") {
        // Si c'est le cas, on inclut la vue pour les paramètres utilisateur
        include(RACINE . "/vue/utilisateur/vueParametres.php");
    } else {
        // Sinon, on inclut la vue pour le profil utilisateur
        include(RACINE . "/vue/utilisateur/vueProfil.php");
    }
}


// Fonction pour rediriger l'utilisateur vers une autre page avec un message flash
function redirigerAvecMessage($page, $message)
{
    // Stocke le message dans la session, pour l'afficher après la redirection
    $_SESSION['message'] = $message;

    // Redirige l'utilisateur vers la page spécifiée dans l'URL
    header("Location: index.php?page=$page");

    // Termine le script après la redirection, évitant l'exécution du reste du code
    exit;
}





// Fonction PHP Description
// require_once()	Inclut le fichier bd.utilisateur.php contenant les fonctions de gestion utilisateur
// session_start()	Démarre une session PHP si aucune n’est active
// session_status()	Vérifie l’état actuel de la session
// isset()	Vérifie la présence d’une variable
// $_SESSION	Utilise les données de session (utilisateur, messages)
// $_GET / $_POST / $_FILES	Récupère les données envoyées par URL, formulaire ou fichier
// trim()	Supprime les espaces en début et fin de chaîne
// htmlspecialchars()	Empêche les injections XSS dans les champs texte
// header("Location: ...")	Redirige l’utilisateur vers une autre page
// exit	Termine immédiatement l’exécution du script
// pathinfo(..., PATHINFO_EXTENSION)	Récupère l’extension du fichier téléchargé
// strtolower()	Convertit une chaîne en minuscules (ex : extension de fichier)
// in_array()	Vérifie si une valeur existe dans un tableau (ex : extension autorisée)
// mime_content_type()	Vérifie le type MIME réel d’un fichier téléchargé
// uniqid()	Génère un nom de fichier unique pour éviter les doublons
// move_uploaded_file()	Déplace un fichier téléchargé vers un dossier cible
// session_destroy()	Supprime toutes les données de session (déconnexion + suppression compte)
// include()	Inclut la bonne vue (vueProfil.php ou vueParametres.php)