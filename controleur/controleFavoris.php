<?php
// Inclusion du fichier contenant les fonctions liées aux favoris (ajout, suppression, récupération, etc.)
require_once(RACINE . "/modele/bd.favoris.php");

// Inclusion du fichier des fonctions liées aux annonces (nécessaire pour afficher les annonces en favoris)
require_once(RACINE . "/modele/bd.annonce.php");

// Vérifie que l'utilisateur est bien connecté, sinon le redirige
verifierSessionUtilisateur();

// Récupère l'identifiant de l'utilisateur connecté depuis la session
$idUtilisateur = $_SESSION['utilisateur']['id_utilisateur'];

// Gère la logique si un favori est ajouté (via GET ou POST)
gererAjoutFavori($idUtilisateur);

// Gère la logique si un favori est supprimé (via GET ou POST)
gererSuppressionFavori($idUtilisateur);

// Affiche la liste des annonces favorites de l'utilisateur, avec pagination
afficherFavoris($idUtilisateur);



// Fonctions

function verifierSessionUtilisateur()
{
    // Vérifie si la clé 'utilisateur' n'existe pas dans la session (c’est-à-dire que l’utilisateur n’est pas connecté)
    if (!isset($_SESSION['utilisateur'])) {
        // Redirige automatiquement l'utilisateur vers la page de connexion
        header("Location: index.php?page=connexion");
        // Termine immédiatement le script après la redirection (important pour éviter toute exécution involontaire)
        exit;
    }
}


// Fonction qui ajoute une annonce aux favoris de l'utilisateur
function gererAjoutFavori($idUtilisateur)
{
    // Vérifie si l'action est définie dans l'URL et si elle est "ajouter"
    if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'ajouter') {

        // Récupère l'identifiant de l'annonce depuis l'URL et le convertit en entier
        $idAnnonce = intval($_GET['id']);

        // Appelle la fonction pour ajouter l'annonce aux favoris dans la base de données
        ajouterFavori($idUtilisateur, $idAnnonce);

        // Prépare l'URL de redirection vers la page des annonces
        $retour = "index.php?page=annonces";

        // Si un numéro de page est spécifié dans l'URL (pour la pagination), on l'ajoute à l'URL de retour
        if (isset($_GET['p'])) {
            $retour .= "&p=" . intval($_GET['p']);
        }

        // Ajoute un ancre HTML pour que la page défile automatiquement jusqu'à l'annonce ajoutée
        $retour .= "#annonce-$idAnnonce";

        // Redirige l'utilisateur vers l'URL construite pour revenir à la page des annonces
        header("Location: $retour");

        // Termine l'exécution du script pour éviter toute action supplémentaire après la redirection
        exit;
    }
}



// Fonction pour gérer la suppression d’un favori pour un utilisateur donné
function gererSuppressionFavori($idUtilisateur)
{
    // Vérifie que les paramètres 'action' et 'id' sont présents dans l'URL et que l'action est bien "supprimer"
    if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'supprimer') {

        // Récupère l'identifiant de l'annonce à supprimer des favoris
        $idAnnonce = intval($_GET['id']); // Convertit l'ID de l'annonce en entier pour éviter les injections de code

        // Appelle la fonction pour supprimer l'annonce des favoris dans la base de données
        supprimerFavori($idUtilisateur, $idAnnonce);

        // Prépare l’URL de retour par défaut vers la page des favoris
        $retour = "index.php?page=favoris";

        // Si une page de pagination est indiquée dans l'URL, on l’ajoute à l’URL pour revenir à la bonne page
        if (isset($_GET['p'])) {
            $retour .= "&p=" . intval($_GET['p']); // Ajoute la pagination si elle est présente
        }

        // Redirige l'utilisateur vers la page des favoris après la suppression (avec pagination si nécessaire)
        header("Location: $retour");

        // Interrompt l’exécution du script pour éviter toute sortie HTML après la redirection
        exit;
    }
}


// Fonction qui affiche les annonces favorites d’un utilisateur avec pagination
function afficherFavoris($idUtilisateur)
{
    $annoncesParPage = 4; // Nombre d'annonces affichées par page

    // Récupère le numéro de la page actuelle depuis l'URL, ou 1 par défaut
    // max(1, ...) garantit que la page ne sera jamais inférieure à 1
    $pageActuelle = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;

    // Calcule l'offset pour la requête SQL : nombre d'annonces à "sauter"
    $offset = ($pageActuelle - 1) * $annoncesParPage;

    // Récupère les favoris de l’utilisateur pour cette page avec la limite et l’offset
    $mesFavoris = getFavorisByUtilisateurAvecLimite($idUtilisateur, $offset, $annoncesParPage);

    // Récupère le nombre total de favoris pour l'utilisateur (pour calculer le nombre total de pages)
    $totalFavoris = countFavorisUtilisateur($idUtilisateur);

    // Calcule le nombre total de pages nécessaires pour afficher tous les favoris
    $totalPages = ceil($totalFavoris / $annoncesParPage);

    // Affiche la vue contenant la liste des favoris paginés
    include(RACINE . "/vue/annonce/vueFavoris.php");
}



// Fonction PHP	   Description
// require_once()	Inclut les fichiers nécessaires une seule fois (fonctions modèle, vues, etc.)
// $_SESSION[...]	Accède aux données de session (utilisateur connecté)
// $_GET[...]	Récupère les paramètres de l’URL (action, id, p)
// isset()	Vérifie l’existence de clés dans un tableau ($_GET, $_SESSION)
// intval()	Convertit une valeur en entier pour sécuriser les ID
// max()	Définit une valeur minimale (ici 1) pour éviter les pages < 1
// ceil()	Calcule le nombre total de pages à partir du total de favoris
// header("Location: ...")	Redirige l’utilisateur vers une autre page (après ajout ou suppression)
// exit	Stoppe l’exécution du script immédiatement
// include()	Inclut le fichier de vue (ex : vueFavoris.php)