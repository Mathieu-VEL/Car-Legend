// Exécute ce bloc une fois que le DOM est entièrement chargé
document.addEventListener("DOMContentLoaded", () => {
  // Récupère les paramètres de l’URL actuelle sous forme exploitable
  const params = new URLSearchParams(window.location.search);

  // Si le paramètre "maj=profil" est détecté ET que la fonction afficherPopup existe
  if (params.get("maj") === "profil" && typeof afficherPopup === "function") {
    afficherPopup("Profil mis à jour avec succès.", "success"); // Affiche une alerte de succès
    params.delete("maj"); // Supprime le paramètre de l'URL pour éviter de réafficher l'alerte

    // Construit une nouvelle URL propre (sans le paramètre "maj")
    const cleanUrl = `${window.location.pathname}?${params.toString()}`;

    // Remplace l’URL actuelle dans l’historique sans recharger la page
    window.history.replaceState(
      {}, // 1er param : objet d’état (pas utilisé ici)
      document.title, // 2e param : garde le titre actuel de l’onglet
      cleanUrl.endsWith("?") // 3e param : si l’URL se termine par ? (aucun param restant)
        ? cleanUrl.slice(0, -1) // alors on retire le ? final inutile
        : cleanUrl // sinon on conserve l’URL telle quelle
    );
  }
});
