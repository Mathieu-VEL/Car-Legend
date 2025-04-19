<?php
// inclusion de l'en-tête (menu, logo, scripts JS, etc.)
require_once(RACINE . "/vue/commun/header.php");
?>


<section class="mes-annonces-wrapper">
    <?php // titre de la page avec le nombre total d'annonces 
    ?>
    <h2><?= $titrePage ?> (<?= $nbMesAnnonces ?>)</h2>

    <?php if (empty($mesAnnonces)): ?>
        <?php // affichage si l'utilisateur n'a encore publié aucune annonce 
        ?>
        <div class="mes-annonces-vide">
            <p>Vous n'avez encore publié aucune annonce.</p>
            <p><a href="index.php?page=ajouterAnnonce" class="mes-annonces-btn-ajouter">Publier ma première annonce</a></p>
        </div>
    <?php else: ?>

        <?php // boucle sur les annonces de l'utilisateur 
        ?>
        <?php foreach ($mesAnnonces as $annonce): ?>
            <div class="mes-annonces-carte">
                <?php // image principale de l'annonce 
                ?>
                <img src="<?= htmlspecialchars($annonce['image1']) ?>" alt="Image de l'annonce">

                <?php // informations principales de l'annonce 
                ?>
                <div class="mes-annonces-infos">
                    <h3><?= htmlspecialchars($annonce['titre']) ?></h3>

                    <ul class="mes-annonces-details">
                        <li><strong>Prix :</strong> <?= number_format($annonce['prix'], 2, ',', ' ') ?> €</li>
                        <li><strong>Année :</strong> <?= $annonce['annee'] ?></li>
                        <li><strong>Kilométrage :</strong> <?= number_format($annonce['kilometrage'], 0, ',', ' ') ?> km</li>
                        <li><strong>Carburant :</strong> <?= $annonce['carburant'] ?></li>
                    </ul>

                    <?php // boutons pour modifier ou supprimer l'annonce 
                    ?>
                    <div class="mes-annonces-actions">
                        <a href="index.php?page=mesAnnonces&action=modifier&id=<?= $annonce['id_annonce'] ?>" class="mes-annonces-btn-modifier">Modifier</a>
                        <button class="mes-annonces-btn-supprimer" data-id="<?= $annonce['id_annonce'] ?>">🗑 Supprimer</button>
                        <?php // tu peux retirer l’icône si tu le souhaites 
                        ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

    <?php // modale de confirmation de suppression, affichée via JS 
    ?>
    <div id="alert-confirmation-suppression" class="alert-confirmation-box cachee">
        <div class="alert-box-content">
            <p id="alert-message">Confirmez-vous la suppression de cette annonce ?</p>
            <div class="alert-box-buttons">
                <button id="alert-annuler" class="alert-btn-annuler">Annuler</button>
                <a id="alert-confirmer" class="alert-btn-supprimer" href="#">Supprimer</a>
            </div>
        </div>
    </div>

    <?php // bloc de pagination si plusieurs pages 
    ?>
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($pageActuelle > 1): ?>
                <a href="index.php?page=mesAnnonces&p=<?= $pageActuelle - 1 ?>" class="pagination-prev">‹</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $pageActuelle): ?>
                    <span class="pagination-current"><?= $i ?></span>
                <?php else: ?>
                    <a href="index.php?page=mesAnnonces&p=<?= $i ?>" class="pagination-link"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pageActuelle < $totalPages): ?>
                <a href="index.php?page=mesAnnonces&p=<?= $pageActuelle + 1 ?>" class="pagination-next">›</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<?php // inclusion du pied de page 
?>
<div class="page-wrapper">
    <?php require_once(RACINE . "/vue/commun/footer.php"); ?>
</div>