<?php
// Inclusion de l'en-tête global du site (logo, navigation, scripts, etc.)
require_once(RACINE . "/vue/commun/header.php");
?>

<section class="admin-section">
    <?php
    // Message de confirmation ou d'erreur stocké en session
    if (!empty($_SESSION['message'])): ?>
        <div class="alert-message">
            <?= $_SESSION['message']; ?>
        </div>
    <?php
        // Suppression du message pour éviter qu'il réapparaisse
        unset($_SESSION['message']);
    endif;
    ?>

    <h2>Liste des annonces</h2>

    <?php
    // Parcours de chaque annonce
    foreach ($annonces as $annonce): ?>
        <div class="admin-annonce">
            <h3><?= htmlspecialchars($annonce['titre']) ?></h3>

            <?php // Affichage de la description avec les sauts de ligne conservés 
            ?>
            <p><?= nl2br(htmlspecialchars($annonce['description'])) ?></p>

            <?php // Informations de l'auteur de l'annonce 
            ?>
            <p class="admin-meta">
                Par : <?= htmlspecialchars($annonce["prenom"] . " " . $annonce["nom"]) ?> |
                ID Annonce : <?= $annonce['id_annonce'] ?>
            </p>

            <?php // Lien de suppression de l’annonce 
            ?>
            <a href="index.php?page=admin&action=supprimerAnnonce&id=<?= $annonce['id_annonce'] ?>" class="btn-danger">
                Supprimer
            </a>
        </div>
    <?php endforeach; ?>

    <hr>

    <h2>Liste des utilisateurs</h2>

    <?php
    // Parcours de chaque utilisateur
    foreach ($utilisateurs as $user): ?>
        <div class="admin-utilisateur">
            <p>
                <?= htmlspecialchars($user["prenom"] . " " . $user["nom"]) ?>
                (<?= htmlspecialchars($user["email"]) ?>) – Rôle : <?= $user["role"] ?>
            </p>

            <?php
            // Affiche le bouton "Supprimer" sauf si c'est le compte admin connecté lui-même
            if ((int)$user["id_utilisateur"] !== (int)$_SESSION['utilisateur']['id_utilisateur']): ?>
                <a href="index.php?page=admin&action=supprimerUtilisateur&id=<?= $user['id_utilisateur'] ?>" class="btn-danger">
                    Supprimer
                </a>
            <?php else: ?>
                <?php // Indication que c'est le compte admin actif 
                ?>
                <span class="admin-indice">(Compte admin actif)</span>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</section>