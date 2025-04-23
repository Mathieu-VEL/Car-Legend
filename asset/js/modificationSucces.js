// Attendre que le DOM soit entièrement chargé
document.addEventListener("DOMContentLoaded", () => {
  // Création d’un objet URLSearchParams pour accéder aux paramètres d’URL (ex : ?maj=1)
  const params = new URLSearchParams(window.location.search);

  // Si le paramètre "maj=1" est présent → succès de la mise à jour
  if (params.get("maj") === "1" && typeof afficherPopup === "function") {
    afficherPopup("Annonce mise à jour avec succès."); // Message de confirmation
    params.delete("maj"); // Supprime le paramètre de l’URL
  }

  // Si le paramètre "error=image" est présent → erreur de validation image
  if (params.get("error") === "image" && typeof afficherPopup === "function") {
    afficherPopup(
      "L’image principale est obligatoire. Veuillez en choisir une.",
      "error" // Style rouge
    );
    params.delete("error"); // Supprime le paramètre d’erreur
  }

  // Nettoyage : met à jour l’URL sans rechargement de la page
  const cleanUrl = `${window.location.pathname}?${params.toString()}`;

  window.history.replaceState(
    {}, // Objet d’état (non utilisé ici)
    document.title, // Conserve le titre actuel de la page
    cleanUrl.endsWith("?") ? cleanUrl.slice(0, -1) : cleanUrl
    // Enlève le ? si la chaîne se termine par "?"
  );
});


// Méthode / Propriété
// document.addEventListener() | Attendre que le DOM soit chargé
// URLSearchParams() | Lire et manipuler les paramètres dans l’URL
// params.get() | Obtenir la valeur d’un paramètre d’URL
// params.delete() | Supprimer un paramètre d’URL
// params.toString() | Convertir l’ensemble des paramètres en chaîne
// window.location.search | Accéder aux paramètres de l’URL
// window.location.pathname | Accéder au chemin de la page (sans les paramètres)
// window.history.replaceState() | Modifier l’URL dans la barre d’adresse sans recharger la page
// String.prototype.endsWith() | Vérifier si une chaîne se termine par une sous-chaîne
// String.prototype.slice() | Extraire une portion d’une chaîne de caractères
// typeof | Vérifier le type d’une variable ou fonction