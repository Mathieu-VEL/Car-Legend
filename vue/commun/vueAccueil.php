<?php
// inclusion de l'en-tête (logo, navigation, scripts, etc.)
require_once(RACINE . "/vue/commun/header.php");

// récupération de l'utilisateur connecté (si présent)
$utilisateur = $_SESSION['utilisateur'] ?? null;
$prenom = $utilisateur['prenom'] ?? null;
?>

<main>
    <section class="accueil-section">
        <div class="accueil-bloc">

            <?php if ($prenom): ?>
                <?php // si l'utilisateur est connecté, message personnalisé avec son prénom 
                ?>
                <h2 class="accueil-titre">
                    <span class="texte-intro">Bienvenue de retour,</span>
                    <span class="prenom-block"><?= strtoupper($prenom) ?></span>
                </h2>
            <?php else: ?>
                <?php // si l'utilisateur n'est pas connecté, message d'accueil général 
                ?>
                <h2 class="accueil-titre">
                    <span class="site-name">Bienvenue sur CAR LEGEND</span>
                </h2>
            <?php endif; ?>

            <?php // phrase de présentation du site 
            ?>
            <p class="accueil-texte">
                Découvrez nos voitures anciennes de collection, proposées par des passionnés.
            </p>

            <?php // bouton d'accès aux annonces 
            ?>
            <div class="accueil-btn-container">
                <a href="index.php?page=annonces" class="accueil-btn-annonces">
                    Voir les annonces
                </a>
            </div>

        </div>
    </section>
</main>

<?php
// inclusion du pied de page
require_once(RACINE . "/vue/commun/footer.php");
?>