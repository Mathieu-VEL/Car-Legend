<?php
// inclusion de l'en-tête global (logo, navigation, scripts, etc.)
require_once(RACINE . "/vue/commun/header.php");
?>

<?php // alerte personnalisée affichée dynamiquement en JavaScript 
?>
<div id="alerte-personnalisee" class="alerte-popup cachee">
    <p id="texte-alerte"></p>
</div>

<?php // conteneur pour message injecté dynamiquement (via PHP ou JS) 
?>
<div id="alerte-dynamique" data-message=""></div>

<?php // conteneur principal de la page profil 
?>
<div class="profil-container">
    <div class="profil-header">
        <?php if (!empty($_SESSION['utilisateur']['avatar'])): ?>
            <?php // affichage de l'avatar si présent 
            ?>
            <img src="<?= htmlspecialchars($_SESSION['utilisateur']['avatar']) ?>" alt="Avatar" class="profil-avatar-img">
        <?php else: ?>
            <?php // sinon, affichage de la première lettre du prénom 
            ?>
            <div class="profil-avatar-placeholder">
                <?= htmlspecialchars(strtoupper(substr($_SESSION['utilisateur']['prenom'], 0, 1))) ?>
            </div>
        <?php endif; ?>

        <?php // informations de l'utilisateur 
        ?>
        <div class="profil-info">
            <h2>
                <?= htmlspecialchars($_SESSION['utilisateur']['prenom']) ?>
                <?= htmlspecialchars($_SESSION['utilisateur']['nom']) ?>
            </h2>
            <p><?= htmlspecialchars($_SESSION['utilisateur']['email']) ?></p>
            <span class="profil-role-badge"><?= htmlspecialchars($_SESSION['utilisateur']['role']) ?></span>
        </div>

        <?php // bouton de déconnexion 
        ?>
        <a href="index.php?page=deconnexion" class="profil-btn-deconnexion">Se déconnecter</a>
    </div>

    <?php // grille de navigation vers les principales fonctionnalités 
    ?>
    <div class="profil-grid">
        <a href="index.php?page=mesAnnonces" class="profil-card">
            <h3>Mes Annonces</h3>
            <p>Gérer mes annonces déposées</p>
        </a>
        <a href="index.php?page=ajouterAnnonce" class="profil-card">
            <h3>Ajouter une annonce</h3>
            <p>Publier une nouvelle voiture de collection</p>
        </a>
        <a href="index.php?page=favoris" class="profil-card">
            <h3>Mes Favoris</h3>
            <p>Voir les annonces que j'ai aimées</p>
        </a>
        <a href="index.php?page=parametres" class="profil-card">
            <h3>Paramètres profil</h3>
            <p>Modifier mes informations</p>
        </a>
    </div>
</div>

<?php // affichage du panneau admin si l'utilisateur connecté est administrateur 
?>
<?php if (isset($_SESSION['utilisateur']) && $_SESSION['utilisateur']['role'] === 'admin'): ?>
    <div class="profil-admin-card">
        <h3>Tableau de bord Admin</h3>
        <p>Gérer les utilisateurs et les annonces du site</p>
        <a href="index.php?page=admin" class="btn-admin-dashboard">Accéder au tableau de bord</a>
    </div>
<?php endif; ?>

<?php // inclusion du pied de page 
?>
<div class="page-wrapper">
    <?php require_once(RACINE . "/vue/commun/footer.php"); ?>
</div>