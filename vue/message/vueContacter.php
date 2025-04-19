<?php
// inclusion de l'en-tête global (navigation, logo, scripts, etc.)
require_once(RACINE . "/vue/commun/header.php");
?>

<section class="contacter-wrapper">
    <?php // titre de la page 
    ?>
    <h2 class="contacter-titre">Contacter le vendeur</h2>

    <?php // informations de l'annonce et du vendeur 
    ?>
    <div class="contacter-info">
        <p><strong>Annonce :</strong> <?= htmlspecialchars($annonce['titre']) ?></p>
        <p><strong>Vendeur :</strong> <?= htmlspecialchars($vendeur['prenom']) ?> <?= htmlspecialchars($vendeur['nom']) ?></p>
    </div>

    <?php // formulaire d'envoi du message 
    ?>
    <form action="index.php?page=messages&action=envoyer" method="post" class="contacter-form">
        <?php // champs cachés pour transmettre les identifiants nécessaires 
        ?>
        <input type="hidden" name="id_annonce" value="<?= htmlspecialchars($annonce['id_annonce']) ?>">
        <input type="hidden" name="id_recoit" value="<?= htmlspecialchars($vendeur['id_utilisateur']) ?>">

        <?php // zone de saisie du contenu du message 
        ?>
        <label for="contenu" class="contacter-label">Votre message :</label>
        <textarea name="contenu" id="contenu" rows="6" required class="contacter-textarea"></textarea>

        <?php // bouton pour envoyer le message 
        ?>
        <button type="submit" class="btn-contacter-envoyer">Envoyer</button>
    </form>

    <?php // lien de retour vers la page des annonces 
    ?>
    <div class="contacter-retour">
        <a href="index.php?page=annonces" class="btn-contacter-retour">Retour à l’annonce</a>
    </div>
</section>

<?php
// inclusion du pied de page global
require_once(RACINE . "/vue/commun/footer.php");
?>