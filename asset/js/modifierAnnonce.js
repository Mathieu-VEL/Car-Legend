// Déclare l’URL du placeholder (utilisée si l’image est supprimée)
if (typeof PLACEHOLDER_URL === "undefined") {
  // Vérifie si la variable PLACEHOLDER_URL n’a pas encore été définie dans un autre script
  // Cela évite de redéfinir cette variable s’il y a plusieurs fichiers JS chargés
  var PLACEHOLDER_URL = "/projet-final/asset/images/placeholder.jpg";
  // Déclare une variable globale contenant le chemin de l’image par défaut
  // Cette image est utilisée pour remplacer une image supprimée dans l’aperçu
}

// Fonction déclenchée lors du changement d’image : affiche l’aperçu et le bouton supprimer
function updatePreview(input) {
  // On récupère l'identifiant du champ <input type="file"> (ex: "image1")
  const imageKey = input.id;

  // On récupère l’élément <img> lié à ce champ pour afficher l’aperçu
  const preview = document.getElementById("preview-" + imageKey);

  // On récupère le premier fichier sélectionné dans le champ (s’il y en a)
  const file = input.files[0];

  // Si un fichier a bien été choisi
  if (file) {
    // On crée un objet FileReader pour lire le fichier localement en JS
    const reader = new FileReader();

    // Quand la lecture est terminée (événement "load"), on exécute la fonction suivante :
    reader.onload = function (e) {
      // On affecte à la balise <img> le contenu du fichier sous forme de base64
      preview.src = e.target.result;

      // On rend visible l’image dans le cas où elle serait masquée
      preview.style.display = "block";

      // On récupère le bloc HTML contenant l’image et le bouton (ex: <div id="block-image1">)
      const block = document.getElementById("block-" + imageKey);

      // Si aucun bouton de suppression n'existe encore dans ce bloc
      if (!block.querySelector(".btn-supprimer")) {
        // On crée un bouton de type <button>
        const button = document.createElement("button");

        // On définit que ce bouton ne soumet pas de formulaire
        button.type = "button";

        // On lui donne une classe CSS pour le styliser
        button.className = "btn-supprimer";

        // On définit son texte visible
        button.textContent = " Supprimer l’image";

        // On stocke la clé de l’image dans un attribut data-* personnalisé
        button.dataset.imageKey = imageKey;

        // On insère le bouton dans le bloc
        block.appendChild(button);

        // On ajoute un événement clic qui appelle la fonction de suppression avec la bonne image
        button.addEventListener("click", () => {
          supprimerImage(imageKey);
        });
      }
    };

    // On lance la lecture du fichier image pour obtenir son contenu base64 (utile pour affichage immédiat)
    reader.readAsDataURL(file);
  }
}

// Fonction pour supprimer une image déjà affichée
function supprimerImage(imageKey) {
  // On récupère la balise <img> correspondant à l’aperçu de l’image (ex: id="preview-image1")
  const preview = document.getElementById("preview-" + imageKey);

  // On récupère le champ caché <input type="hidden"> qui sert à indiquer au backend que cette image est supprimée
  const deleteField = document.getElementById("delete-" + imageKey);

  // On récupère le champ <input type="file"> qui permet à l’utilisateur de choisir une nouvelle image
  const fileInput = document.getElementById(imageKey);

  // On récupère le bloc contenant l’image et le bouton de suppression (ex: div id="block-image1")
  const block = document.getElementById("block-" + imageKey);

  // Si un champ input de fichier existe, on le vide → l’image n’est plus sélectionnée côté frontend
  if (fileInput) fileInput.value = "";

  // Si le champ hidden existe, on indique que l’image doit être supprimée (valeur "1" envoyée au backend)
  if (deleteField) deleteField.value = "1";

  // Si l’aperçu existe, on le remplace par une image de substitution et on s’assure qu’il soit visible
  if (preview) {
    preview.src = PLACEHOLDER_URL; // Variable globale définie au début du script
    preview.style.display = "block";
  }

  // On cherche un bouton de suppression existant dans le bloc
  const btn = block.querySelector(".btn-supprimer");

  // Si un tel bouton est trouvé, on le supprime du DOM
  if (btn) btn.remove();

  // Si la fonction afficherPopup() est définie, on affiche une confirmation visuelle
  if (typeof afficherPopup === "function") {
    afficherPopup("Image supprimée (placeholder affiché).");
  }
}

