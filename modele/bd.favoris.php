<?php
require_once(RACINE . "/modele/bd.php"); // Inclusion du fichier permettant la connexion à la base de données via PDO

// Fonction qui ajoute une annonce aux favoris d’un utilisateur
function ajouterFavori($idUtilisateur, $idAnnonce)
{
    $bdd = connexionPDO(); // Connexion à la base de données en utilisant la fonction définie dans bd.php

    try {
        // Requête SQL qui insère une nouvelle ligne dans la table "favoris"
        // La clause "INSERT IGNORE" permet d'éviter les erreurs si la combinaison utilisateur/annonce existe déjà
        $req = $bdd->prepare("INSERT IGNORE INTO favoris (id_utilisateur, id_annonce) VALUES (:idUtilisateur, :idAnnonce)");

        // Exécution de la requête avec les valeurs fournies en paramètre
        // Les clés (ex: :idUtilisateur) sont associées aux valeurs PHP passées à la fonction
        return $req->execute([
            ':idUtilisateur' => $idUtilisateur, // Liaison de l'identifiant utilisateur
            ':idAnnonce' => $idAnnonce          // Liaison de l'identifiant de l'annonce
        ]);
    } catch (PDOException $e) {
        // En cas d’exception SQL (erreur de base), on l’enregistre dans les logs pour diagnostic
        error_log("Erreur ajouterFavori : " . $e->getMessage());

        // Retourne false pour signaler que l'opération a échoué
        return false;
    }
}


// Fonction permettant de retirer une annonce des favoris d’un utilisateur
function supprimerFavori($idUtilisateur, $idAnnonce)
{
    $bdd = connexionPDO(); // Connexion à la base de données via PDO

    try {
        // Préparation de la requête SQL de suppression
        // On supprime la ligne dans la table "favoris" où l'identifiant utilisateur ET l'identifiant de l'annonce correspondent
        $req = $bdd->prepare("DELETE FROM favoris WHERE id_utilisateur = :idUtilisateur AND id_annonce = :idAnnonce");

        // Exécution de la requête avec les paramètres passés à la fonction
        // Chaque paramètre est lié à sa valeur dans le tableau associatif
        return $req->execute([
            ':idUtilisateur' => $idUtilisateur, // Liaison de l'ID utilisateur
            ':idAnnonce' => $idAnnonce          // Liaison de l'ID annonce
        ]);
    } catch (PDOException $e) {
        // En cas d'erreur SQL, on enregistre l'erreur dans les logs PHP
        error_log("Erreur supprimerFavori : " . $e->getMessage());

        // Retourne false pour indiquer que la suppression a échoué
        return false;
    }
}


// Fonction pour récupérer les annonces favorites d’un utilisateur avec pagination
function getFavorisByUtilisateurAvecLimite($idUtilisateur, $offset, $limite)
{
    $bdd = connexionPDO(); // Connexion à la base de données via PDO

    try {
        // Requête SQL permettant de récupérer les annonces favorites d’un utilisateur
        // On fait une jointure entre la table annonce et la table favoris pour ne récupérer que les annonces qui sont en favoris
        // On filtre avec l'ID utilisateur, on trie les annonces les plus récentes en premier
        // LIMIT et OFFSET permettent d’appliquer la pagination
        $sql = "SELECT * FROM annonce 
                INNER JOIN favoris ON annonce.id_annonce = favoris.id_annonce
                WHERE favoris.id_utilisateur = :idUtilisateur
                ORDER BY annonce.date_creation DESC
                LIMIT :offset, :limite";

        $stmt = $bdd->prepare($sql); // Préparation de la requête

        // Liaison du paramètre :idUtilisateur avec la valeur passée à la fonction
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT); // ID de l'utilisateur dont on récupère les favoris

        // Liaison de l’offset (décalage dans les résultats à afficher)
        // casté en (int) pour éviter les injections ou erreurs
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        // Liaison de la limite (nombre maximum d'annonces à afficher)
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);

        $stmt->execute(); // Exécution de la requête SQL

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne un tableau associatif contenant les résultats (annonces favorites)
    } catch (PDOException $e) {
        // Enregistre l’erreur dans les logs du serveur en cas d’échec SQL
        error_log("Erreur getFavorisByUtilisateurAvecLimite : " . $e->getMessage());

        return []; // Retourne un tableau vide en cas d’erreur
    }
}


