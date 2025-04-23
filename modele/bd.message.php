<?php

require_once(RACINE . "/modele/bd.php"); // inclusion de la connexion à la base

// Déclare une fonction qui envoie un message en base
function envoyerMessage($contenu, $id_annonce, $id_expedie, $id_recoit)
{
    $bdd = connexionPDO(); // Connexion à la base de données via la fonction personnalisée connexionPDO()

    try {
        // Requête SQL pour insérer un nouveau message dans la table 'message'
        $sql = "INSERT INTO message (contenu, date_envoi, id_annonce, id_expedie, id_recoit)
                VALUES (:contenu, NOW(), :id_annonce, :id_expedie, :id_recoit)";
        // - contenu : champ texte contenant le corps du message
        // - date_envoi : la date et l'heure d'envoi, automatiquement définie à l'instant T avec la fonction SQL NOW()
        // - id_annonce : identifiant de l’annonce concernée par le message (pour lier le message à une annonce)
        // - id_expedie : identifiant de l’utilisateur qui envoie le message
        // - id_recoit : identifiant de l’utilisateur qui reçoit le message

        $stmt = $bdd->prepare($sql); // Prépare la requête SQL pour éviter les injections SQL

        // Exécution de la requête préparée avec les paramètres liés
        return $stmt->execute([
            'contenu' => $contenu,         // Contenu réel du message à enregistrer
            'id_annonce' => $id_annonce,   // ID de l'annonce liée au message
            'id_expedie' => $id_expedie,   // ID de l’expéditeur du message
            'id_recoit' => $id_recoit      // ID du destinataire du message
        ]);
    } catch (PDOException $e) {
        // En cas d’erreur SQL, on logue l’exception dans les fichiers d’erreurs du serveur
        error_log("Erreur envoyerMessage : " . $e->getMessage());
        return false; // On retourne false pour signaler l’échec
    }
}


// Fonction pour récupérer tous les messages envoyés ou reçus par un utilisateur
function getMessagesUtilisateur($idUtilisateur)
{
    $bdd = connexionPDO(); // Connexion à la base de données via PDO

    try {
        // Requête SQL pour récupérer tous les messages liés à un utilisateur donné
        $sql = "
            SELECT m.*, 
                   a.titre AS titre_annonce,                        -- Sélectionne le titre de l'annonce liée au message (avec alias)
                   u1.prenom AS expediteur_prenom,                  -- Prénom de l'utilisateur qui a envoyé le message (alias u1)
                   u2.prenom AS destinataire_prenom                 -- Prénom de l'utilisateur qui a reçu le message (alias u2)
            FROM message m                                          -- Table principale : message (alias m)
            JOIN annonce a ON m.id_annonce = a.id_annonce           -- Jointure avec la table annonce pour accéder au titre de l’annonce
            JOIN utilisateur u1 ON m.id_expedie = u1.id_utilisateur -- Jointure pour récupérer les infos de l’expéditeur (u1)
            JOIN utilisateur u2 ON m.id_recoit = u2.id_utilisateur  -- Jointure pour récupérer les infos du destinataire (u2)
            WHERE m.id_expedie = :id OR m.id_recoit = :id           -- On filtre : on ne prend que les messages où l'utilisateur est soit expéditeur, soit destinataire
            ORDER BY m.date_envoi DESC                              -- On trie les messages par date d'envoi, du plus récent au plus ancien
        ";

        $stmt = $bdd->prepare($sql); // Prépare la requête SQL pour éviter les injections
        $stmt->execute(['id' => $idUtilisateur]); // Exécution avec le paramètre d'utilisateur lié deux fois (expéditeur OU destinataire)

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne tous les messages sous forme de tableau associatif
    } catch (PDOException $e) {
        // En cas d’erreur SQL, on écrit le message d’erreur dans les logs
        error_log("Erreur getMessagesUtilisateur : " . $e->getMessage());
        return []; // Retourne un tableau vide en cas d’échec
    }
}


// Fonction qui retourne la liste des conversations distinctes pour un utilisateur donné
function getConversationsUtilisateur($idUtilisateur)
{
    $bdd = connexionPDO(); // Connexion à la base de données

    try {
        // Requête SQL pour récupérer les conversations de l'utilisateur
        $sql = "
            SELECT DISTINCT                                                     -- On sélectionne uniquement les lignes distinctes (pas de doublons)
                a.id_annonce,                                                   -- Récupère l'identifiant de l'annonce concernée par la conversation
                a.titre AS titre_annonce,                                       -- Récupère le titre de l'annonce (avec un alias)
                u.id_utilisateur,                                               -- Identifiant de l'interlocuteur (l’autre utilisateur dans la conversation)
                u.prenom,                                                       -- Prénom de l'interlocuteur
                u.nom,                                                          -- Nom de l'interlocuteur
                u.avatar                                                        -- Avatar de l'interlocuteur (affichage dans la messagerie)
            FROM message m                                                      -- On récupère depuis la table message (alias m)
            JOIN annonce a ON m.id_annonce = a.id_annonce                       -- Jointure pour récupérer les informations de l’annonce associée au message
            JOIN utilisateur u ON                                               -- On récupère les infos de l’autre utilisateur de la conversation :
                (u.id_utilisateur = m.id_expedie AND m.id_recoit = :id)         -- Si l’autre a envoyé un message à l'utilisateur
                OR (u.id_utilisateur = m.id_recoit AND m.id_expedie = :id)      -- Ou si l’utilisateur a envoyé un message à l’autre
            WHERE m.id_expedie = :id OR m.id_recoit = :id                       -- L’utilisateur est impliqué dans la conversation (peu importe le sens)
            ORDER BY a.titre, u.nom                                             -- On trie les résultats par titre d’annonce, puis par nom de l'interlocuteur
        ";

        $stmt = $bdd->prepare($sql); // Préparation de la requête
        $stmt->execute(['id' => $idUtilisateur]); // Exécution avec le paramètre utilisateur
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retour des conversations sous forme de tableau associatif
    } catch (PDOException $e) {
        // Log de l’erreur en cas de problème SQL
        error_log("Erreur getConversationsUtilisateur : " . $e->getMessage());
        return []; // Retour d’un tableau vide en cas d’erreur
    }
}


