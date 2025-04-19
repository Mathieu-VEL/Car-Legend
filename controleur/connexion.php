<?php
// Vérifie si aucune session n’est active, et en démarre une si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarre la session pour pouvoir utiliser $_SESSION (messages, identifiants, etc.)
}

require_once(__DIR__ . "/config.php");
require_once(RACINE . "/modele/bd.utilisateur.php");
require_once(RACINE . "/modele/authentification.php");

// Appelle la fonction qui va gérer tout le processus de connexion (validation, redirection, message...)
traiterConnexion();

// Affichage de la vue connexion (par défaut)
require(RACINE . "/vue/utilisateur/vueConnexion.php"); // Inclut le fichier de la vue qui contient le formulaire de connexion HTML.
// Le chemin est construit à partir de la constante RACINE, ce qui garantit que le fichier est bien localisé, peu importe où se trouve ce script.
// Cette vue s'affiche si aucune redirection n'a été déclenchée auparavant.

// Gère le POST de connexion si présent
function traiterConnexion()
{
    // Vérifie que la requête est bien un envoi de formulaire (méthode POST)
    if ($_SERVER["REQUEST_METHOD"] !== "POST") return;

    // Récupère et nettoie l'email envoyé depuis le formulaire (trim + htmlspecialchars pour éviter les injections XSS)
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));

    // Récupère directement le mot de passe brut envoyé par le formulaire (pas besoin de trim ici)
    $password = $_POST['motdepasse'] ?? '';

    // Si l'un des champs est vide, on affiche un message d'erreur et on redirige
    if (empty($email) || empty($password)) {
        setMessageEtRediriger('Tous les champs sont requis.', 'error');
    }

    // Vérifie dans la base si les identifiants sont corrects (fonction définie dans authentification.php)
    $utilisateur = verifierConnexion($email, $password);

    // Si l'utilisateur est trouvé (email + mot de passe valide)
    if ($utilisateur) {
        // On enregistre les infos de l'utilisateur dans la session
        $_SESSION['utilisateur'] = $utilisateur;

        // On redirige avec un message de succès vers la page profil
        setMessageEtRediriger('Connexion réussie !', 'success', 'profil');
    } else {
        // Sinon, on affiche une erreur et on reste sur la page de connexion
        setMessageEtRediriger('Email ou mot de passe incorrect.', 'error');
    }
}


// Définit un message flash (affiché une seule fois) et redirige vers une page donnée
function setMessageEtRediriger($texte, $type, $page = 'connexion')
{
    // Stocke un message flash dans la session avec deux informations :
    // - texte : le contenu du message (ex : "Connexion réussie !")
    // - type  : le type de message (ex : "success", "error", utilisé pour le style CSS)
    $_SESSION['message'] = [
        'texte' => $texte,
        'type' => $type
    ];

    // Redirige immédiatement vers la page spécifiée (par défaut : page=connexion)
    header("Location: index.php?page=$page");

    // Stoppe l'exécution du script après la redirection
    exit;
}
