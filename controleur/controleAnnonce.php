<?php
// --- Inclusion des fichiers nécessaires ---
require_once(RACINE . "/modele/bd.annonce.php");        // Inclut les fonctions liées aux annonces (ajout, modif, suppression, requêtes SQL...)
require_once(RACINE . "/modele/bd.favoris.php");         // Inclut les fonctions de gestion des favoris
require_once(RACINE . "/modele/authentification.php");  // Inclut les fonctions d'authentification (vérif connexion, rôle, etc.)
require_once(RACINE . "/modele/uploadImage.php");        // Inclut la fonction utilitaire d’upload d’image

// --- Configuration upload ---
$uploadDir = "asset/photos/";                            // Dossier de destination des fichiers uploadés
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];      // Extensions autorisées pour les images
$maxSize = 40 * 1024 * 1024;                             // Taille maximale autorisée pour l’upload (40 Mo)

// --- Paramètres GET ---
$action = $_GET['action'] ?? null;                       // Récupère l’action passée en paramètre GET, ou null si non défini
$pageAppelante = $_GET['page'] ?? 'accueil';            // Récupère la page appelante (utile pour savoir d’où l’action a été lancée)

// --- Protection pages et actions ---
protegerPagesEtActions($action, $pageAppelante);        // Fonction de sécurité : empêche les utilisateurs non connectés ou non autorisés d’effectuer certaines actions

// --- Logique par action ou page ---
gererAjoutAnnonce();                                    // Traite l'ajout d'une annonce si les conditions sont réunies
gererSuppressionAnnonce();                              // Traite la suppression d'une annonce
gererModificationAnnonce();                             // Traite la modification d'une annonce
afficherAnnonceDetaillee($pageAppelante);               // Affiche les détails d'une annonce (si un id est passé)
afficherFormulaireAjout($pageAppelante);                // Affiche le formulaire d’ajout si nécessaire
afficherFormulaireModification($action);                // Affiche le formulaire de modification si l’action correspond
afficherListePublique($pageAppelante);                  // Affiche toutes les annonces visibles (page d’accueil ou page annonce publique)
afficherMesAnnonces($pageAppelante);                    // Affiche les annonces personnelles de l’utilisateur connecté (profil)
nettoyerImagesOrphelinesManuellement();                 // Lance un nettoyage des images orphelines si demandé (option manuelle dans l’URL)





function protegerPagesEtActions($action, $page) // Fonction qui vérifie si la page ou l'action demandée nécessite une connexion utilisateur
{
    $protegees = ['favori-ajouter', 'favori-supprimer', 'modifier', 'supprimer'];
    // Liste des actions qui ne doivent être autorisées qu'à un utilisateur connecté (ex : ajouter un favori, modifier/supprimer une annonce)

    $pagesProtegees = ['ajouterAnnonce', 'mesAnnonces'];
    // Liste des pages qui doivent être protégées (ex : formulaire d’ajout, liste des annonces personnelles)

    if (in_array($action, $protegees) || in_array($page, $pagesProtegees)) {
        // Si l'action ou la page en cours fait partie des actions/pages protégées...
        redirigerSiNonConnecte();
        // ... alors on redirige l'utilisateur vers la page de connexion s'il n'est pas connecté
    }
}

