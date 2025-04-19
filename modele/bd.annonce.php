<?php
require_once(RACINE . "/modele/bd.php"); // Inclusion du fichier contenant la fonction de connexion à la base de données

// Fonction pour récupérer toutes les annonces avec une pagination (offset et limit)
function getToutesLesAnnonces($offset = 0, $limit = 4)
{
    $bdd = connexionPDO(); // On ouvre une connexion PDO à la base de données

    try {
        // Préparation de la requête SQL pour récupérer les annonces
        $req = $bdd->prepare("
            SELECT a.*, u.nom, u.prenom                         -- On sélectionne toutes les colonnes de l'annonce (a.*) + le nom et prénom de l'utilisateur (propriétaire de l'annonce)
            FROM annonce a                                      -- On récupère les données depuis la table 'annonce' qu'on alias en 'a'
            JOIN utilisateur u                                  -- On fait une jointure avec la table 'utilisateur' (alias 'u') pour lier chaque annonce à son utilisateur
            ON a.id_utilisateur = u.id_utilisateur              -- La jointure s'effectue en liant l'id_utilisateur de l'annonce à celui de l'utilisateur
            ORDER BY a.date_creation DESC, a.id_annonce DESC    -- On trie les annonces de la plus récente à la plus ancienne, en cas d'égalité on trie par id décroissant
            LIMIT :limit OFFSET :offset                         -- On limite le nombre de résultats affichés avec un décalage (pour la pagination)
        ");
        $req->bindValue(':limit', $limit, PDO::PARAM_INT);   // On lie la valeur :limit au paramètre $limit (nombre maximum d’annonces à afficher)
        $req->bindValue(':offset', $offset, PDO::PARAM_INT); // On lie la valeur :offset au paramètre $offset (à partir de quel enregistrement commencer)
        $req->execute(); // Exécution de la requête SQL

        return $req->fetchAll(PDO::FETCH_ASSOC); // On retourne toutes les lignes sous forme de tableau associatif
    } catch (PDOException $e) {
        error_log("Erreur getToutesLesAnnonces : " . $e->getMessage()); // En cas d'erreur SQL, on la logue dans les fichiers du serveur
        return []; // On retourne un tableau vide pour éviter de casser l'affichage
    }
}


// Fonction qui compte le nombre total d'annonces en base
function countAnnoncesTotal()
{
    $bdd = connexionPDO(); // Connexion à la base

    try {
        $req = $bdd->query("SELECT COUNT(*) as total FROM annonce"); // Requête qui compte toutes les lignes de la table 'annonce' et les retourne sous la forme d'un champ 'total'
        return $req->fetch(PDO::FETCH_ASSOC)['total']; // On récupère le champ 'total' de la première ligne (le seul ici) et on le retourne
    } catch (PDOException $e) {
        error_log("Erreur countAnnoncesTotal : " . $e->getMessage()); // En cas d'erreur, log de l'exception SQL
        return 0; // Retourne 0 si une erreur survient pour éviter d'afficher une valeur fausse
    }
}


// Fonction pour insérer une nouvelle annonce dans la base de données
function ajouterAnnonce($titre, $description, $statut, $date, $image1, $image2, $image3, $marque, $modele, $kilometrage, $prix, $annee, $carburant, $idUtilisateur)
{
    $bdd = connexionPDO(); // Connexion à la base de données en utilisant la fonction personnalisée connexionPDO()

    try {
        // Préparation de la requête SQL d'insertion dans la table "annonce"
        $req = $bdd->prepare("
            INSERT INTO annonce (                               -- On insère une nouvelle ligne dans la table 'annonce'
                titre, description, statut, date_creation,     -- Colonnes : titre, texte descriptif, statut de publication, date de création
                image1, image2, image3,                         -- Colonnes : chemins des 3 images (la première est obligatoire, les autres sont optionnelles)
                marque, modele, kilometrage, prix, annee,       -- Colonnes : informations techniques de l’annonce (voiture)
                carburant, id_utilisateur                       -- Type de carburant et identifiant du propriétaire (FK vers utilisateur)
            ) VALUES (
                :titre, :description, :statut, :date_creation,  -- Paramètres liés pour insérer les données de manière sécurisée
                :image1, :image2, :image3,
                :marque, :modele, :kilometrage, :prix, :annee,
                :carburant, :id_utilisateur
            )
        ");

        // Exécution de la requête SQL avec un tableau associatif contenant les valeurs à insérer
        $req->execute([
            ":titre" => $titre,                     // Valeur à insérer dans la colonne 'titre'
            ":description" => $description,         // Valeur à insérer dans la colonne 'description'
            ":statut" => $statut,                   // Valeur à insérer dans la colonne 'statut'
            ":date_creation" => $date,              // Date d'ajout de l’annonce
            ":image1" => $image1,                   // Chemin de l'image principale
            ":image2" => $image2,                   // Chemin de la seconde image (optionnelle)
            ":image3" => $image3,                   // Chemin de la troisième image (optionnelle)
            ":marque" => $marque,                   // Marque du véhicule
            ":modele" => $modele,                   // Modèle du véhicule
            ":kilometrage" => $kilometrage,         // Kilométrage indiqué
            ":prix" => $prix,                       // Prix de vente du véhicule
            ":annee" => $annee,                     // Année de mise en circulation
            ":carburant" => $carburant,             // Type de carburant (ex: Essence, Diesel)
            ":id_utilisateur" => $idUtilisateur     // Identifiant de l’utilisateur qui a posté l’annonce (clé étrangère)
        ]);

        return true; // Si tout s’est bien passé, on retourne true pour signaler le succès
    } catch (PDOException $e) {
        error_log("Erreur ajouterAnnonce : " . $e->getMessage()); // En cas d’erreur, on l’enregistre dans les logs serveur
        return false; // On retourne false pour indiquer que l’insertion a échoué
    }
}

// Fonction qui récupère toutes les annonces d’un utilisateur spécifique grâce à son ID
function getAnnoncesUtilisateur($idUtilisateur)
{
    $bdd = connexionPDO(); // Connexion à la base de données via la fonction personnalisée connexionPDO()

    try {
        // Préparation de la requête SQL avec jointure
        $req = $bdd->prepare("                                  -- Préparation de la requête pour récupérer les annonces d’un utilisateur
            SELECT a.*, u.nom, u.prenom                         -- On sélectionne toutes les colonnes de la table 'annonce' (a.*) et aussi le nom et prénom du propriétaire de l’annonce
            FROM annonce a                                      -- La requête porte sur la table 'annonce' qu'on nomme ici 'a' pour simplifier l'écriture
            JOIN utilisateur u                                  -- On fait une jointure entre 'annonce' et 'utilisateur' pour accéder aux infos de l’utilisateur lié
            ON a.id_utilisateur = u.id_utilisateur              -- La jointure se fait sur l’ID de l’utilisateur (clé étrangère dans la table annonce)
            WHERE a.id_utilisateur = :id                        -- On filtre les résultats pour ne garder que ceux dont l’id_utilisateur correspond à l’ID donné en paramètre
            ORDER BY a.date_creation DESC, a.id_annonce DESC    -- On trie les résultats par date décroissante (plus récentes en premier),
                                                                -- et en cas d'égalité, par ID décroissant (dernière annonce en premier)
        ");
        $req->bindParam(':id', $idUtilisateur, PDO::PARAM_INT); // On associe le paramètre :id à la variable $idUtilisateur, en précisant que c’est un entier
        $req->execute(); // Exécution de la requête préparée

        return $req->fetchAll(PDO::FETCH_ASSOC); // On récupère toutes les lignes sous forme de tableau associatif et on les retourne
    } catch (PDOException $e) {
        error_log("Erreur getAnnoncesUtilisateur : " . $e->getMessage()); // En cas d’erreur, on log l’exception dans les journaux du serveur
        return []; // On retourne un tableau vide pour éviter une erreur dans l’affichage côté appelant
    }
}



// Fonction qui récupère une seule annonce grâce à son identifiant unique (id_annonce)
function getAnnonceParId($idAnnonce)
{
    $bdd = connexionPDO(); // Connexion à la base de données via la fonction personnalisée connexionPDO()

    try {
        // Préparation de la requête SQL pour récupérer une annonce précise et les infos de son utilisateur
        $req = $bdd->prepare("                          -- Prépare une requête SQL sécurisée avec un paramètre :id
            SELECT a.*, u.nom, u.prenom                 -- Sélectionne toutes les colonnes de l'annonce (a.*) + le nom et le prénom de l'utilisateur qui a posté l’annonce
            FROM annonce a                              -- La requête est exécutée sur la table 'annonce' aliasée en 'a'
            JOIN utilisateur u                          -- On joint la table 'utilisateur' aliasée en 'u'
            ON a.id_utilisateur = u.id_utilisateur      -- La jointure relie l’annonce à l’utilisateur qui l’a postée
            WHERE a.id_annonce = :id                    -- Filtre pour ne récupérer que l’annonce dont l’id correspond à :id (valeur fournie à l’exécution)
        ");
        $req->bindParam(':id', $idAnnonce, PDO::PARAM_INT); // Associe la variable $idAnnonce au paramètre SQL :id, en précisant qu'il s'agit d’un entier
        $req->execute(); // Exécute la requête avec le paramètre lié

        return $req->fetch(PDO::FETCH_ASSOC); // Récupère et retourne le premier résultat trouvé sous forme de tableau associatif, ou false si rien trouvé
    } catch (PDOException $e) {
        error_log("Erreur getAnnonceParId : " . $e->getMessage()); // En cas d’erreur PDO (connexion ou exécution), on l’enregistre dans le log du serveur
        return null; // En cas d’échec, retourne null pour signaler qu’aucune annonce n’a pu être récupérée
    }
}


// Fonction qui met à jour les champs d'une annonce existante dans la base de données, en fonction de son ID
function modifierAnnonce($id, $titre, $description, $image1, $image2, $image3, $marque, $modele, $kilometrage, $prix, $annee, $carburant)
{
    $bdd = connexionPDO(); // Connexion à la base de données via PDO

    try {
        // Requête SQL de mise à jour (UPDATE) avec tous les champs modifiables de l’annonce
        $sql = "UPDATE annonce SET
                    titre = :titre,                   -- Met à jour le titre de l’annonce
                    description = :description,       -- Met à jour la description de l’annonce
                    image1 = :image1,                 -- Met à jour l’image principale (obligatoire)
                    image2 = :image2,                 -- Met à jour l’image secondaire 1 (facultative)
                    image3 = :image3,                 -- Met à jour l’image secondaire 2 (facultative)
                    marque = :marque,                 -- Met à jour la marque du véhicule
                    modele = :modele,                 -- Met à jour le modèle du véhicule
                    kilometrage = :kilometrage,       -- Met à jour le kilométrage
                    prix = :prix,                     -- Met à jour le prix de l’annonce
                    annee = :annee,                   -- Met à jour l’année de mise en circulation
                    carburant = :carburant            -- Met à jour le type de carburant
                WHERE id_annonce = :id";              // Ne modifie que l’annonce dont l’id correspond

        $stmt = $bdd->prepare($sql); // Prépare la requête SQL avec les paramètres nommés

        // Exécute la requête avec les valeurs à insérer dans chaque champ
        $stmt->execute([
            ':id' => $id,                             // Identifiant de l’annonce à modifier
            ':titre' => $titre,
            ':description' => $description,
            ':image1' => $image1,
            ':image2' => $image2,
            ':image3' => $image3,
            ':marque' => $marque,
            ':modele' => $modele,
            ':kilometrage' => $kilometrage,
            ':prix' => $prix,
            ':annee' => $annee,
            ':carburant' => $carburant
        ]);

        return true; // Retourne true si la mise à jour a bien été effectuée
    } catch (PDOException $e) {
        error_log("Erreur modifierAnnonce : " . $e->getMessage()); // Enregistre un message d'erreur dans le log serveur si problème SQL
        return false; // Retourne false si une erreur survient
    }
}


// Fonction qui supprime une annonce de la base de données en fonction de son identifiant unique
function supprimerAnnonce($idAnnonce)
{
    $bdd = connexionPDO(); // Connexion à la base de données via PDO

    try {
        // Préparation de la requête SQL pour supprimer une annonce spécifique
        $req = $bdd->prepare("DELETE FROM annonce WHERE id_annonce = :id");
        // Liaison du paramètre :id avec la valeur réelle $idAnnonce passée à la fonction (type entier)
        $req->bindParam(':id', $idAnnonce, PDO::PARAM_INT);
        // Exécution de la requête DELETE
        $req->execute();

        return true; // Si la suppression s’est bien passée, retourne true
    } catch (PDOException $e) {
        // Si une erreur SQL survient (ex: base inaccessible), on enregistre le message dans le log serveur
        error_log("Erreur supprimerAnnonce : " . $e->getMessage());
        return false; // Retourne false pour signaler l’échec de la suppression
    }
}



// Fonction qui retourne le nombre total d'annonces postées par un utilisateur spécifique
function countAnnoncesUtilisateur($idUtilisateur)
{
    $bdd = connexionPDO(); // Connexion à la base de données via PDO

    try {
        // Préparation de la requête SQL pour compter les annonces liées à un utilisateur donné
        $req = $bdd->prepare("SELECT COUNT(*) FROM annonce WHERE id_utilisateur = :id");
        // Liaison de la valeur de l'ID utilisateur à la variable :id de la requête (en tant qu'entier)
        $req->bindParam(':id', $idUtilisateur, PDO::PARAM_INT);
        // Exécution de la requête
        $req->execute();

        return $req->fetchColumn();
        // Récupère la première colonne de la première ligne du résultat (le COUNT) et la retourne
    } catch (PDOException $e) {
        // En cas d'erreur SQL (problème de connexion, syntaxe, etc.), on enregistre l’erreur
        error_log("Erreur countAnnoncesUtilisateur : " . $e->getMessage());
        return 0;
        // Si une erreur est détectée, retourne 0 comme valeur par défaut
    }
}



// Fonction permettant de récupérer les annonces d’un utilisateur avec un système de pagination
function getAnnoncesUtilisateurAvecLimite($idUtilisateur, $offset, $limite)
// $idUtilisateur : ID de l'utilisateur dont on souhaite voir les annonces
// $offset : décalage à partir duquel commencer l'affichage (ex : 0, 4, 8...)
// $limite : nombre maximum d’annonces à afficher
{
    $bdd = connexionPDO(); // Connexion à la base de données via PDO

    try {
        // Préparation de la requête SQL pour récupérer les annonces de l'utilisateur avec pagination
        $req = $bdd->prepare("
            SELECT * FROM annonce                                -- On récupère toutes les colonnes de la table annonce
            WHERE id_utilisateur = :id                           -- On filtre les annonces par ID d'utilisateur
            ORDER BY date_creation DESC                          -- On trie les résultats de la plus récente à la plus ancienne
            LIMIT :limite OFFSET :offset                         -- On limite les résultats et on les décale selon la pagination
        ");
        // Liaison du paramètre :id avec la variable $idUtilisateur, en forçant le type entier
        $req->bindValue(':id', $idUtilisateur, PDO::PARAM_INT);
        // Liaison du paramètre :limite avec la variable $limite, en forçant le type entier
        $req->bindValue(':limite', $limite, PDO::PARAM_INT);
        // Liaison du paramètre :offset avec la variable $offset, en forçant le type entier
        $req->bindValue(':offset', $offset, PDO::PARAM_INT);
        // Exécution de la requête SQL préparée
        $req->execute();

        return $req->fetchAll(PDO::FETCH_ASSOC);
        // Récupération de toutes les lignes retournées sous forme de tableau associatif et renvoi au contrôleur
    } catch (PDOException $e) {
        // En cas d’erreur SQL, enregistrement du message d’erreur dans les logs
        error_log("Erreur getAnnoncesUtilisateurAvecLimite : " . $e->getMessage());
        return [];
        // Retourne un tableau vide pour éviter de casser l'affichage si erreur
    }
}


// Fonction permettant de récupérer toutes les annonces du site sans appliquer de pagination.
function getToutesLesAnnoncesSansLimite()
// Elle est utilisée lorsqu'on veut afficher toutes les annonces d’un coup (ex : dans un export ou une vue admin).
{
    $bdd = connexionPDO(); // Connexion à la base de données via la fonction personnalisée connexionPDO()

    try {
        // Préparation de la requête SQL pour récupérer toutes les annonces avec les informations utilisateur
        $req = $bdd->prepare("
            SELECT a.*, u.nom, u.prenom                         -- On sélectionne toutes les colonnes de la table annonce (a.*) + le nom et prénom de l'utilisateur lié à chaque annonce
            FROM annonce a                                      -- Table principale : annonce (aliasée 'a')
            JOIN utilisateur u                                  -- Jointure avec la table utilisateur (aliasée 'u')
            ON a.id_utilisateur = u.id_utilisateur              -- Condition de jointure : lier chaque annonce à son utilisateur grâce à l’ID
            ORDER BY a.date_creation DESC, a.id_annonce DESC    -- On trie les annonces : les plus récentes d’abord (date puis id en cas d’égalité)
        ");
        $req->execute(); // Exécution de la requête SQL

        return $req->fetchAll(PDO::FETCH_ASSOC);
        // On retourne les résultats sous forme d’un tableau associatif contenant chaque annonce + nom/prénom du propriétaire
    } catch (PDOException $e) {
        error_log("Erreur getToutesLesAnnoncesSansLimite : " . $e->getMessage());
        // En cas d’erreur SQL, on log l’erreur pour pouvoir la diagnostiquer
        return []; // En cas d’erreur, on retourne un tableau vide
    }
}


// Fonction utilitaire permettant de renvoyer le chemin vers une image si elle existe,
function getImageUrl($imageName)
// ou un chemin vers une image par défaut (placeholder) si elle est vide ou introuvable.
{
    if (empty($imageName)) {
        // Si aucun nom d’image n’est fourni (chaîne vide, null, etc.)
        return "asset/images/placeholder.jpg";
        // On retourne l’image par défaut (placeholder) pour éviter un affichage cassé
    }

    $cheminRelatif = "asset/photos/" . basename($imageName);
    // On construit le chemin relatif de l’image en extrayant uniquement le nom du fichier (sans dossier)
    // Cela permet de sécuriser le chemin contre des injections de chemin (ex: "../../etc/passwd")

    $cheminAbsolu = __DIR__ . "/../" . $cheminRelatif;
    // On construit le chemin absolu en partant du dossier actuel (__DIR__) et en remontant d’un niveau (..)
    // Ceci permet de vérifier la présence réelle du fichier sur le disque serveur

    if (file_exists($cheminAbsolu)) {
        return $cheminRelatif;
        // Si le fichier existe bien sur le serveur, on retourne son chemin relatif
        // Il sera utilisé dans un <img src="..."> côté HTML
    } else {
        return "asset/images/placeholder.jpg";
        // Si le fichier n’existe pas physiquement, on retourne une image par défaut
        // Cela évite d'afficher un lien cassé dans la page web
    }
}


// Fonction qui supprime physiquement sur le serveur les fichiers image présents dans un dossier
function nettoyerImagesOrphelines($cheminDossier = "asset/photos/")
// mais qui ne sont plus associés à aucune annonce dans la base de données.
{
    $fichiersServeur = array_diff(scandir($cheminDossier), ['.', '..']);
    // On récupère tous les fichiers présents dans le dossier (ex : asset/photos/)
    // On enlève les entrées spéciales '.' et '..' qui représentent les répertoires système

    $bdd = connexionPDO();
    // Connexion à la base de données via PDO

    try {
        $stmt = $bdd->query("SELECT image1, image2, image3 FROM annonce");
        // On récupère toutes les valeurs des colonnes image1, image2 et image3 depuis la table "annonce"
        // Ce sont les chemins d'images actuellement utilisées par les annonces

        $imagesUtilisées = [];
        // Initialisation d’un tableau qui va contenir les noms des images effectivement utilisées

        // Boucle sur chaque ligne de résultat (chaque annonce)
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            foreach (['image1', 'image2', 'image3'] as $champ) {
                if (!empty($row[$champ])) {
                    $imagesUtilisées[] = basename($row[$champ]);
                    // On extrait uniquement le nom de fichier (sans chemin) et on le stocke dans le tableau
                    // Cela permet de le comparer aux noms de fichiers présents physiquement
                }
            }
        }

        $nbSupprimees = 0;
        // Compteur pour suivre le nombre d'images supprimées

        // Parcours tous les fichiers présents sur le serveur
        foreach ($fichiersServeur as $fichier) {
            if (!in_array($fichier, $imagesUtilisées)) {
                // Si le fichier n'est pas utilisé dans la base de données

                $cheminComplet = $cheminDossier . $fichier;
                // On reconstitue le chemin complet vers le fichier

                if (is_file($cheminComplet)) {
                    // Vérifie qu’il s’agit bien d’un fichier (et non d’un répertoire)

                    unlink($cheminComplet);
                    // Supprime physiquement le fichier du dossier sur le serveur

                    $nbSupprimees++;
                    // Incrémente le compteur de fichiers supprimés
                }
            }
        }

        return $nbSupprimees;
        // Retourne le nombre total de fichiers supprimés
    } catch (PDOException $e) {
        error_log("Erreur nettoyerImagesOrphelines : " . $e->getMessage());
        // En cas d’erreur SQL, on écrit l’erreur dans les logs serveur

        return 0;
        // En cas d’échec, retourne 0 suppression
    }
}
