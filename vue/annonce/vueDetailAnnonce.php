<?php
// inclusion de l'en-tête global du site
require_once(RACINE . "/vue/commun/header.php");
?>

<div class="detail-wrapper">

    <?php // galerie d'images de l'annonce 
    ?>
    <div class="detail-galerie">
        <?php // image principale toujours affichée 
        ?>
        <img src="<?= htmlspecialchars(getImageUrl($annonce['image1'])) ?>" alt="Image principale" class="detail-image-principale">

        <?php // miniatures affichées si présentes 
        ?>
        <div class="detail-miniatures">
            <?php if (!empty($annonce['image2'])): ?>
                <img src="<?= htmlspecialchars(getImageUrl($annonce['image2'])) ?>" alt="Miniature 2" class="detail-miniature-img">
            <?php endif; ?>
            <?php if (!empty($annonce['image3'])): ?>
                <img src="<?= htmlspecialchars(getImageUrl($annonce['image3'])) ?>" alt="Miniature 3" class="detail-miniature-img">
            <?php endif; ?>
        </div>
    </div>

    <?php // bloc des informations principales 
    ?>
    <div class="detail-infos">
        <p class="detail-rapide">
            <?= htmlspecialchars($annonce['annee']) ?> •
            <?= number_format($annonce['kilometrage'], 0, ',', ' ') ?> km •
            <?= htmlspecialchars($annonce['carburant']) ?>
        </p>
        <div class="detail-prix">
            <?= number_format($annonce['prix'], 2, ',', ' ') ?> €
        </div>
        <h2><?= htmlspecialchars($annonce['titre']) ?></h2>
    </div>

    <?php // bloc des critères techniques 
    ?>
    <div class="detail-criteres">
        <h3>Critères</h3>
        <div class="detail-grid">
            <div><strong>Marque</strong><br><?= htmlspecialchars($annonce['marque']) ?></div>
            <div><strong>Modèle</strong><br><?= htmlspecialchars($annonce['modele']) ?></div>
            <div><strong>Année modèle</strong><br><?= htmlspecialchars($annonce['annee']) ?></div>
            <div><strong>Kilométrage</strong><br><?= number_format($annonce['kilometrage'], 0, ',', ' ') ?> km</div>
            <div><strong>Carburant</strong><br><?= htmlspecialchars($annonce['carburant']) ?></div>
            <div><strong>Prix</strong><br><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</div>
        </div>
    </div>

    <?php // séparateur visuel 
    ?>
    <hr class="detail-separateur" />

    <?php // bloc de description complète 
    ?>
    <div class="detail-description">
        <h3>Description</h3>
        <p><?= nl2br(htmlspecialchars($annonce['description'])) ?></p>
    </div>

    <?php // bloc fiche API CarQuery 
    ?>
    <div class="fiche-api-bloc">
        <h3>Fiche technique véhicule</h3>

        <div class="selecteurs-api">
            <select id="select-annee" name="cqy">
                <option value="">Année</option>
            </select>
            <select id="select-marque" name="cqm">
                <option value="">Marque</option>
            </select>
            <select id="select-modele" name="cqt">
                <option value="">Modèle</option>
            </select>

            <button id="btn-recherche" disabled>Recherche</button>
        </div>

        <div id="comparaison-api" class="resultats-api">
            <p>Sélectionnez un véhicule pour voir les détails.</p>
        </div>
    </div>

    <?php // bloc des actions : contacter ou revenir 
    ?>
    <div class="detail-bloc-actions">
        <?php if (isset($_SESSION['utilisateur']) && $_SESSION['utilisateur']['id_utilisateur'] !== $annonce['id_utilisateur']): ?>
            <a href="index.php?page=messages&action=contacter&id_annonce=<?= htmlspecialchars($annonce['id_annonce']) ?>&dest=<?= htmlspecialchars($annonce['id_utilisateur']) ?>" class="detail-btn-contacter">
                Contacter le vendeur
            </a>
        <?php endif; ?>

        <a href="index.php?page=annonces" class="detail-btn-retour">
            Retour aux annonces
        </a>
    </div>
</div>

<?php // modale de zoom image 
?>
<div id="modale-zoom" class="detail-modal-zoom cachee">
    <span class="detail-fermer-modale">&times;</span>
    <img id="image-agrandie" class="detail-image-zoom" src="" alt="Image agrandie">
</div>

<?php
// inclusion du pied de page global
require_once(RACINE . "/vue/commun/footer.php");
?>