// Déclare l’URL du placeholder (utilisée si l’image est supprimée)
if (typeof PLACEHOLDER_URL === "undefined") {
  var PLACEHOLDER_URL = "/projet-final/asset/images/placeholder.jpg";
}

// Fonction déclenchée lors du changement d’image : affiche l’aperçu et le bouton supprimer
function updatePreview(input) {
  const imageKey = input.id; // Ex: "image1", "image2"
  const preview = document.getElementById("preview-" + imageKey); // L’élément <img> lié
  const file = input.files[0]; // Récupère le fichier image choisi

  if (file) {
    const reader = new FileReader(); // Crée un lecteur pour lire le fichier image
    reader.onload = function (e) {
      preview.src = e.target.result; // Affiche l’image dans la balise <img>
      preview.style.display = "block"; // Rendu visible (si masqué)

      const block = document.getElementById("block-" + imageKey);
      // Si aucun bouton supprimer n’existe encore → on le crée dynamiquement
      if (!block.querySelector(".btn-supprimer")) {
        const button = document.createElement("button");
        button.type = "button";
        button.className = "btn-supprimer";
        button.textContent = " Supprimer l’image";
        button.dataset.imageKey = imageKey;
        block.appendChild(button);

        button.addEventListener("click", () => {
          supprimerImage(imageKey); // Appel la fonction de suppression
        });
      }
    };
    reader.readAsDataURL(file); // Lecture en base64 (utilisé dans les balises <img>)
  }
}

// Fonction pour supprimer une image déjà affichée
function supprimerImage(imageKey) {
  const preview = document.getElementById("preview-" + imageKey);
  const deleteField = document.getElementById("delete-" + imageKey); // Champ caché pour le backend
  const fileInput = document.getElementById(imageKey);
  const block = document.getElementById("block-" + imageKey);

  if (fileInput) fileInput.value = ""; // Vide le champ input file
  if (deleteField) deleteField.value = "1"; // Marque l’image comme supprimée pour le traitement PHP

  if (preview) {
    preview.src = PLACEHOLDER_URL; // Remplace l’image par un placeholder
    preview.style.display = "block";
  }

  const btn = block.querySelector(".btn-supprimer");
  if (btn) btn.remove(); // Supprime le bouton

  if (typeof afficherPopup === "function") {
    afficherPopup("Image supprimée (placeholder affiché)."); // Message de confirmation
  }
}

// Génère automatiquement les boutons supprimer pour les images déjà existantes (en édition)
document.addEventListener("DOMContentLoaded", () => {
  ["image1", "image2", "image3"].forEach((imageKey) => {
    const preview = document.getElementById("preview-" + imageKey);
    const block = document.getElementById("block-" + imageKey);

    if (
      preview &&
      block &&
      preview.src &&
      !preview.src.includes("placeholder.jpg")
    ) {
      if (!block.querySelector(".btn-supprimer")) {
        const button = document.createElement("button");
        button.type = "button";
        button.className = "btn-supprimer";
        button.textContent = " Supprimer l’image";
        button.dataset.imageKey = imageKey;

        button.addEventListener("click", () => {
          supprimerImage(imageKey);
        });

        block.appendChild(button);
      }
    }
  });
});

// Affiche une popup si l’image principale est manquante (?error=image dans l’URL)
document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);

  if (params.get("error") === "image" && typeof afficherPopup === "function") {
    afficherPopup("L’image principale est obligatoire.");
    params.delete("error");

    const cleanUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.replaceState(
      {},
      document.title,
      cleanUrl.endsWith("?") ? cleanUrl.slice(0, -1) : cleanUrl
    );
  }
});

// Rend ces deux fonctions accessibles dans d'autres scripts si besoin
window.updatePreview = updatePreview;
window.supprimerImage = supprimerImage;
