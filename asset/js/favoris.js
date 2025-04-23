// Attend que tout le DOM soit chargé avant d'exécuter le code
document.addEventListener("DOMContentLoaded", () => {
  // Sélectionne tous les éléments HTML qui ont la classe .favori-toggle
  // Ces éléments servent à ajouter ou retirer une annonce des favoris
  const favoris = document.querySelectorAll(".favori-toggle");

  // Pour chaque bouton favori trouvé
  favoris.forEach((favori) => {
    // On écoute le clic sur le bouton
    favori.addEventListener("click", (e) => {
      e.preventDefault(); // Annule le comportement par défaut (ex: lien)

      // Récupère l'identifiant de l'annonce depuis l'attribut data-id
      const idAnnonce = favori.dataset.id;

      // Récupère l'action à effectuer : "ajouter" ou "supprimer"
      const action = favori.dataset.action;

      // Effectue une requête AJAX vers index.php avec les bons paramètres
      fetch(`index.php?page=favoris&action=${action}&id=${idAnnonce}`, {
        headers: {
          "X-Requested-With": "XMLHttpRequest", // permet à PHP de savoir que c’est un appel AJAX
        },
      })
        .then((response) => response.text()) // Récupère la réponse sous forme de texte
        .then((result) => {
          // Vérifie si la réponse contient "ok" (réussite côté PHP)
          if (result.includes("ok")) {
            // Message selon l'action effectuée
            const message =
              action === "ajouter"
                ? "Annonce ajoutée aux favoris."
                : "Annonce retirée des favoris.";

            // Affiche une popup d’alerte avec le bon type (vert ou rouge)
            if (typeof afficherPopup === "function") {
              afficherPopup(
                message,
                action === "ajouter" ? "success" : "error"
              );
            }
          } else {
            // Message d’erreur si le serveur n’a pas retourné "ok"
            if (typeof afficherPopup === "function") {
              afficherPopup("Une erreur est survenue.", "error");
            }
          }

          // Mise à jour du texte et de l'action dans le bouton
          if (action === "ajouter") {
            favori.innerText = "Retirer des favoris";
            favori.dataset.action = "supprimer"; // Prochaine action à effectuer
          } else {
            favori.innerText = "Ajouter aux favoris";
            favori.dataset.action = "ajouter";
          }

          // Ajoute temporairement une classe CSS pour une animation visuelle
          favori.classList.add("favori-updated");
          setTimeout(() => {
            favori.classList.remove("favori-updated");
          }, 600); // Supprime la classe après 600ms
        })
        .catch((error) => {
          // En cas d'erreur de communication réseau
          console.error("Erreur AJAX :", error);

          if (typeof afficherPopup === "function") {
            afficherPopup("Erreur de communication avec le serveur.", "error");
          }
        });
    });
  });
});



// Méthode / Propriété
// document.addEventListener() | Attendre que le DOM soit complètement chargé
// document.querySelectorAll() | Sélectionner tous les éléments correspondant à un sélecteur CSS
// NodeList.prototype.forEach() | Parcourir une liste de nœuds HTML
// element.addEventListener() | Attacher un gestionnaire d’événement
// event.preventDefault() | Annuler le comportement par défaut de l’événement
// element.dataset | Accéder aux attributs personnalisés data-*
// fetch() | Envoyer une requête réseau HTTP asynchrone
// response.text() | Lire la réponse serveur comme texte brut
// Promise.then() | Exécuter une action après une réponse réussie
// Promise.catch() | Gérer les erreurs dans une requête asynchrone
// String.prototype.includes() | Vérifier si une chaîne contient une sous-chaîne
// typeof | Vérifier le type d’une variable ou fonction
// element.innerText | Lire ou modifier le texte visible d’un élément HTML
// element.classList.add() | Ajouter une ou plusieurs classes CSS
// element.classList.remove() | Retirer une ou plusieurs classes CSS
// setTimeout() | Retarder l’exécution d’un bloc de code après un délai
// console.error() | Afficher une erreur dans la console de développement