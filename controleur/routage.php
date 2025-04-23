<?php

// Fonction qui gère la redirection vers la bonne page en fonction du paramètre "page" dans l'URL
function redirigeVers()
{
    // Tableau associatif contenant les routes de l'application
    // Les clés sont les noms des pages, les valeurs sont les fichiers PHP correspondants
    $routes = [
        "accueil"         => "controleAccueil.php",       // Page d'accueil
        "connexion"       => "connexion.php",             // Page de connexion
        "connexionAdmin"  => "connexionAdmin.php",        // Page de connexion pour l'admin
        "inscription"     => "inscription.php",           // Page d'inscription
        "deconnexion"     => "deconnexion.php",           // Page de déconnexion
        "profil"          => "controleUtilisateur.php",   // Page du profil utilisateur
        "parametres"      => "controleUtilisateur.php",   // Page des paramètres utilisateur
        "annonces"        => "controleAnnonce.php",       // Page des annonces
        "mesAnnonces"     => "controleAnnonce.php",       // Page des annonces de l'utilisateur
        "ajouterAnnonce"  => "controleAnnonce.php",       // Page pour ajouter une annonce
        "detailAnnonce"   => "controleAnnonce.php",       // Page de détail d'une annonce
        "favoris"         => "controleFavoris.php",       // Page des favoris de l'utilisateur
        "messages"        => "controleMessage.php",       // Page de messagerie
        "admin"           => "admin.php",                 // Page d'administration
        "erreur"          => "controleErreur.php"         // Page d'erreur (404, etc.)
    ];

    // Récupère la page demandée depuis l'URL, ou "accueil" par défaut
    $page = $_GET['page'] ?? 'accueil';

    // Cas particulier pour les vues simples qui ne sont pas des pages contrôlées par un fichier PHP
    if ($page === "rgpd") {
        // Redirige vers la page RGPD
        return __DIR__ . "/../vue/commun/vueRGPD.php";
    }

    if ($page === "apropos") {
        // Redirige vers la page "Qui sommes-nous ?"
        return __DIR__ . "/../vue/commun/vueQuiSommesNous.php";
    }

    // Si la page demandée existe dans le tableau des routes
    if (array_key_exists($page, $routes)) {
        // Récupère le chemin absolu du fichier correspondant à la page
        $chemin = __DIR__ . "/" . $routes[$page];

        // Vérifie si le fichier existe
        if (file_exists($chemin)) {
            return $chemin; // Retourne le chemin complet du fichier
        }
    }

    // Si la page demandée n'existe pas ou si le fichier correspondant est introuvable
    // Redirige l'utilisateur vers la page d'erreur 404
    header("Location: index.php?page=erreur&code=404");
    exit;
}



// Fonction PHP	Description
// $_GET	Récupère les données envoyées via l’URL (paramètre page ici)
// __DIR__	Donne le chemin absolu du dossier courant (utile pour les chemins de fichier)
// array_key_exists()	Vérifie si une clé existe dans un tableau (ici dans $routes)
// file_exists()	Vérifie si un fichier existe physiquement sur le serveur
// header("Location:")	Redirige l’utilisateur vers une autre page avec le protocole HTTP
// exit	Termine immédiatement l’exécution du script PHP