// Génère automatiquement les boutons supprimer pour les images déjà existantes (en édition)
// Quand le DOM est entièrement chargé (structure HTML prête), on exécute ce code
document.addEventListener("DOMContentLoaded", () => {
  // On parcourt un tableau contenant les clés des trois images (principale et secondaires)
  ["image1", "image2", "image3"].forEach((imageKey) => {
    // On récupère l'élément <img> correspondant à la prévisualisation (ex: id="preview-image1")
    const preview = document.getElementById("preview-" + imageKey);

    // On récupère le conteneur associé à cette image (ex: id="block-image1")
    const block = document.getElementById("block-" + imageKey);

    // Si les deux éléments existent dans le DOM, que l'image est bien définie,
    // et qu'elle n'est pas une image par défaut de type "placeholder"
    if (
      preview && // Vérifie que l'image est présente
      block && // Vérifie que le bloc contenant l'image est présent
      preview.src && // Vérifie que l'attribut `src` contient une URL
      !preview.src.includes("placeholder.jpg") // Vérifie que ce n'est pas une image vide ou de remplacement
    ) {
      // Si le bouton de suppression n’a pas encore été ajouté à ce bloc
      if (!block.querySelector(".btn-supprimer")) {
        // On crée dynamiquement un bouton de type <button>
        const button = document.createElement("button");

        // On précise que c’est un bouton normal (non submit)
        button.type = "button";

        // On lui donne une classe CSS (pour le styliser)
        button.className = "btn-supprimer";

        // On définit le texte affiché à l’intérieur du bouton
        button.textContent = " Supprimer l’image";

        // On stocke la clé de l’image (image1, image2 ou image3) dans un attribut data-*
        // Cela permet de savoir plus tard quelle image on veut supprimer
        button.dataset.imageKey = imageKey;

        // On ajoute un écouteur d’événement au clic sur le bouton
        // Celui-ci appelle la fonction `supprimerImage()` avec le nom de l’image concernée
        button.addEventListener("click", () => {
          supprimerImage(imageKey);
        });

        // On ajoute le bouton à la fin du bloc contenant l’image
        block.appendChild(button);
      }
    }
  });
});

// Affiche une popup si l’image principale est manquante (?error=image dans l’URL)
// Lorsque le DOM est entièrement chargé (structure HTML prête), on exécute la fonction
document.addEventListener("DOMContentLoaded", () => {
  // On récupère les paramètres de l'URL sous forme d'objet manipulable (ex : ?error=image devient { error: "image" })
  const params = new URLSearchParams(window.location.search);

  // Si le paramètre "error" est présent et vaut "image", et que la fonction afficherPopup() existe:
  if (params.get("error") === "image" && typeof afficherPopup === "function") {
    // On appelle la fonction afficherPopup pour afficher un message d'erreur à l'utilisateur
    afficherPopup("L’image principale est obligatoire.");

    // On supprime le paramètre "error" de l'URL (dans l'objet seulement, pas encore visuellement)
    params.delete("error");

    // On reconstruit l’URL propre sans le paramètre supprimé. On garde les autres éventuels paramètres.
    const cleanUrl = `${window.location.pathname}?${params.toString()}`;

    // On remplace l’URL affichée dans la barre d’adresse du navigateur (sans recharger la page),
    // en retirant le "?" inutile si aucun paramètre ne reste après suppression.
    window.history.replaceState(
      {}, // État vide (non utilisé ici)
      document.title, // Titre actuel de la page (affiché dans l’onglet)
      cleanUrl.endsWith("?") ? cleanUrl.slice(0, -1) : cleanUrl // Nettoie une éventuelle fin de chaîne "?"
    );
  }
});

// Rend ces deux fonctions accessibles dans d'autres scripts si besoin
window.updatePreview = updatePreview;
window.supprimerImage = supprimerImage;

// Méthode / Propriété

// document.addEventListener() | Attendre que le DOM soit prêt avant d’exécuter du code
// document.getElementById() | Sélectionner un élément HTML via son identifiant
// document.createElement() | Créer dynamiquement un élément HTML
// element.appendChild() | Ajouter un élément enfant dans le DOM
// element.addEventListener() | Attacher un événement (clic, change, etc.) à un élément
// element.querySelector() | Rechercher un élément enfant avec un sélecteur CSS
// Array.prototype.forEach() | Parcourir chaque élément d’un tableau
// URLSearchParams() | Lire et manipuler les paramètres dans l’URL
// params.get() | Obtenir la valeur d’un paramètre d’URL
// params.delete() | Supprimer un paramètre d’URL
// params.toString() | Convertir les paramètres en chaîne
// window.history.replaceState() | Modifier l’URL affichée sans recharger la page
// window.location.pathname | Obtenir le chemin actuel de l’URL (sans paramètres)
// String.prototype.includes() | Vérifier si une chaîne contient une sous-chaîne
// String.prototype.endsWith() | Vérifier si une chaîne se termine par une valeur
// String.prototype.slice() | Extraire une portion d’une chaîne
// typeof | Vérifier le type d’une variable ou fonction
// input.files | Accéder à la liste des fichiers sélectionnés
// FileReader() | Lire un fichier en local côté client
// reader.readAsDataURL() | Lire un fichier et le convertir en base64
// reader.onload | Définir une action après lecture d’un fichier
// element.style.display | Modifier dynamiquement l’affichage CSS
// element.remove() | Supprimer un élément du DOM
// element.dataset | Accéder à un attribut data-* personnalisé
// window.location.search | Obtenir la chaîne de requête de l’URL (?param=...)
