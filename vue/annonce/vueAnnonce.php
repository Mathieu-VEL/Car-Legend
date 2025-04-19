<?php
// Inclusion du header global du site
require_once(RACINE . "/vue/commun/header.php");

// Récupération de l'ID de l'utilisateur connecté (s'il y en a un)
$idUtilisateur = $_SESSION['utilisateur']['id_utilisateur'] ?? null;
?>

<div class="annonce-wrapper">
    <section>
        <h2>Nos Annonces</h2>

        <?php
        // Message si aucune annonce n’est trouvée
        if (empty($annonces)):
        ?>
            <p class="annonce-vide">Aucune annonce publiée pour le moment.</p>
        <?php else: ?>
            <div class="annonce-liste">
                <?php
                // Parcours de toutes les annonces
                foreach ($annonces as $annonce):
                ?>
                    <div class="annonce-card" id="annonce-<?= $annonce['id_annonce'] ?>">
                        <?php // Image de l’annonce (image1 obligatoire) 
                        ?>
                        <div class="annonce-image">
                            <?php if (!empty($annonce['image1'])): ?>
                                <img src="<?= htmlspecialchars($annonce['image1']) ?>" alt="Image de l'annonce">
                            <?php endif; ?>
                        </div>

                        <?php // Bloc informations (titre, prix, marque/modèle) 
                        ?>
                        <div class="annonce-infos">
                            <div>
                                <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
                                <div class="annonce-prix"><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</div>
                                <div class="annonce-marque"><?= strtoupper($annonce['marque']) ?> <?= $annonce['modele'] ?></div>
                            </div>

                            <div class="annonce-actions">
                                <?php // Bouton de redirection vers la page détail 
                                ?>
                                <a href="index.php?page=detailAnnonce&id=<?= $annonce['id_annonce'] ?>" class="annonce-btn-voir">
                                    Voir détail
                                </a>

                                <?php // Date de publication 
                                ?>
                                <div class="date-poste">
                                    Posté le <?= date("d/m/Y", strtotime($annonce['date_creation'])) ?>
                                </div>

                                <?php
                                // Si un utilisateur est connecté, on affiche le lien favori
                                if ($idUtilisateur):
                                ?>
                                    <div class="favori-lien">
                                        <a href="#" class="favori-toggle"
                                            data-id="<?= $annonce['id_annonce'] ?>"
                                            data-action="<?= estFavori($idUtilisateur, $annonce['id_annonce']) ? 'supprimer' : 'ajouter' ?>">
                                            <?= estFavori($idUtilisateur, $annonce['id_annonce']) ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php // Pagination (si plusieurs pages) 
        ?>
        <div class="pagination">
            <?php if ($pageActuelle > 1): ?>
                <a href="index.php?page=annonces&p=<?= $pageActuelle - 1 ?>" class="pagination-prev">‹</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $pageActuelle): ?>
                    <span class="pagination-current"><?= $i ?></span>
                <?php else: ?>
                    <a href="index.php?page=annonces&p=<?= $i ?>" class="pagination-link"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pageActuelle < $totalPages): ?>
                <a href="index.php?page=annonces&p=<?= $pageActuelle + 1 ?>" class="pagination-next">›</a>
            <?php endif; ?>
        </div>
    </section>
</div>

<div class="page-wrapper">
    <?php
    // Inclusion du footer global
    require_once(RACINE . "/vue/commun/footer.php");
    ?>
</div>