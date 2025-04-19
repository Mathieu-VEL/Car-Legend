<?php
// inclusion de l'en-tête commun du site (logo, navigation, etc.)
require_once(RACINE . "/vue/commun/header.php");
?>

<main>
    <section class="erreur-404">
        <div class="contenu-erreur">
            <?php // affichage du code d'erreur (ex : 404, 403...) 
            ?>
            <h1>Erreur <?= ($codeErreur) ?></h1>

            <?php // message d'erreur personnalisé (ex : page introuvable) 
            ?>
            <p><?= ($messageErreur) ?></p>

            <?php // bouton permettant de retourner à la page d'accueil 
            ?>
            <a href="index.php?page=accueil" class="btn-retour">Retour à l'accueil</a>
        </div>
    </section>
</main>

<?php
// inclusion du pied de page commun
require_once(RACINE . "/vue/commun/footer.php");
?>