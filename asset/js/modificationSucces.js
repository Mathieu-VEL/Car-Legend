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
