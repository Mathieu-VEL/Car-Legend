<?php
require_once(RACINE . "/modele/bd.php");

// Vérifie si un email est déjà enregistré
function emailExiste($email)
{
    $bdd = connexionPDO(); // Connexion à la base de données via la fonction personnalisée connexionPDO()

    try {
        // Préparation de la requête SQL :
        // On cherche l'identifiant d'un utilisateur ayant cet email dans la table 'utilisateur'
        $req = $bdd->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = :email");

        // Exécution de la requête avec le paramètre fourni (valeur sécurisée avec bind automatique)
        $req->execute([':email' => $email]);

        // Si la requête retourne au moins un résultat (fetch() !== false), cela signifie que l'email existe déjà
        return $req->fetch() !== false;
    } catch (PDOException $e) {
        // En cas d'erreur SQL, on logue le message dans les fichiers d'erreur du serveur
        error_log("Erreur emailExiste : " . $e->getMessage());
        // En cas d'erreur, on retourne false (considère que l’email n’existe pas)
        return false;
    }
}

// Ajoute un nouvel utilisateur dans la base de données
function ajouterUtilisateur($email, $password_hash, $nom, $prenom)
{
    $bdd = connexionPDO(); // Connexion à la base de données en utilisant la fonction connexionPDO()

    try {
        // Préparation de la requête SQL d'insertion
        // On insère les champs : email, mot de passe (déjà hashé), nom, prénom, rôle (fixé à "utilisateur") et la date d'inscription (CURDATE() retourne la date du jour)
        $req = $bdd->prepare("
            INSERT INTO utilisateur (email, password, nom, prenom, role, date_inscription)
            VALUES (:email, :password, :nom, :prenom, 'utilisateur', CURDATE())
        ");

        // Exécution de la requête avec les données fournies
        // Les données sont passées sous forme de tableau associatif pour lier les valeurs aux placeholders dans la requête
        return $req->execute([
            ':email' => $email,                   // Email fourni par l'utilisateur
            ':password' => $password_hash,        // Mot de passe déjà sécurisé avec password_hash()
            ':nom' => $nom,                       // Nom de l'utilisateur
            ':prenom' => $prenom                  // Prénom de l'utilisateur
        ]);
    } catch (PDOException $e) {
        // En cas d'erreur (problème de connexion ou SQL), on enregistre l’erreur dans les logs du serveur
        error_log("Erreur ajouterUtilisateur : " . $e->getMessage());
        return false; // Retourne false si l'insertion échoue
    }
}


// Récupère tous les utilisateurs (ex : pour l'affichage dans une page d'administration)
function getAllUtilisateurs()
{
    $bdd = connexionPDO(); // Connexion à la base de données via la fonction personnalisée connexionPDO()

    try {
        // Exécution directe d'une requête SQL pour récupérer tous les utilisateurs
        // On trie les résultats par date d'inscription, du plus récent au plus ancien
        $req = $bdd->query("SELECT * FROM utilisateur ORDER BY date_inscription DESC");

        // On récupère tous les résultats sous forme de tableau associatif
        return $req->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // En cas d'erreur (ex : problème de requête), on log l’erreur dans le fichier d’erreurs PHP
        error_log("Erreur getAllUtilisateurs : " . $e->getMessage());
        return []; // On retourne un tableau vide pour ne pas casser l’affichage
    }
}


// Supprime un utilisateur par son ID (ex : depuis un compte admin ou lors d’une suppression de compte)
function supprimerUtilisateur($id)
{
    $bdd = connexionPDO(); // Connexion à la base de données via PDO

    try {
        // Préparation de la requête SQL de suppression
        // On cible l'utilisateur dont l'identifiant (clé primaire) correspond à l’ID fourni
        $req = $bdd->prepare("DELETE FROM utilisateur WHERE id_utilisateur = :id");

        // Exécution de la requête avec un tableau associatif contenant l'identifiant
        // Cela permet d’éviter les injections SQL (sécurisé via requête préparée)
        return $req->execute([':id' => $id]);
    } catch (PDOException $e) {
        // En cas d’erreur SQL, on log l’erreur dans le fichier de log PHP
        error_log("Erreur supprimerUtilisateur : " . $e->getMessage());

        // On retourne false pour signaler que la suppression n’a pas pu être effectuée
        return false;
    }
}


// Met à jour les informations du profil utilisateur (nom, prénom et avatar)
function updateProfilUtilisateur($id, $nom, $prenom, $avatar)
{
    $bdd = connexionPDO(); // Connexion à la base de données via PDO

    try {
        // Requête SQL de mise à jour des colonnes nom, prenom et avatar
        // pour l'utilisateur dont l'ID correspond à :id
        $sql = "UPDATE utilisateur
                SET nom = :nom, prenom = :prenom, avatar = :avatar
                WHERE id_utilisateur = :id";

        // Préparation sécurisée de la requête SQL
        $req = $bdd->prepare($sql);

        // Exécution de la requête avec les valeurs liées via un tableau associatif
        // - :nom correspond au nouveau nom
        // - :prenom au nouveau prénom
        // - :avatar au chemin de la nouvelle image de profil (ou null)
        // - :id est l’identifiant de l’utilisateur à mettre à jour
        return $req->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':avatar' => $avatar,
            ':id' => $id
        ]);
    } catch (PDOException $e) {
        // En cas d’erreur SQL, on la consigne dans les logs
        error_log("Erreur updateProfilUtilisateur : " . $e->getMessage());

        // Retourne false pour signaler que la mise à jour a échoué
        return false;
    }
}


// Récupère un utilisateur spécifique en fonction de son identifiant unique
function getUtilisateurParId($id)
{
    $bdd = connexionPDO(); // Connexion à la base de données via PDO

    try {
        // Requête SQL pour récupérer tous les champs d'un utilisateur
        // correspondant à un identifiant spécifique (clé primaire)
        $sql = "SELECT * FROM utilisateur WHERE id_utilisateur = :id";

        // Préparation de la requête SQL pour éviter les injections SQL
        $stmt = $bdd->prepare($sql);

        // Exécution de la requête avec la valeur de l'identifiant passée en paramètre
        // Ici on utilise un tableau associatif pour lier :id à la valeur $id
        $stmt->execute(['id' => $id]);

        // On récupère une seule ligne (fetch) sous forme de tableau associatif
        // Cela retourne les infos de l'utilisateur ou false si non trouvé
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // En cas d’erreur SQL, on la consigne dans les logs pour analyse
        error_log("Erreur getUtilisateurParId : " . $e->getMessage());

        // Retourne null si la récupération échoue
        return null;
    }
}
