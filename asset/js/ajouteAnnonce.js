// Exécute le code une fois que le DOM est complètement chargé
document.addEventListener("DOMContentLoaded", () => {
  // Déclaration d'un tableau contenant les objets pour chaque image à gérer
  // Chaque objet contient :
  // - input : l’ID de l’input file
  // - preview : l’ID de la balise <img> pour l’aperçu
  // - btn : l’ID du bouton de suppression correspondant
  const images = [
    { input: "image1", preview: "preview-image1", btn: "supprimer-image1" },
    { input: "image2", preview: "preview-image2", btn: "supprimer-image2" },
    { input: "image3", preview: "preview-image3", btn: "supprimer-image3" },
  ];

  // Pour chaque bloc image, on configure les comportements
  images.forEach(({ input, preview, btn }) => {
    // Récupération des éléments HTML via leur ID
    const inputEl = document.getElementById(input); // Champ de fichier image
    const previewEl = document.getElementById(preview); // Image affichée à l'écran
    const btnEl = document.getElementById(btn); // Bouton pour supprimer l’image

    // Vérifie que les trois éléments existent bien dans le DOM
    if (inputEl && previewEl && btnEl) {
      // Lorsqu’un fichier est sélectionné
      inputEl.addEventListener("change", () => {
        const file = inputEl.files[0]; // Récupère le premier fichier sélectionné
        if (file) {
          const reader = new FileReader(); // Crée un lecteur de fichiers local
          reader.onload = (e) => {
            previewEl.src = e.target.result; // Affiche l’aperçu de l’image (base64)
            btnEl.style.display = "inline-block"; // Affiche le bouton "Supprimer"
          };
          reader.readAsDataURL(file); // Convertit le fichier en URL base64
        }
      });

      // Lorsqu’on clique sur “Supprimer”
      btnEl.addEventListener("click", () => {
        inputEl.value = ""; // Vide le champ file
        previewEl.src = "asset/images/placeholder.jpg"; // Remet le placeholder par défaut
        btnEl.style.display = "none"; // Cache le bouton
      });
    }
  });
});

// Deuxième écouteur DOMContentLoaded (séparé pour une autre logique)
// Gère l'affichage d'un message d'erreur si une image principale est manquante
document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search); // Analyse les paramètres d’URL

  // Si erreur=image dans l’URL, on affiche une alerte
  if (params.get("error") === "image" && typeof afficherPopup === "function") {
    afficherPopup("L’image principale est obligatoire."); // Affiche une alerte d’erreur
    params.delete("error"); // Supprime le paramètre pour ne pas répéter

    // Construit une nouvelle URL sans "error"
    const cleanUrl = `${window.location.pathname}?${params.toString()}`;

    // Met à jour l’URL sans recharger la page :
    window.history.replaceState(
      {}, // État vide (pas utilisé ici)
      document.title, // On garde le titre actuel de la page
      cleanUrl.endsWith("?") // Vérifie si l’URL finit par ?
        ? cleanUrl.slice(0, -1) // Si oui, on le supprime
        : cleanUrl // Sinon, on garde tel quel
    );
  }
});
