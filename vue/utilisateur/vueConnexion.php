<?php
// inclusion de l'en-tête global (logo, menu, scripts, etc.)
require_once(RACINE . "/vue/commun/header.php");
?>

<?php
// affichage conditionnel d'un message injecté via $_SESSION['message'] (alerte dynamique)
if (isset($_SESSION['message'])): ?>
    <div id="alerte-dynamique"
        data-message="<?= $_SESSION['message']['texte'] ?>"
        data-type="<?= $_SESSION['message']['type'] ?? 'info' ?>">
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<?php // section principale de connexion utilisateur 
?>
<div class="conn-section">
    <h3>Connexion à mon compte</h3>

    <?php // formulaire HTML pour se connecter 
    ?>
    <form method="post" class="conn-form" action="index.php?page=connexion">

        <?php // champ email requis pour l’identification 
        ?>
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required>

        <?php // champ mot de passe requis pour l’identification 
        ?>
        <label for="motdepasse">Mot de passe :</label>
        <input type="password" name="motdepasse" id="motdepasse" required>

        <?php // bouton pour envoyer les données du formulaire 
        ?>
        <input type="submit" value="Connexion">

        <?php // lien vers la page d'inscription si l'utilisateur n’a pas encore de compte 
        ?>
        <div class="conn-link-inscription">
            <p>Pas encore de compte ?</p>
            <a href="index.php?page=inscription" class="conn-btn-inscription">Créer un compte</a>
        </div>
    </form>
</div>

<?php
// inclusion du pied de page global
require_once(RACINE . "/vue/commun/footer.php");
?>