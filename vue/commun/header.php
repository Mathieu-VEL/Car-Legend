<?php
// inclusion du script d'upload d'image
require_once(RACINE . "/modele/uploadImage.php");

// définition du titre du site
$titreSite = "CAR LEGEND";

// récupération de l'utilisateur connecté s'il existe
$utilisateur = $_SESSION['utilisateur'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="asset/images/favicon.ico" type="image/x-icon">
    <title><?= $titreSite; ?></title>

    <?php // feuille de style principale compilée depuis SCSS 
    ?>
    <link rel="stylesheet" href="asset/css/dist/style.css">

    <?php // scripts JS du projet, tous différés pour un chargement optimisé 
    ?>
    <script src="asset/js/alertes.js" defer></script>
    <script src="asset/js/favoris.js" defer></script>
    <script src="asset/js/ajouteAnnonce.js" defer></script>
    <script src="asset/js/modifierAnnonce.js" defer></script>
    <script src="asset/js/detailAnnonce.js" defer></script>
    <script src="asset/js/validationAnnonce.js" defer></script>
    <script src="asset/js/modificationSucces.js" defer></script>
    <script src="asset/js/suppressionAnnonce.js" defer></script>
    <script src="asset/js/alerteAnnoncePublie.js" defer></script>
    <script src="asset/js/alerteProfil.js" defer></script>
    <script src="asset/js/popupSuppressionCompte.js" defer></script>
    <script src="asset/js/formulaire.js" defer></script>
    <script src="asset/js/api.js" defer></script>
    <script src="asset/js/header.js" defer></script>
</head>

<?php // la variable data-utilisateur permet de savoir si l'utilisateur est connecté côté JS 
?>

<body data-utilisateur="<?= isset($_SESSION['utilisateur']) ? '1' : '0' ?>">

    <?php // bloc popup JS affichée si l'utilisateur tente d'accéder à une fonctionnalité sans être connecté 
    ?>
    <div id="popup-non-connecte" class="popup-non-connecte cachee">
        Vous devez être connecté pour accéder à cette fonctionnalité.
    </div>

    <?php // en-tête du site : logo, navigation mobile et desktop 
    ?>
    <header class="site-header">
        <div class="header-container">

            <?php // logo cliquable qui mène à l’accueil 
            ?>
            <div class="logo">
                <a href="index.php?page=accueil"><strong>CAR LEGEND</strong></a>
            </div>

            <?php // bouton menu burger pour mobile 
            ?>
            <div class="burger-menu" id="burger-menu" aria-label="Menu mobile" role="button" tabindex="0">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <?php // menu mobile (s'affiche avec le JS sur petits écrans) 
            ?>
            <nav class="nav-mobile" id="nav-mobile">
                <a href="index.php?page=favoris">Favoris</a>
                <a href="index.php?page=messages">Messages</a>

                <?php // affichage selon connexion utilisateur 
                ?>
                <?php if ($utilisateur): ?>
                    <a href="index.php?page=profil"><?= htmlspecialchars($utilisateur['prenom']) ?></a>
                <?php else: ?>
                    <a href="index.php?page=connexion">Connexion</a>
                <?php endif; ?>
            </nav>

            <?php // menu principal visible sur desktop 
            ?>
            <nav class="nav-desktop">
                <div class="nav-center">
                    <a href="index.php?page=ajouterAnnonce" class="btn-deposer">Déposer une annonce</a>
                    <a href="index.php?page=annonces" class="btn-voir-annonce">Voir les annonces</a>
                </div>

                <div class="nav-right">
                    <a href="index.php?page=favoris" class="menu-lien">Favoris</a>
                    <a href="index.php?page=messages" class="menu-lien">Messages</a>


                    <?php // affichage avatar ou lien de connexion 
                    ?>
                    <?php if ($utilisateur): ?>
                        <a href="index.php?page=profil" class="avatar-header">
                            <img src="<?= getAvatarUrl($utilisateur['avatar'] ?? '') ?>" alt="Avatar">
                            <span><?= ($utilisateur['prenom']) ?></span>
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=connexion">Connexion</a>
                    <?php endif; ?>
                </div>
            </nav>

        </div>
    </header>

    <?php // conteneur invisible pour les popups dynamiques JS 
    ?>
    <div class="alerte-popup cachee" id="alerte-personnalisee">
        <p id="texte-alerte"></p>
    </div>

    <?php // bloc data utilisé par javascript pour afficher les messages flash 
    ?>
    <!-- <div id="alerte-dynamique" data-message=""></div> -->


    <?php // si un message est défini dans la session, on le passe à JS via un bloc data 
    ?>
    <?php if (!empty($_SESSION['message'])): ?>
        <?php
        $message = $_SESSION['message'];
        $type = 'info';

        if (is_array($message) && isset($message['type'], $message['texte'])) {
            $type = $message['type'];
            $message = $message['texte'];
        } elseif (is_string($message)) {
            $type = 'error';
        }

        unset($_SESSION['message']);
        ?>
        <div id="alerte-dynamique"
            data-message="<?= htmlspecialchars($message) ?>"
            data-type="<?= htmlspecialchars($type) ?>">
        </div>
    <?php endif; ?>