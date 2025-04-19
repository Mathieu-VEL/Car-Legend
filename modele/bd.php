<?php

function connexionPDO()
{
    // Déclaration des paramètres nécessaires à la connexion à la base de données
    $serveur = "localhost";        // Adresse du serveur MySQL
    $bd = "carlegend";             // Nom de la base de données à laquelle se connecter
    $login = "root";               // Nom d'utilisateur pour la connexion à MySQL
    $mdp = "";                     // Mot de passe correspondant

    try {
        // Création d'un nouvel objet PDO avec :
        // - le DSN (Data Source Name) qui contient le type de base, le serveur, le nom de la base et l'encodage
        // - l'identifiant et le mot de passe
        // - une option PDO pour initialiser l'encodage UTF-8 à chaque connexion
        $conn = new PDO(
            "mysql:host=$serveur;dbname=$bd;charset=utf8", // DSN : type, hôte, base, charset
            $login,                                         // Identifiant MySQL
            $mdp,                                           // Mot de passe MySQL
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'") // Forcer UTF-8 à l'initialisation
        );

        // Active le mode de gestion des erreurs en mode exception (très utile pour le debug)
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn; // Retourne l'objet PDO si la connexion est réussie
    } catch (PDOException $e) {
        // En cas d’erreur lors de la connexion, on enregistre l’erreur dans le fichier de log
        error_log("Erreur connexionPDO : " . $e->getMessage());

        // On retourne null pour éviter de faire planter le site brutalement (pas de die())
        return null;
    }
}
