<?php
// inclusion de l'en-tête global (logo, menu, scripts, etc.)
require_once(RACINE . "/vue/commun/header.php");
?>

<?php // popup d’alerte personnalisée affichée dynamiquement via JavaScript 
?>
<div id="alerte-personnalisee" class="alerte-popup cachee">
    <p id="texte-alerte"></p>
</div>

<?php
// affichage d’un message PHP injecté via $_SESSION['message']
if (isset($_SESSION['message'])): ?>
    <div id="alerte-dynamique"
        data-message="<?= $_SESSION['message']['texte'] ?>"
        data-type="<?= $_SESSION['message']['type'] ?? 'info' ?>">
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<?php // conteneur principal du formulaire d'inscription 
?>
<div class="insc-section">
    <h3>Créer un compte</h3>

    <?php // formulaire d'inscription 
    ?>
    <form method="post" class="insc-form" id="form-inscription">

        <?php // champ nom obligatoire 
        ?>
        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" required>

        <?php // champ prénom obligatoire 
        ?>
        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" id="prenom" required>

        <?php // champ email obligatoire 
        ?>
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required>

        <?php // champ mot de passe avec règles de sécurité 
        ?>
        <label for="motdepasse">Mot de passe :</label>
        <input type="password" name="motdepasse" id="motdepasse" required>

        <?php // checklist des critères de sécurité du mot de passe (mise à jour en JS) 
        ?>
        <ul id="password-criteria" class="password-checklist">
            <li id="length" class="invalid">Au moins 8 caractères</li>
            <li id="uppercase" class="invalid">Une majuscule</li>
            <li id="number" class="invalid">Un chiffre</li>
            <li id="special" class="invalid">Un caractère spécial</li>
        </ul>

        <?php // confirmation du mot de passe 
        ?>
        <label for="confirm_motdepasse">Confirmer le mot de passe :</label>
        <input type="password" name="confirm_motdepasse" id="confirm_motdepasse" required>

        <?php // bouton de soumission 
        ?>
        <input type="submit" value="Créer mon compte">
    </form>

    <?php // lien vers la page de connexion 
    ?>
    <div class="insc-link-login">
        <p>Déjà inscrit ?</p>
        <a href="index.php?page=connexion">Connexion ici</a>
    </div>
</div>

<?php
// inclusion du pied de page global
require_once(RACINE . "/vue/commun/footer.php");
?>