// Fonction qui récupère tous les messages d'une conversation entre deux utilisateurs pour une annonce donnée
function getConversation($idAnnonce, $idUtilisateur, $idAutre)
{
    $bdd = connexionPDO(); // Connexion à la base de données via PDO

    try {
        // Requête SQL pour récupérer tous les messages échangés entre deux utilisateurs pour une même annonce
        $sql = "
            SELECT m.*, 
                   u1.prenom AS expediteur_prenom,                  -- On récupère le prénom de l'expéditeur (alias u1)
                   u2.prenom AS destinataire_prenom                 -- Et le prénom du destinataire (alias u2)
            FROM message m                                          -- Depuis la table des messages (alias m)
            JOIN utilisateur u1 ON m.id_expedie = u1.id_utilisateur -- Jointure avec l'expéditeur (u1) pour récupérer ses infos
            JOIN utilisateur u2 ON m.id_recoit = u2.id_utilisateur  -- Jointure avec le destinataire (u2) pour récupérer ses infos
            WHERE m.id_annonce = :id_annonce                        -- On filtre les messages liés à une annonce spécifique

              AND (                                                 -- On veut uniquement les messages échangés entre les deux utilisateurs
                (m.id_expedie = :id1 AND m.id_recoit = :id2)        -- Cas 1 : utilisateur actuel a envoyé un message à l’autre
                OR 
                (m.id_expedie = :id2 AND m.id_recoit = :id1)        -- Cas 2 : l’autre utilisateur a répondu
              )

            ORDER BY m.date_envoi ASC                               -- On trie les messages du plus ancien au plus récent (ordre chronologique)
        ";

        $stmt = $bdd->prepare($sql); // Préparation de la requête SQL
        $stmt->execute([
            'id_annonce' => $idAnnonce,      // On lie l'identifiant de l'annonce
            'id1' => $idUtilisateur,         // L'utilisateur connecté
            'id2' => $idAutre                // L'autre participant à la conversation
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne tous les messages de la conversation sous forme de tableau associatif
    } catch (PDOException $e) {
        error_log("Erreur getConversation : " . $e->getMessage()); // Enregistre l'erreur en cas d'échec
        return []; // Retourne un tableau vide si erreur
    }
}


// Fonction qui vérifie si une conversation existe déjà entre deux utilisateurs pour une annonce donnée
function existeConversation($idAnnonce, $id1, $id2)
{
    $bdd = connexionPDO(); // Connexion à la base de données

    try {
        // Requête SQL qui compte le nombre de messages échangés entre deux utilisateurs sur une même annonce
        $sql = "
            SELECT COUNT(*) FROM message                        -- On compte le nombre total de messages trouvés
            WHERE id_annonce = :id_annonce                      -- On filtre uniquement les messages liés à l'annonce fournie

            AND (                                               -- On vérifie si l’un a déjà envoyé un message à l’autre (dans les deux sens)
                (id_expedie = :id1 AND id_recoit = :id2)        -- Cas 1 : id1 a envoyé un message à id2
                OR 
                (id_expedie = :id2 AND id_recoit = :id1)        -- Cas 2 : id2 a envoyé un message à id1
            )
        ";

        $stmt = $bdd->prepare($sql);    // Préparation de la requête
        $stmt->execute([                // Exécution avec liaison des paramètres
            'id_annonce' => $idAnnonce, // ID de l'annonce concernée
            'id1' => $id1,              // Premier utilisateur (souvent l'utilisateur connecté)
            'id2' => $id2               // Deuxième utilisateur (l'interlocuteur)
        ]);

        return $stmt->fetchColumn() > 0; // On récupère le nombre et on retourne true si au moins un message existe
    } catch (PDOException $e) {
        error_log("Erreur existeConversation : " . $e->getMessage()); // Enregistre l'erreur dans le log serveur
        return false; // Retourne false en cas d'erreur (pas de conversation)
    }
}


// Fonction PHP	Description
// require_once()	Inclut un fichier une seule fois (ici, bd.php pour la connexion à la BDD).
// connexionPDO()	Fonction définie dans bd.php, qui retourne un objet PDO pour se connecter à la base.
// prepare()	Prépare une requête SQL sécurisée avec des paramètres nommés (:param) pour éviter les injections.
// execute()	Exécute la requête préparée avec un tableau de valeurs associées aux paramètres SQL.
// fetch()	Récupère la première ligne du résultat SQL sous forme de tableau associatif.
// fetchAll()	Récupère toutes les lignes du résultat sous forme de tableau associatif.
// fetchColumn()	Récupère la valeur de la première colonne de la première ligne du résultat (souvent un COUNT).
// error_log()	Enregistre un message d’erreur dans les logs du serveur (utile en cas d’erreur SQL).