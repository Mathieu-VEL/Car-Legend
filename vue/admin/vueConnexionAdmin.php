<?php
// Inclusion de l'en-tête global (logo, navigation, scripts, etc.)
require_once(RACINE . "/vue/commun/header.php");
?>

<?php
// Affichage d'un message si l'accès admin a été refusé
if (isset($_GET['erreur']) && $_GET['erreur'] === 'acces'): ?>
    <div class="connexion-admin-error">
        Accès réservé à l’administrateur.
    </div>
<?php endif; ?>

<section class="connexion-admin-section">
    <h2>Connexion Admin</h2>

    <?php
    // Affichage d’un message d’erreur si l’authentification a échoué
    if (isset($_GET['erreur']) && $_GET['erreur'] == 1): ?>
        <div class="connexion-admin-error">
            Identifiants incorrects ou accès non autorisé.
        </div>
    <?php endif; ?>

    <?php // Formulaire de connexion réservé à l’administrateur 
    ?>
    <form class="connexion-admin-form" method="post" action="controleur/traitementConnexionAdmin.php">
        <input type="email" name="email" placeholder="Email admin" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>

    <?php // Lien de retour vers la page d’accueil du site 
    ?>
    <div class="connexion-admin-retour">
        <a href="index.php?page=accueil">← Retour à l'accueil</a>
    </div>
</section>