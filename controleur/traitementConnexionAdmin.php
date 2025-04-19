<?php
session_start();  // Démarre une session PHP ou reprend la session existante. Cela est nécessaire pour utiliser la variable $_SESSION.

require_once("config.php");  // Inclut le fichier de configuration pour les constantes globales et paramètres de base.
require_once(RACINE . "/modele/authentification.php");  // Inclut le fichier contenant les fonctions liées à l'authentification des utilisateurs (probablement pour gérer la connexion et la vérification).


// Traitement du formulaire si envoi POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // Vérifie si la méthode de la requête HTTP est POST (ce qui signifie que le formulaire a été soumis).
    traiterConnexionAdmin();  // Appelle la fonction qui gère la connexion de l'administrateur (probablement pour valider l'email et le mot de passe, et démarrer une session d'administrateur).
}


function traiterConnexionAdmin()
{
    // Récupère l'email et le mot de passe soumis dans le formulaire, si disponibles
    $email = $_POST['email'] ?? '';  // Utilise l'email soumis dans le formulaire ou une chaîne vide si non défini
    $password = $_POST['password'] ?? '';  // Idem pour le mot de passe

    // Appelle la fonction 'verifierConnexion' pour vérifier si l'email et le mot de passe sont corrects
    $utilisateur = verifierConnexion($email, $password);

    // Connexion réussie si l'utilisateur existe et a un rôle 'admin'
    if ($utilisateur && $utilisateur['role'] === 'admin') {
        $_SESSION['utilisateur'] = $utilisateur;  // Enregistre l'utilisateur dans la session
        header("Location: ../index.php?page=admin");  // Redirige vers la page d'administration
        exit;  // Termine l'exécution pour éviter toute exécution supplémentaire
    }

    // Si la connexion échoue ou si l'utilisateur n'est pas un admin, redirige vers la page de connexion admin avec un message d'erreur
    header("Location: ../index.php?page=connexionAdmin&erreur=1");  // Redirige avec un paramètre d'erreur
    exit;  // Termine l'exécution
}
