<?php
// inclusion de l'en-tête global du site (logo, menu, scripts, etc.)
require_once(RACINE . "/vue/commun/header.php");
?>

<?php // conteneur principal de la page des paramètres 
?>
<div class="param-wrapper">
    <h2>Paramètres du profil</h2>

    <?php // formulaire pour modifier les informations du profil 
    ?>
    <form method="post" action="index.php?page=parametres" enctype="multipart/form-data" class="param-form">
        <input type="hidden" name="action" value="modifier">

        <?php // bloc avatar : affichage de l'image ou d'une initiale 
        ?>
        <div class="param-avatar">
            <?php if (!empty($_SESSION['utilisateur']['avatar'])): ?>
                <?php // si une image d'avatar est présente, on l'affiche 
                ?>
                <img src="<?= htmlspecialchars($_SESSION['utilisateur']['avatar']) ?>" alt="Avatar" class="param-avatar-preview">
            <?php else: ?>
                <?php // sinon on affiche l'initiale du prénom dans un cercle 
                ?>
                <div class="param-avatar-circle">
                    <?= htmlspecialchars(strtoupper(substr($_SESSION['utilisateur']['prenom'], 0, 1))) ?>
                </div>
            <?php endif; ?>

            <?php // bouton pour charger un nouveau fichier avatar 
            ?>
            <label for="avatar" class="param-avatar-upload">+</label>
            <input type="file" name="avatar" id="avatar" accept="image/*" hidden>

            <p class="param-avatar-info">
                Avec une photo, vous avez de quoi personnaliser votre profil et rassurer les autres membres !
            </p>
        </div>

        <?php // champs de modification des informations personnelles 
        ?>
        <div class="param-inputs">
            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($_SESSION['utilisateur']['prenom']) ?>" required>

            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($_SESSION['utilisateur']['nom']) ?>" required>

            <?php // bouton de validation du formulaire 
            ?>
            <input type="submit" value="Enregistrer les modifications" class="param-submit">
        </div>
    </form>

    <?php // lien de retour vers la page de profil 
    ?>
    <a href="index.php?page=profil" class="param-btn-retour">⬅ Retour au profil</a>

    <?php // bouton pour ouvrir la popup de suppression de compte 
    ?>
    <button type="button" class="param-delete-btn" id="ouvrir-confirmation-suppression">
        Supprimer mon compte
    </button>

    <?php // popup de confirmation avant suppression du compte 
    ?>
    <div id="popup-confirmation-suppression" class="popup-confirmation cachee">
        <div class="popup-contenu">
            <p>
                Es-tu sûr de vouloir supprimer ton compte ?<br>
                <strong>Cette action est irréversible.</strong>
            </p>
            <form method="post" action="index.php?page=parametres">
                <input type="hidden" name="action" value="supprimer">
                <button type="submit" class="btn-confirmation">Oui, supprimer</button>
                <button type="button" class="btn-annulation" id="annuler-suppression">Annuler</button>
            </form>
        </div>
    </div>
</div>

<?php
// inclusion du pied de page global
require_once(RACINE . "/vue/commun/footer.php");
?>