// Fonction pour récupérer tous les favoris d’un utilisateur (sans pagination)
function getFavorisUtilisateur($idUtilisateur)
{
    $bdd = connexionPDO(); // Connexion à la base de données via PDO

    try {
        // Préparation de la requête SQL :
        // On sélectionne toutes les colonnes de la table "annonce" (a.*)
        // en effectuant une jointure entre "favoris" (f) et "annonce" (a)
        // Cela permet d’obtenir toutes les annonces que l'utilisateur a ajoutées à ses favoris
        $req = $bdd->prepare("
            SELECT a.* FROM favoris f
            JOIN annonce a ON f.id_annonce = a.id_annonce
            WHERE f.id_utilisateur = :idUtilisateur
            ORDER BY a.date_creation DESC
        ");

        // Liaison du paramètre :idUtilisateur à la valeur reçue par la fonction
        // Cela permet de sécuriser la requête contre les injections SQL
        $req->bindParam(":idUtilisateur", $idUtilisateur, PDO::PARAM_INT);

        $req->execute(); // Exécution de la requête SQL

        // On récupère et retourne les résultats sous forme de tableau associatif
        // Chaque élément du tableau représente une annonce en favori de l'utilisateur
        return $req->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // En cas d’erreur SQL, on log le message dans le journal d’erreurs du serveur
        error_log("Erreur getFavorisUtilisateur : " . $e->getMessage());

        return []; // Retourne un tableau vide si une erreur survient
    }
}


// Fonction pour vérifier si une annonce est déjà dans les favoris d’un utilisateur
function estFavori($idUtilisateur, $idAnnonce)
{
    $bdd = connexionPDO(); // Connexion à la base de données avec PDO

    try {
        // Préparation de la requête SQL :
        // On cherche une ligne dans la table "favoris" où les deux identifiants (utilisateur et annonce) correspondent
        // Cela permet de vérifier si l'utilisateur a déjà ajouté cette annonce à ses favoris
        $req = $bdd->prepare("
            SELECT * FROM favoris 
            WHERE id_utilisateur = :idUtilisateur AND id_annonce = :idAnnonce
        ");

        // Exécution de la requête avec les paramètres fournis :
        // - :idUtilisateur correspond à l'identifiant de l'utilisateur connecté
        // - :idAnnonce correspond à l'identifiant de l'annonce à vérifier
        $req->execute([
            ':idUtilisateur' => $idUtilisateur,
            ':idAnnonce' => $idAnnonce
        ]);

        // On utilise fetch() pour tenter de récupérer une ligne du résultat :
        // - Si une ligne est trouvée, cela signifie que l'annonce est déjà en favori => retourne true
        // - Sinon, retourne false
        return $req->fetch() !== false;
    } catch (PDOException $e) {
        // En cas d'erreur (ex. problème de base ou requête), on log l’erreur dans le fichier d’erreurs du serveur
        error_log("Erreur estFavori : " . $e->getMessage());

        // Par sécurité, on retourne false si une erreur empêche de vérifier
        return false;
    }
}


// Fonction pour compter le nombre total de favoris d’un utilisateur
function countFavorisUtilisateur($idUtilisateur)
{
    $bdd = connexionPDO(); // Connexion à la base de données via la fonction définie dans bd.php

    try {
        // Préparation de la requête SQL :
        // On compte toutes les lignes de la table "favoris" où l'id_utilisateur correspond à celui passé en paramètre
        // Cette requête renverra un seul entier représentant le nombre total de favoris pour cet utilisateur
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM favoris WHERE id_utilisateur = ?");

        // Exécution de la requête avec un paramètre positionnel :
        // Ici on passe simplement le tableau contenant l'identifiant de l'utilisateur
        $stmt->execute([$idUtilisateur]);

        // Utilisation de fetchColumn() :
        // Cela permet de récupérer directement la première colonne de la première ligne (le COUNT ici)
        return $stmt->fetchColumn(); // Retourne un entier représentant le nombre total de favoris
    } catch (PDOException $e) {
        // En cas d’erreur SQL (ex. connexion ou requête invalide), on écrit l’erreur dans les logs
        error_log("Erreur countFavorisUtilisateur : " . $e->getMessage());

        // En cas d’échec, on retourne 0 pour éviter un crash côté frontend
        return 0;
    }
}


// Élément PHP	Utilisation dans ce fichier
// connexionPDO()	Ouvre une connexion sécurisée à la base via PDO (définie dans bd.php)
// $bdd->prepare()	Prépare une requête SQL avec des paramètres nommés
// $stmt->bindValue()	Lie une valeur à un paramètre dans une requête préparée
// $stmt->bindParam()	Lie une variable (référence) à un paramètre dans une requête préparée
// $stmt->execute()	Exécute une requête SQL préparée avec les valeurs liées
// $stmt->fetch()	Récupère la première ligne du résultat (ou false si aucune trouvée)
// $stmt->fetchAll()	Récupère toutes les lignes de résultats en tableau associatif
// $stmt->fetchColumn()	Récupère une seule valeur (colonne) du premier résultat (ex: COUNT(*))
// PDO::PARAM_INT	Spécifie que le paramètre est un entier
// PDO::FETCH_ASSOC	Définit le format des résultats sous forme de tableau associatif
// error_log()	Enregistre un message d’erreur dans les logs du serveur
// INSERT IGNORE INTO	Insère une ligne seulement si elle n’existe pas déjà (évite doublons)
// INNER JOIN / JOIN	Jointure entre favoris et annonce pour croiser les données
// ORDER BY / LIMIT / OFFSET	Trie et limite les résultats pour la pagination