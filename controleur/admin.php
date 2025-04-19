<?php
// Vérifie si une session est déjà active, sinon en démarre une
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarre la session PHP pour accéder aux variables $_SESSION
}

// Inclusion du fichier de configuration globale (chemin RACINE, constantes...)
require_once("config.php");

// Inclusion des fonctions d'authentification (par ex. vérification de rôle, login, etc.)
require_once(RACINE . "/modele/authentification.php");

// Inclusion des fonctions de manipulation des utilisateurs (CRUD utilisateurs)
require_once(RACINE . "/modele/bd.utilisateur.php");

// Inclusion des fonctions de manipulation des annonces (CRUD annonces)
require_once(RACINE . "/modele/bd.annonce.php");

// Vérifie si l'utilisateur est connecté et a le rôle 'admin'.
// Si ce n'est pas le cas, l'utilisateur est redirigé (souvent vers une page de connexion ou erreur)
redirigerSiNonAdmin();

// Fonction interne qui traite les actions admin (suppression utilisateur ou suppression annonce)
// Cela signifie que si une requête POST ou GET avec une action est présente, elle est traitée ici
traiterActionsAdmin();

// Récupère la liste complète des utilisateurs depuis la base de données
$utilisateurs = getAllUtilisateurs();

// Récupère toutes les annonces (sans limite de pagination) pour affichage dans le tableau admin
$annonces = getToutesLesAnnoncesSansLimite();

// Inclut la vue correspondante à l'administration (interface d'affichage HTML)
include(RACINE . "/vue/admin/vueAdmin.php");


// Gère les actions passées en GET (depuis l'URL de l'interface admin)
function traiterActionsAdmin()
{
    // Récupère l'action depuis l'URL (ex : ?action=supprimerUtilisateur)
    // Si aucun paramètre 'action' n’est défini, la valeur par défaut est une chaîne vide ''
    $action = $_GET['action'] ?? '';

    // Récupère l'identifiant (ex : ?id=5), en le convertissant en entier pour éviter les injections
    // Si l’id n’est pas présent dans l’URL, on stocke null
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    // Si l'action demandée est de supprimer un utilisateur, et qu'un id est bien fourni
    if ($action === 'supprimerUtilisateur' && $id !== null) {
        // Appelle la fonction de suppression d’un utilisateur (spécifique admin)
        supprimerUtilisateurAdmin($id);
    }

    // Si l'action demandée est de supprimer une annonce, et qu'un id est bien fourni
    if ($action === 'supprimerAnnonce' && $id !== null) {
        // Appelle la fonction de suppression d’une annonce (spécifique admin)
        supprimerAnnonceAdmin($id);
    }
}


// Supprime un utilisateur depuis le panneau admin avec une sécurité pour empêcher l’admin de se supprimer lui-même
function supprimerUtilisateurAdmin($id)
{
    // Récupère l’ID de l’utilisateur actuellement connecté (l’admin)
    $idAdmin = (int) $_SESSION['utilisateur']['id_utilisateur'];

    // Vérifie si l'ID à supprimer est celui de l’admin lui-même
    if ($id === $idAdmin) {
        // Message de protection : l’admin ne peut pas se supprimer lui-même
        $_SESSION['message'] = "Tu ne peux pas supprimer ton propre compte admin.";
    } else {
        // Sinon, on supprime l'utilisateur ciblé
        supprimerUtilisateur($id);

        // Message de confirmation affiché via le système de message flash
        $_SESSION['message'] = "Utilisateur supprimé avec succès.";
    }

    // Redirige vers la page d’administration après l’action
    header("Location: index.php?page=admin");

    // On arrête l’exécution du script pour éviter toute suite inutile
    exit;
}


// Supprime une annonce depuis le panneau admin, puis redirige avec un message
function supprimerAnnonceAdmin($id)
{
    // Appelle la fonction modèle pour supprimer l’annonce dont l’identifiant est passé en paramètre
    supprimerAnnonce($id);

    // Stocke un message de confirmation dans la session (affiché sur la vue admin après redirection)
    $_SESSION['message'] = "Annonce supprimée avec succès.";

    // Redirige l’admin vers la page d’administration pour voir le résultat de l’action
    header("Location: index.php?page=admin");

    // Interrompt immédiatement le script pour éviter toute exécution après la redirection
    exit;
}