function gererAjoutAnnonce()
{
    // Vérifie si la requête est un POST et que l'action demandée est "ajouter"
    if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === 'ajouter') {

        redirigerSiNonConnecte(); // Empêche un utilisateur non connecté d'ajouter une annonce

        global $uploadDir, $allowedExtensions, $maxSize;
        // On accède aux variables globales définies plus haut (chemin, extensions autorisées, taille max)

        // Récupère l'ID de l'utilisateur connecté depuis la session
        $idUtilisateur = $_SESSION['utilisateur']['id_utilisateur'];

        // Récupère et nettoie les données envoyées par le formulaire
        $titre = trim($_POST['titre']);                          // Titre de l'annonce
        $description = trim($_POST['description']);              // Description
        $statut = "en ligne";                                    // Statut par défaut : l’annonce est active
        $date_creation = date("Y-m-d");                          // Date du jour au format YYYY-MM-DD
        $marque = trim($_POST['marque']);                        // Marque du véhicule
        $modele = trim($_POST['modele']);                        // Modèle du véhicule
        $kilometrage = intval($_POST['kilometrage']);            // Conversion en entier
        $prix = floatval($_POST['prix']);                        // Conversion en float
        $annee = intval($_POST['annee']);                        // Année
        $carburant = trim($_POST['carburant']);                  // Type de carburant

        // Upload des images via la fonction uploadImage()
        $image1 = uploadImage('image1', $uploadDir, $allowedExtensions, $maxSize); // image principale (obligatoire)
        $image2 = uploadImage('image2', $uploadDir, $allowedExtensions, $maxSize); // image secondaire (facultative)
        $image3 = uploadImage('image3', $uploadDir, $allowedExtensions, $maxSize); // image secondaire (facultative)

        // Si aucune image principale n'a été envoyée ou est invalide
        if (!$image1) {
            $_SESSION['form_annonce'] = $_POST; // Sauvegarde les données du formulaire dans la session pour pré-remplir
            header("Location: index.php?page=ajouterAnnonce&error=image"); // Redirige vers le formulaire avec erreur
            exit;
        }

        // Insère l’annonce dans la base avec toutes les infos
        ajouterAnnonce(
            $titre,
            $description,
            $statut,
            $date_creation,
            $image1,
            $image2,
            $image3,
            $marque,
            $modele,
            $kilometrage,
            $prix,
            $annee,
            $carburant,
            $idUtilisateur
        );

        // Redirige vers la page "Mes annonces" avec confirmation
        header("Location: index.php?page=mesAnnonces&ajout=1");
        exit;
    }
}


function gererSuppressionAnnonce()
{
    // Vérifie si l'action "supprimer" et un identifiant sont présents dans l'URL, et si l'id est numérique
    if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'supprimer' && is_numeric($_GET['id'])) {

        // Récupère l'identifiant de l'annonce à supprimer
        $idAnnonce = intval($_GET['id']); // On force le cast en entier pour sécuriser

        // Récupère l'ID de l'utilisateur connecté (s'il existe)
        $idUtilisateur = $_SESSION['utilisateur']['id_utilisateur'] ?? null;

        // Récupère les données de l’annonce à partir de son identifiant
        $annonce = getAnnonceParId($idAnnonce);

        // Vérifie que l’annonce existe et appartient bien à l’utilisateur connecté
        if ($annonce && $annonce['id_utilisateur'] == $idUtilisateur) {
            supprimerAnnonce($idAnnonce); // Supprime l’annonce de la base
            header("Location: index.php?page=mesAnnonces&suppr=1"); // Redirige avec succès
        } else {
            header("Location: index.php?page=mesAnnonces&suppr=0"); // Redirige avec échec (annonce non trouvée ou pas la bonne personne)
        }

        exit; // Termine le script après redirection
    }
}


function gererModificationAnnonce()
{
    // Vérifie si une requête POST a été envoyée et que l'action est "modifier"
    if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === 'modifier') {

        // Vérifie que l'utilisateur est connecté
        redirigerSiNonConnecte();

        // Récupère les variables globales définies dans le contrôleur principal
        global $uploadDir, $allowedExtensions, $maxSize;

        // --- Récupération des champs du formulaire ---
        $id = intval($_POST['id_annonce']); // Identifiant de l'annonce à modifier
        $titre = trim($_POST['titre']);     // Nouveau titre
        $description = trim($_POST['description']); // Nouvelle description
        $marque = trim($_POST['marque']);   // Marque du véhicule
        $modele = trim($_POST['modele']);   // Modèle
        $kilometrage = intval($_POST['kilometrage']); // Kilométrage (entier)
        $prix = floatval($_POST['prix']);   // Prix (flottant)
        $annee = intval($_POST['annee']);   // Année de mise en circulation
        $carburant = trim($_POST['carburant']); // Type de carburant

        // --- Indicateurs de suppression des images cochés par l'utilisateur ---
        $delete1 = ($_POST['delete-image1'] ?? '') === '1'; // Image principale
        $delete2 = ($_POST['delete-image2'] ?? '') === '1'; // Image 2
        $delete3 = ($_POST['delete-image3'] ?? '') === '1'; // Image 3

        // --- Tentative de réupload de nouvelles images depuis le formulaire ---
        $image1_new = uploadImage('image1', $uploadDir, $allowedExtensions, $maxSize);
        $image2_new = uploadImage('image2', $uploadDir, $allowedExtensions, $maxSize);
        $image3_new = uploadImage('image3', $uploadDir, $allowedExtensions, $maxSize);

        // Si l'utilisateur a demandé la suppression de l'image1 mais n'en a pas uploadé de nouvelle => erreur
        if ($delete1 && empty($image1_new)) {
            header("Location: index.php?page=mesAnnonces&action=modifier&id=$id&error=image");
            exit;
        }

        // --- Définition finale des chemins d'images à sauvegarder ---
        $image1 = $image1_new ?: ($delete1 ? '' : $_POST['ancienne_image1']); // Si nouvelle image -> on garde, sinon on supprime ou conserve l’ancienne
        $image2 = $image2_new ?: ($delete2 ? '' : $_POST['ancienne_image2']);
        $image3 = $image3_new ?: ($delete3 ? '' : $_POST['ancienne_image3']);

        // Mise à jour de l’annonce avec toutes les nouvelles données
        modifierAnnonce($id, $titre, $description, $image1, $image2, $image3, $marque, $modele, $kilometrage, $prix, $annee, $carburant);

        // Redirige avec paramètre de succès
        header("Location: index.php?page=mesAnnonces&maj=1");
        exit;
    }
}


