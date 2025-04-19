<?php
// Déconnexion de l'utilisateur
session_start(); // Démarre la session si ce n'est pas déjà fait

// Détruire toutes les variables de session
session_destroy(); // Cette fonction supprime toutes les variables liées à la session, donc l'utilisateur est déconnecté

// Redirection vers la page d'accueil
header("Location: index.php?page=accueil"); // Redirige l'utilisateur vers la page d'accueil du site (ici, la page d'accueil est définie par "accueil")
exit; // Assure que rien d'autre n'est exécuté après cette redirection
