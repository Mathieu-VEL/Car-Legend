<?php
require_once(RACINE . "/modele/bd.message.php");
// Inclusion du fichier contenant toutes les fonctions SQL liées aux messages (envoyer, lire, conversations...)


// Accès utilisateur
verifierConnexionUtilisateur();
// Fonction (à définir ou déjà définie) qui redirige si l'utilisateur n'est pas connecté (protection d'accès)


// Identifiant utilisateur connecté
$idUtilisateur = $_SESSION['utilisateur']['id_utilisateur'];
// Récupération de l'identifiant de l'utilisateur actuellement connecté depuis la session


// Logique des messages
gererEnvoiMessage($idUtilisateur);
// Gère la logique d'envoi d'un message s’il y a un formulaire POST soumis

afficherFormulaireContact($idUtilisateur);
// Affiche le formulaire "Contacter le vendeur" si la page appelée est index.php?page=contacter

afficherMessagerie($idUtilisateur);
// Affiche l’interface complète de messagerie (vueMessagerie) si page=messagerie ou page=conversation


// Fonctions

function verifierConnexionUtilisateur()
{
    // Vérifie si l'utilisateur n'est pas connecté (absence de la clé 'utilisateur' dans la session)
    if (!isset($_SESSION['utilisateur'])) {

        // Définit un message de session pour informer l’utilisateur qu’il doit se connecter
        $_SESSION['message'] = "Connecte-toi pour accéder à tes messages.";

        // Redirige automatiquement vers la page de connexion
        header("Location: index.php?page=connexion");

        // Arrête immédiatement l'exécution du script pour éviter de charger une vue interdite
        exit;
    }
}


function gererEnvoiMessage($idUtilisateur)
{
    // Vérifie si la méthode HTTP utilisée est POST ET si l'action passée en GET est bien "envoyer"
    if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_GET['action'] ?? '') === "envoyer") {

        // Récupère l'ID de l'utilisateur connecté (expéditeur du message)
        $id_expedie = $idUtilisateur;

        // Récupère l'ID du destinataire depuis les données POST, ou 0 si absent
        $id_recoit = (int) ($_POST['id_recoit'] ?? 0);

        // Récupère l'ID de l'annonce associée à la conversation
        $id_annonce = (int) ($_POST['id_annonce'] ?? 0);

        // Récupère le contenu du message et supprime les espaces inutiles
        $contenu = trim($_POST['contenu'] ?? '');

        // Vérifie que tous les champs requis sont présents et valides
        if (!$id_recoit || !$id_annonce || empty($contenu)) {
            // Si un champ est manquant ou vide, on informe l’utilisateur via un message flash
            $_SESSION['message'] = "Veuillez remplir tous les champs du message.";

            // On le redirige vers la même conversation pour corriger les erreurs
            header("Location: index.php?page=messages&action=conversation&id_annonce=$id_annonce&id_autre=$id_recoit");
            exit;
        }

        // Envoie le message via la fonction dédiée
        envoyerMessage($contenu, $id_annonce, $id_expedie, $id_recoit);

        // Redirige vers la même conversation, avec ancre vers le dernier message (id="dernier")
        header("Location: index.php?page=messages&action=conversation&id_annonce=$id_annonce&id_autre=$id_recoit#dernier");
        exit;
    }
}


function afficherFormulaireContact($idUtilisateur)
{
    // Vérifie si l'action passée en GET est bien "contacter" et que les paramètres nécessaires sont présents
    if (($_GET['action'] ?? '') === 'contacter' && isset($_GET['id_annonce'], $_GET['dest'])) {

        // Inclut les fichiers nécessaires pour récupérer l'annonce et le vendeur
        require_once(RACINE . "/modele/bd.annonce.php");
        require_once(RACINE . "/modele/bd.utilisateur.php");

        // Récupère et sécurise les identifiants de l'annonce, du destinataire et de l'utilisateur connecté (expéditeur)
        $idAnnonce = (int) $_GET['id_annonce'];
        $idDest = (int) $_GET['dest'];
        $idExpediteur = $idUtilisateur;

        // Vérifie si une conversation entre les deux utilisateurs pour cette annonce existe déjà
        if (existeConversation($idAnnonce, $idExpediteur, $idDest)) {
            // Si oui, redirige automatiquement vers cette conversation
            header("Location: index.php?page=messages&action=conversation&id_annonce=$idAnnonce&id_autre=$idDest");
            exit;
        }

        // Récupère les détails de l'annonce et du vendeur
        $annonce = getAnnonceParId($idAnnonce);         // Détails de l'annonce (titre, images, etc.)
        $vendeur = getUtilisateurParId($idDest);         // Détails du vendeur (nom, prénom, avatar...)

        // Si l’un des deux est introuvable, affiche une erreur et redirige vers la liste des annonces
        if (!$annonce || !$vendeur) {
            $_SESSION['message'] = "Annonce ou utilisateur introuvable.";
            header("Location: index.php?page=annonces");
            exit;
        }

        // Si tout est bon, on inclut la vue du formulaire de contact
        include(RACINE . "/vue/message/vueContacter.php");
        exit;
    }
}


function afficherMessagerie($idUtilisateur)
{
    // On inclut les modèles nécessaires pour récupérer les données des annonces et des utilisateurs
    require_once(RACINE . "/modele/bd.annonce.php");
    require_once(RACINE . "/modele/bd.utilisateur.php");

    // On récupère la liste des conversations de l'utilisateur (groupées par annonce et par interlocuteur)
    $conversations = getConversationsUtilisateur($idUtilisateur);

    // Initialise un tableau vide pour les messages (au cas où aucune conversation n'est sélectionnée)
    $messages = [];

    // Vérifie si l'utilisateur a cliqué sur une conversation spécifique
    if (($_GET['action'] ?? '') === 'conversation' && isset($_GET['id_annonce'], $_GET['id_autre'])) {
        // Récupère les identifiants nécessaires depuis les paramètres GET
        $idAnnonce = (int) $_GET['id_annonce'];       // ID de l'annonce liée à la conversation
        $idAutre = (int) $_GET['id_autre'];           // ID de l'interlocuteur (autre utilisateur)

        // Récupère tous les messages échangés dans cette conversation (ordre chronologique)
        $messages = getConversation($idAnnonce, $idUtilisateur, $idAutre);
    }

    // Inclut la vue principale de la messagerie (vueMessagerie.php)
    // Elle affiche la liste des conversations à gauche et les messages à droite (si sélectionnés)
    include(RACINE . "/vue/message/vueMessagerie.php");
    exit;
}





// Fonction PHP	Description
// require_once()	Inclut les fichiers nécessaires (modèles, utilitaires, etc.)
// $_SESSION[...]	Accède à la session pour l’utilisateur connecté ou pour définir un message flash
// $_GET[...] / $_POST[...]	Récupère les données envoyées par l’URL ou le formulaire
// isset()	Vérifie la présence d’une variable ou d’une clé dans un tableau
// trim()	Supprime les espaces inutiles autour du contenu du message
// header("Location: ...")	Redirige vers une autre page (ex : messagerie, annonces, etc.)
// exit	Stoppe l’exécution du script immédiatement
// (int)	Force le typage en entier pour sécuriser les ID passés par l’utilisateur
// include()	Inclut une vue spécifique (formulaire ou messagerie)