function afficherAnnonceDetaillee($page)
{
    // Vérifie si la page demandée est bien "detailAnnonce" et que l'ID de l'annonce est présent dans l'URL
    if ($page === 'detailAnnonce' && isset($_GET['id'])) {

        // Conversion sécurisée de l'identifiant de l'annonce en entier
        $idAnnonce = intval($_GET['id']);

        // Récupère les données de l'annonce via son identifiant
        $annonce = getAnnonceParId($idAnnonce);

        // Si aucune annonce trouvée (résultat vide ou null)
        if (!$annonce) {
            // Stocke un message d'erreur dans la session
            $_SESSION['message'] = "Annonce introuvable.";

            // Redirige vers la liste des annonces
            header("Location: index.php?page=annonces");
            exit;
        }

        // Si l’annonce est trouvée, on affiche la vue correspondante avec ses détails
        include(RACINE . "/vue/annonce/vueDetailAnnonce.php");
        exit; // Stoppe le script après affichage
    }
}

function afficherFormulaireAjout($page)
{
    // Vérifie si la page courante correspond à "ajouterAnnonce"
    if ($page === 'ajouterAnnonce') {

        // Vérifie si l'utilisateur est connecté, sinon redirige vers la page de connexion
        redirigerSiNonConnecte();

        // Inclut le fichier de la vue contenant le formulaire d'ajout d'annonce
        include(RACINE . "/vue/annonce/vueAjouterAnnonce.php");

        // Termine immédiatement le script pour empêcher tout autre traitement
        exit;
    }
}


function afficherFormulaireModification($action)
{
    // Vérifie si l'action passée en GET est bien "modifier"
    // et si un identifiant d'annonce est présent dans l'URL
    if ($action === 'modifier' && isset($_GET['id'])) {

        // Si l'utilisateur n'est pas connecté, on le redirige
        redirigerSiNonConnecte();

        // Récupère l'identifiant de l'annonce à modifier et le convertit en entier
        $idAnnonce = intval($_GET['id']);

        // Appelle la fonction pour récupérer les données de cette annonce dans la base
        $annonceAModifier = getAnnonceParId($idAnnonce);

        // Définit le titre de la page (utile si affiché dans la vue ou le header)
        $titrePage = "Modifier une annonce";

        // Charge la vue contenant le formulaire de modification d'annonce
        include(RACINE . "/vue/annonce/vueModifierAnnonce.php");

        // Stoppe l'exécution du script pour ne pas charger d'autres pages
        exit;
    }
}


function afficherListePublique($page)
{
    // Vérifie que la page actuelle est "annonces" (vue publique de toutes les annonces)
    if ($page === 'annonces') {

        $parPage = 4; // Nombre d'annonces à afficher par page

        // Récupère le numéro de page dans l'URL (GET), sinon 1 par défaut
        $pageActuelle = isset($_GET['p']) ? intval($_GET['p']) : 1;

        // Calcule le décalage à appliquer dans la requête SQL (OFFSET)
        $offset = ($pageActuelle - 1) * $parPage;

        // Récupère les annonces à afficher avec pagination (offset et limite)
        $annonces = getToutesLesAnnonces($offset, $parPage);

        // Compte le nombre total d'annonces disponibles en base
        $totalAnnonces = countAnnoncesTotal();

        // Calcule le nombre total de pages nécessaires
        $totalPages = ceil($totalAnnonces / $parPage);

        // Titre affiché dans la vue
        $titrePage = "Annonces";

        // Inclut la vue pour afficher les annonces publiques
        include(RACINE . "/vue/annonce/vueAnnonce.php");

        // Arrête l'exécution après avoir affiché la vue
        exit;
    }
}


