<?php
// inclusion de l'en-tête global (logo, navigation, scripts, etc.)
require_once(RACINE . "/vue/commun/header.php");
?>

<div class="mes-favoris-wrapper">
    <section>
        <?php // titre de la page 
        ?>
        <h2>Mes annonces en favoris</h2>

        <?php if (empty($mesFavoris)): ?>
            <?php // message affiché si l'utilisateur n'a aucun favori 
            ?>
            <p class="mes-favoris-vide" style="text-align: center; margin: 0 auto;">
                Vous n'avez encore ajouté aucune annonce en favoris.
            </p>
        <?php else: ?>
            <?php // affichage de la liste des annonces mises en favoris 
            ?>
            <div class="mes-favoris-liste">
                <?php foreach ($mesFavoris as $annonce): ?>
                    <div class="mes-favoris-carte" id="annonce-<?= $annonce['id_annonce'] ?>">

                        <?php // image de l'annonce si présente 
                        ?>
                        <div class="mes-favoris-image">
                            <?php if (!empty($annonce['image1'])): ?>
                                <img src="<?= htmlspecialchars($annonce['image1']) ?>" alt="Image de l'annonce">
                            <?php endif; ?>
                        </div>

                        <?php // informations et actions associées à l'annonce 
                        ?>
                        <div class="mes-favoris-infos">
                            <div>
                                <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
                                <div class="mes-favoris-prix">
                                    <?= number_format($annonce['prix'], 2, ',', ' ') ?> €
                                </div>
                                <div class="annonce-marque">
                                    <?= htmlspecialchars($annonce['kilometrage']) ?> km |
                                    <?= htmlspecialchars($annonce['carburant']) ?> |
                                    <?= htmlspecialchars($annonce['annee']) ?>
                                </div>
                            </div>

                            <div class="mes-favoris-actions">
                                <?php // bouton pour accéder à la page de détail 
                                ?>
                                <a href="index.php?page=detailAnnonce&id=<?= $annonce['id_annonce'] ?>" class="mes-favoris-btn-voir">
                                    Voir l'annonce
                                </a>

                                <?php // date de publication de l'annonce 
                                ?>
                                <div class="date-poste">
                                    Posté le <?= date("d/m/Y", strtotime($annonce['date_creation'])) ?>
                                </div>

                                <?php // lien pour retirer l'annonce des favoris 
                                ?>
                                <div class="favori-lien">
                                    <a href="index.php?page=favoris&action=supprimer&id=<?= $annonce['id_annonce'] ?>" class="mes-favoris-btn-retirer">
                                        Retirer des favoris
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php // pagination 
        ?>
        <div class="pagination">
            <?php if ($pageActuelle > 1): ?>
                <a href="index.php?page=favoris&p=<?= $pageActuelle - 1 ?>" class="pagination-prev">‹</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $pageActuelle): ?>
                    <span class="pagination-current"><?= $i ?></span>
                <?php else: ?>
                    <a href="index.php?page=favoris&p=<?= $i ?>" class="pagination-link"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pageActuelle < $totalPages): ?>
                <a href="index.php?page=favoris&p=<?= $pageActuelle + 1 ?>" class="pagination-next">›</a>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php // inclusion du pied de page dans un wrapper 
?>
<div class="page-wrapper">
    <?php require_once(RACINE . "/vue/commun/footer.php"); ?>
</div>