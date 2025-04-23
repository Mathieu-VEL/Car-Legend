<?php
require_once(RACINE . "/modele/bd.php"); // Inclusion de la fonction de connexion à la base de données

// Vérifie les identifiants de connexion fournis
function verifierConnexion($email, $password)
{
    $bdd = connexionPDO(); // Connexion à la base de données

    try {
        $req = $bdd->prepare("SELECT * FROM utilisateur WHERE email = :email"); // Requête pour récupérer l'utilisateur avec cet email
        $req->execute([':email' => $email]); // Exécution de la requête
        $user = $req->fetch(PDO::FETCH_ASSOC); // Récupération du résultat sous forme de tableau associatif

        // Si l'utilisateur existe et que le mot de passe est correct
        if ($user && password_verify($password, $user['password'])) {
            return $user; // On retourne les informations de l'utilisateur
        }

        return false; // Sinon, échec de la connexion

    } catch (PDOException $e) {
        error_log("Erreur dans verifierConnexion : " . $e->getMessage()); // En cas d'erreur, on log l'erreur côté serveur
        return false; // Retourne false si une exception est levée
    }
}

// Redirige vers la page de connexion si l'utilisateur n'est pas connecté
function redirigerSiNonConnecte()
{
    if (!isset($_SESSION['utilisateur'])) {
        header("Location: index.php?page=connexion"); // Redirection vers la page de connexion
        exit; // Interrompt l'exécution du script
    }
}

// Redirige vers la page admin si l'utilisateur n'est pas administrateur
function redirigerSiNonAdmin()
{
    if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
        header("Location: index.php?page=connexionAdmin&erreur=acces"); // Redirection vers la connexion admin avec erreur
        exit; // Arrêt du script
    }
}



// Élément PHP	Rôle
// function nomFonction()	Déclaration d’une fonction utilisateur
// connexionPDO()	Fonction personnalisée de connexion à la base (définie dans bd.php)
// try { ... } catch (PDOException)	Gestion des exceptions liées à PDO
// $bdd->prepare()	Prépare une requête SQL sécurisée avec PDO
// $req->execute()	Exécute une requête préparée avec des paramètres
// $req->fetch(PDO::FETCH_ASSOC)	Récupère une ligne de résultat sous forme de tableau associatif
// password_verify()	Vérifie si un mot de passe correspond à un hash stocké
// return	Renvoie une valeur depuis une fonction
// isset()	Vérifie si une variable est définie et non nulle
// $_SESSION	Superglobale contenant les données de session
// header()	Redirige vers une autre page
// exit	Interrompt immédiatement le script PHP
// error_log()	Enregistre un message dans le fichier de logs du serveur