function afficherMesAnnonces($page)
{
    // Vérifie si la page demandée est "mesAnnonces"
    if ($page === 'mesAnnonces') {

        // Vérifie que l'utilisateur est connecté, sinon redirige
        redirigerSiNonConnecte();

        // Récupère l'ID de l'utilisateur depuis la session
        $idUtilisateur = $_SESSION['utilisateur']['id_utilisateur'];

        $parPage = 5; // Nombre d’annonces à afficher par page

        // Récupère la page actuelle depuis l’URL, minimum 1 si non défini
        $pageActuelle = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;

        // Calcule l’offset pour la requête SQL (nombre d’annonces à ignorer)
        $offset = ($pageActuelle - 1) * $parPage;

        // Récupère les annonces de l’utilisateur avec pagination
        $mesAnnonces = getAnnoncesUtilisateurAvecLimite($idUtilisateur, $offset, $parPage);

        // Compte le nombre total d’annonces de l’utilisateur
        $nbMesAnnonces = countAnnoncesUtilisateur($idUtilisateur);

        // Calcule le nombre total de pages nécessaires
        $totalPages = ceil($nbMesAnnonces / $parPage);

        // Définit le titre de la page
        $titrePage = "Mes Annonces";

        // Inclut la vue correspondante
        include(RACINE . "/vue/annonce/vueMesAnnonces.php");

        // Termine l'exécution pour éviter tout autre affichage
        exit;
    }
}


function nettoyerImagesOrphelinesManuellement()
{
    // Vérifie si l'URL contient le paramètre ?nettoyer=1
    if (isset($_GET['nettoyer']) && $_GET['nettoyer'] === '1') {

        // S'assure que l'utilisateur est connecté avant de faire le nettoyage
        redirigerSiNonConnecte();

        // Appelle la fonction qui supprime les images non utilisées du dossier /photos/
        $nbSupprimees = nettoyerImagesOrphelines();

        // Enregistre un message de confirmation dans la session avec le nombre d'images supprimées
        $_SESSION['message'] = "$nbSupprimees image(s) orpheline(s) supprimée(s).";

        // Redirige l'utilisateur vers sa page "Mes Annonces"
        header("Location: index.php?page=mesAnnonces");

        // Arrête l'exécution du script pour empêcher tout autre traitement
        exit;
    }
}



// Fonction PHP	Description
// require_once()	Importe un fichier PHP externe une seule fois (fichiers de modèles, outils, etc.).
// $_GET['...'] ?? valeur	Récupère un paramètre GET ou retourne une valeur par défaut s’il n’existe pas.
// $_POST['...'] ?? valeur	Récupère un paramètre POST ou retourne une valeur par défaut s’il n’existe pas.
// $_SERVER["REQUEST_METHOD"]	Récupère la méthode HTTP utilisée pour la requête (GET, POST, etc.).
// in_array(valeur, tableau)	Vérifie si une valeur est présente dans un tableau.
// isset()	Vérifie si une variable ou un index de tableau est défini.
// intval()	Convertit une valeur en entier (sécurisation de l’ID ou des champs numériques).
// floatval()	Convertit une valeur en nombre à virgule flottante (ex : pour le prix).
// trim()	Supprime les espaces en début et fin de chaîne (nettoyage des champs texte).
// empty()	Vérifie si une variable est vide.
// uploadImage()	Fonction personnalisée pour uploader une image (validation, nommage, etc.).
// exit	Stoppe l’exécution du script immédiatement.
// header("Location: ...")	Effectue une redirection HTTP vers une autre page.
// date("Y-m-d")	Renvoie la date actuelle au format AAAA-MM-JJ.
// ceil()	Arrondit un nombre à l’entier supérieur (utile pour les pages de pagination).
// max(val1, val2)	Renvoie la plus grande des deux valeurs (pour éviter des pages < 1 en pagination).
// count()	Retourne le nombre d’éléments dans un tableau (ex: annonces, messages...).
// $_SESSION['...']	Stocke ou récupère une variable de session (messages flash, données utilisateur).
// include()	Inclut un fichier PHP (vue HTML, souvent utilisée pour afficher des formulaires).