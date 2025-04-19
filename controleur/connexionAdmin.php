<?php
// Démarrer la session si elle n'est pas encore active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Permet de manipuler les variables de session (utile pour la connexion, les messages flash, etc.)
}

require_once("config.php"); // Inclusion du fichier de configuration global contenant la constante RACINE

// Inclusion de la vue du formulaire de connexion admin
// Cela affichera le formulaire HTML pour permettre à un administrateur de se connecter
include(RACINE . "/vue/admin/vueConnexionAdmin.php");
