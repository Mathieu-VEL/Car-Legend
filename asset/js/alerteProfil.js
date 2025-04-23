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
      document.title, // 1er param : garde le titre actuel de l’onglet
      cleanUrl.endsWith("?") // 2e param : si l’URL se termine par ? (aucun param restant)
        ? cleanUrl.slice(0, -1) // alors on retire le ? final inutile
        : cleanUrl // sinon on conserve l’URL telle quelle
    );
  }
});



// Méthode / Propriété
// document.addEventListener() | Attendre que le DOM soit complètement chargé
// URLSearchParams() | Manipuler les paramètres de l’URL
// params.get() | Obtenir la valeur d’un paramètre
// params.delete() | Supprimer un paramètre d’URL
// params.toString() | Reconvertir les paramètres en chaîne
// window.location.search | Récupérer la chaîne des paramètres de l’URL
// window.location.pathname | Obtenir le chemin de la page sans les paramètres
// String.prototype.endsWith() | Vérifier si une chaîne se termine par une sous-chaîne
// String.prototype.slice() | Supprimer ou extraire une partie d’une chaîne
// window.history.replaceState() | Modifier l’URL affichée sans recharger la page
// document.title | Obtenir le titre de l’onglet en cours
// typeof | Vérifier le type d’une variable ou fonction