<?php
// Gestion des erreurs
gererErreur(); // Appel immédiat à la fonction pour traiter et afficher une erreur

// Fonction
function gererErreur()
{
    $codeErreur = $_GET['code'] ?? 404;
    // On récupère le code d’erreur passé en paramètre GET (ex : ?code=404)
    // Si aucun code n’est précisé, on utilise 404 par défaut (page non trouvée)

    $titrePage = "Erreur $codeErreur";
    // On définit dynamiquement le titre de la page d'erreur (utile pour la vue)

    $messages = [
        404 => "La page que vous cherchez n'existe pas.",
        403 => "Accès interdit.",
        500 => "Erreur interne du serveur."
    ];
    // Tableau associatif contenant des messages personnalisés pour certains codes d’erreur

    $messageErreur = $messages[$codeErreur] ?? "Une erreur inconnue est survenue.";
    // Si le code d’erreur est présent dans le tableau, on récupère son message
    // Sinon on utilise un message par défaut "erreur inconnue"

    // Variables utilisées dans la vue
    require_once(RACINE . "/vue/commun/vue404.php");
    // Inclusion de la vue spécifique d'erreur qui affichera le message à l'utilisateur
    // La vue aura accès à $titrePage et $messageErreur
}
