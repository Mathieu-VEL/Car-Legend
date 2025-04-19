<?php
// Inclusion de l'en-tête global
require_once(RACINE . "/vue/commun/header.php");
?>

<div class="ajouter-annonce-wrapper">
    <h2>Ajouter une annonce</h2>

    <?php // Formulaire d'ajout d'annonce avec envoi multipart pour upload d'images 
    ?>
    <form method="post" action="index.php?page=ajouterAnnonce" enctype="multipart/form-data" class="ajout-formulaire" id="form-annonce">
        <input type="hidden" name="action" value="ajouter">

        <?php // Champ titre 
        ?>
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" required maxlength="100" value="<?= htmlspecialchars($_SESSION['form_annonce']['titre'] ?? '') ?>">

        <?php // Champ description + compteur + barre de progression 
        ?>
        <label for="description">Description :</label>
        <textarea id="description" name="description" maxlength="3000" required><?= htmlspecialchars($_SESSION['form_annonce']['description'] ?? '') ?></textarea>
        <div class="compteur-description">
            <span id="compteur-caracteres">0</span> / 3000 caractères
            <div class="barre-compteur">
                <div class="barre-remplie" id="barre-remplie"></div>
            </div>
        </div>

        <?php // Première ligne de champs : prix, kilométrage, marque 
        ?>
        <div class="ajout-champs-ligne">
            <div class="ajout-champ">
                <label for="prix">Prix (€) :</label>
                <input type="number" id="prix" name="prix" step="0.01" min="0" required value="<?= htmlspecialchars($_SESSION['form_annonce']['prix'] ?? '') ?>">
            </div>

            <div class="ajout-champ">
                <label for="kilometrage">Kilométrage :</label>
                <input type="number" id="kilometrage" name="kilometrage" step="1" min="0" required value="<?= htmlspecialchars($_SESSION['form_annonce']['kilometrage'] ?? '') ?>">
            </div>

            <div class="ajout-champ">
                <label for="marque">Marque :</label>
                <input type="text" id="marque" name="marque" required value="<?= htmlspecialchars($_SESSION['form_annonce']['marque'] ?? '') ?>">
            </div>
        </div>

        <?php // Deuxième ligne de champs : modèle, année, carburant 
        ?>
        <div class="ajout-champs-ligne">
            <div class="ajout-champ">
                <label for="modele">Modèle :</label>
                <input type="text" id="modele" name="modele" value="<?= htmlspecialchars($_SESSION['form_annonce']['modele'] ?? '') ?>">
            </div>

            <div class="ajout-champ">
                <label for="annee">Année :</label>
                <input type="number" id="annee" name="annee" min="1885" max="<?= date("Y") ?>" value="<?= htmlspecialchars($_SESSION['form_annonce']['annee'] ?? '') ?>">
            </div>

            <div class="ajout-champ">
                <label for="carburant">Carburant :</label>
                <select id="carburant" name="carburant" required>
                    <option value="">Choisissez un type</option>
                    <?php
                    // Liste déroulante des types de carburant
                    $types = ['Essence', 'Diesel', 'Hybride', 'Électrique', 'GPL', 'Autre'];
                    foreach ($types as $type) {
                        $selected = ($_SESSION['form_annonce']['carburant'] ?? '') === $type ? 'selected' : '';
                        echo "<option value=\"$type\" $selected>$type</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <?php // Zone d'ajout des images (principale + secondaires) 
        ?>
        <div class="ajout-zone-images">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="ajout-zone-image">
                    <p><?= $i === 1 ? "Image principale obligatoire" : "Image secondaire $i" ?> :</p>
                    <img id="preview-image<?= $i ?>" src="asset/images/placeholder.jpg" alt="Aperçu" />
                    <label class="ajout-label-image">
                        Sélectionner une image
                        <input type="file" name="image<?= $i ?>" id="image<?= $i ?>" accept="image/*">
                    </label>
                    <button type="button" class="ajout-btn-supprimer" id="supprimer-image<?= $i ?>" style="display: none;">Supprimer</button>
                </div>
            <?php endfor; ?>
        </div>

        <?php // Boutons de navigation et soumission 
        ?>
        <div class="ajout-boutons-vertical">
            <a href="index.php?page=mesAnnonces" class="ajout-btn-retour">Retour aux annonces</a>
            <input type="submit" value="Publier l'annonce" class="ajout-btn-publier">
        </div>
    </form>
</div>

<?php
// Nettoyage de la session après affichage
unset($_SESSION['form_annonce']);
?>

<div class="page-wrapper">
    <?php
    // Inclusion du pied de page
    require_once(RACINE . "/vue/commun/footer.php");
    ?>
</div>