<?php
// inclusion de l'en-tête global (navigation, styles, scripts, etc.)
require_once(RACINE . "/vue/commun/header.php");
?>

<?php // popup d'alerte personnalisée (affichée via JS en haut de page) 
?>
<div id="alerte-personnalisee" class="alerte-popup cachee">
    <p id="texte-alerte"></p>
</div>

<?php // conteneur pour injecter dynamiquement un message JS 
?>
<div id="alerte-dynamique" data-message=""></div>

<?php // conteneur principal de la page de modification 
?>
<div class="modif-modifier-annonce-container">
    <h2>Modifier l'annonce</h2>

    <?php // formulaire de modification d'une annonce existante 
    ?>
    <form id="form-modifier" method="post" enctype="multipart/form-data" action="index.php?page=mesAnnonces&action=modifier" class="modif-form-modifier-annonce">

        <?php // champs cachés : action + identifiants des images actuelles 
        ?>
        <input type="hidden" name="action" value="modifier">
        <input type="hidden" name="id_annonce" value="<?= $annonceAModifier['id_annonce'] ?>">
        <input type="hidden" name="ancienne_image1" value="<?= $annonceAModifier['image1'] ?>">
        <input type="hidden" name="ancienne_image2" value="<?= $annonceAModifier['image2'] ?>">
        <input type="hidden" name="ancienne_image3" value="<?= $annonceAModifier['image3'] ?>">

        <?php // titre de l'annonce 
        ?>
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($annonceAModifier['titre']) ?>" required>

        <?php // description avec compteur dynamique 
        ?>
        <label for="description">Description :</label>
        <textarea id="description" name="description" maxlength="3000" required><?= htmlspecialchars($annonceAModifier['description']) ?></textarea>
        <div class="compteur-description">
            <span id="compteur-caracteres">0</span> / 3000 caractères
            <div class="barre-compteur">
                <div class="barre-remplie" id="barre-remplie"></div>
            </div>
        </div>

        <?php // champs numériques 
        ?>
        <label for="prix">Prix (€) :</label>
        <input type="number" id="prix" name="prix" step="0.01" min="0" value="<?= $annonceAModifier['prix'] ?>" required>

        <label for="kilometrage">Kilométrage :</label>
        <input type="number" id="kilometrage" name="kilometrage" step="1" min="0" value="<?= $annonceAModifier['kilometrage'] ?>" required>

        <?php // caractéristiques du véhicule 
        ?>
        <label for="marque">Marque :</label>
        <input type="text" id="marque" name="marque" value="<?= htmlspecialchars($annonceAModifier['marque']) ?>" required>

        <label for="modele">Modèle :</label>
        <input type="text" id="modele" name="modele" value="<?= htmlspecialchars($annonceAModifier['modele']) ?>">

        <label for="annee">Année :</label>
        <input type="number" id="annee" name="annee" min="1885" max="<?= date('Y') ?>" value="<?= $annonceAModifier['annee'] ?>">

        <label for="carburant">Carburant :</label>
        <input type="text" id="carburant" name="carburant" value="<?= htmlspecialchars($annonceAModifier['carburant']) ?>">

        <?php // image principale 
        ?>
        <div class="modif-input-image-wrapper">
            <label>Image principale :</label>
            <img id="preview-image1" class="modif-image-preview" src="<?= getImageUrl($annonceAModifier['image1']) ?>" alt="Image 1">
            <div class="modif-image-controls" id="block-image1">
                <label class="modif-custom-file-upload">
                    Sélectionner une image
                    <input type="file" name="image1" id="image1" accept="image/*" onchange="updatePreview(this)">
                </label>
            </div>
            <input type="hidden" name="delete-image1" id="delete-image1" value="0">
        </div>

        <?php // image secondaire 
        ?>
        <div class="modif-input-image-wrapper">
            <label>Image secondaire :</label>
            <img id="preview-image2" class="modif-image-preview" src="<?= getImageUrl($annonceAModifier['image2']) ?>" alt="Image 2">
            <div class="modif-image-controls" id="block-image2">
                <label class="modif-custom-file-upload">
                    Sélectionner une image
                    <input type="file" name="image2" id="image2" accept="image/*" onchange="updatePreview(this)">
                </label>
            </div>
            <input type="hidden" name="delete-image2" id="delete-image2" value="0">
        </div>

        <?php // image supplémentaire 
        ?>
        <div class="modif-input-image-wrapper">
            <label>Image supplémentaire :</label>
            <img id="preview-image3" class="modif-image-preview" src="<?= getImageUrl($annonceAModifier['image3']) ?>" alt="Image 3">
            <div class="modif-image-controls" id="block-image3">
                <label class="modif-custom-file-upload">
                    Sélectionner une image
                    <input type="file" name="image3" id="image3" accept="image/*" onchange="updatePreview(this)">
                </label>
            </div>
            <input type="hidden" name="delete-image3" id="delete-image3" value="0">
        </div>

        <?php // boutons en bas du formulaire 
        ?>
        <div class="modif-zone-boutons-vertical">
            <a href="index.php?page=mesAnnonces" class="modif-btn-retour-annonces">
                <span class="icone-fleche"></span> Retour aux annonces
            </a>
            <input type="submit" value="Enregistrer les modifications" class="modif-btn-publier">
        </div>
    </form>
</div>

<?php
// inclusion du pied de page global
require_once(RACINE . "/vue/commun/footer.php");
?>