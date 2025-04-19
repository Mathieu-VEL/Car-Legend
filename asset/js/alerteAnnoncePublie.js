// Écouteur d'événement qui s'exécute quand tout le contenu HTML est chargé
document.addEventListener("DOMContentLoaded", () => {
  // Création d’un objet `URLSearchParams` pour accéder aux paramètres d’URL (ex : ?ajout=1)
  const params = new URLSearchParams(window.location.search);

  // --- Cas 1 : Succès de la publication ---
  if (params.get("ajout") === "1") {
    // afficherPopup() est une fonction personnalisée qui montre un message visuel sur la page
    // Le 2e paramètre "success" applique un style vert (message positif)
    afficherPopup("Annonce publiée avec succès.", "success");

    // Supprime le paramètre "ajout" de l'objet `params`
    params.delete("ajout");
  }

  // --- Cas 2 : Échec de la publication ---
  if (params.get("ajout") === "0") {
    // Affiche un message d’erreur en style rouge
    afficherPopup("Erreur lors de la publication de l’annonce.", "error");

    // Supprime également le paramètre
    params.delete("ajout");
  }

  // --- Nettoyage de l’URL dans le navigateur (sans recharger la page) ---

  // reconstruit une URL propre avec les paramètres restants (ex : ?id=5)
  // .toString() convertit tous les paramètres de `params` en chaîne de requête (ex : "id=5")
  const cleanUrl = `${window.location.pathname}?${params.toString()}`;

  // history.replaceState() remplace l'URL affichée dans la barre sans recharger la page
  // Paramètre 1 ({}): l'état de l'historique (non utilisé ici)
  // Paramètre 2 (document.title): garde le titre actuel de la page
  // Paramètre 3 (cleanUrl): nouvelle URL à afficher dans la barre d'adresse
  window.history.replaceState(
    {},
    document.title,
    // .endsWith("?") vérifie si la chaîne se termine par un "?" (ex: "/page?")
    // si oui, on le retire avec .slice(0, -1), sinon on garde l’URL propre telle quelle
    cleanUrl.endsWith("?") ? cleanUrl.slice(0, -1) : cleanUrl
  );
});