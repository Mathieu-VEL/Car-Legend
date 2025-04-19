<?php
// Vérifie si la session est déjà démarrée. Si ce n'est pas le cas, démarre une session PHP
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Démarre une session PHP pour maintenir les informations entre les pages
}

// Inclut le fichier contenant la fonction de gestion des utilisateurs (par exemple pour interagir avec la base de données)
require_once(RACINE . "/modele/bd.utilisateur.php");  // Le fichier contient des fonctions pour gérer les utilisateurs dans la base de données

// Vérifie si la méthode de requête HTTP est POST, ce qui signifie que le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    traiterInscription();  // Si le formulaire est soumis, appelle la fonction pour traiter l'inscription
}

// Si ce n'est pas une requête POST, cela signifie qu'on veut juste afficher la page d'inscription
afficherVueInscription();  // Affiche la vue du formulaire d'inscription


// Fonctions

function traiterInscription()
{
    // Récupère et nettoie les données soumises par le formulaire d'inscription
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));  // Nettoie l'email en supprimant les espaces avant et après, et échappe les caractères spéciaux pour éviter les failles XSS
    $password = $_POST['motdepasse'] ?? '';  // Récupère le mot de passe soumis, ou une chaîne vide si non défini
    $confirmation = $_POST['confirm_motdepasse'] ?? '';  // Récupère la confirmation du mot de passe
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));  // Récupère et nettoie le nom de l'utilisateur
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));  // Récupère et nettoie le prénom de l'utilisateur

    // Vérifie si l'email existe déjà dans la base de données
    if (emailExiste($email)) {
        // Si l'email est déjà utilisé, redirige avec un message d'erreur
        redirigerAvecMessage("inscription", "Cet email est déjà utilisé.");
    }

    // Vérifie si les mots de passe correspondent et si le mot de passe respecte les critères
    if (!validerMotDePasse($password, $confirmation)) {
        // Si le mot de passe n'est pas conforme, redirige avec un message d'erreur
        redirigerAvecMessage("inscription", "Le mot de passe n'est pas conforme.");
    }

    // Hache le mot de passe pour le stocker de manière sécurisée dans la base de données
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Appelle la fonction qui ajoute l'utilisateur à la base de données
    ajouterUtilisateur($email, $hash, $nom, $prenom);

    // Stocke un message de succès dans la session pour informer l'utilisateur
    $_SESSION['message'] = "Compte créé avec succès. Vous pouvez maintenant vous connecter.";

    // Redirige l'utilisateur vers la page de connexion
    header("Location: index.php?page=connexion");
    exit;  // Termine l'exécution du script pour éviter que du code supplémentaire ne soit exécuté
}


function validerMotDePasse($password, $confirmation)
{
    // Vérifie que le mot de passe et la confirmation du mot de passe sont identiques
    // La condition $password === $confirmation vérifie que les deux champs sont exactement égaux
    return $password === $confirmation
        // Vérifie que le mot de passe respecte une certaine complexité avec une expression régulière
        && preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W]).{8,}$/', $password);
}


function redirigerAvecMessage($page, $message)
{
    // Stocke le message dans la session pour pouvoir l'afficher après la redirection
    $_SESSION['message'] = $message;

    // Redirige l'utilisateur vers la page spécifiée (en ajoutant le message dans la session)
    header("Location: index.php?page=$page");

    // Termine l'exécution du script après la redirection pour éviter toute sortie HTML
    exit;
}

function afficherVueInscription()
{
    // Inclut le fichier PHP de la vue d'inscription dans le projet.
    // Cela permet d'afficher le formulaire d'inscription sur la page de l'utilisateur.
    require(RACINE . "/vue/utilisateur/vueInscription.php");
}
