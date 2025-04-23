// Fonction principale pour afficher une alerte personnalisée
function afficherPopup(message, type = "info") {
  const alerte = document.querySelector(".alerte-popup"); // Récupère le bloc HTML de l'alerte
  let texte = alerte.querySelector("#texte-alerte"); // Récupère le <p> contenant le message (s'il existe)

  // Sécurité : si l’élément .alerte-popup est introuvable dans le DOM, on quitte la fonction
  if (!alerte) return;

  // Si aucun paragraphe <p id="texte-alerte"> n'est encore présent, on le crée
  if (!texte) {
    texte = document.createElement("p"); // Crée un élément <p>
    texte.id = "texte-alerte"; // Donne un ID pour le cibler ensuite
    alerte.innerHTML = ""; // Vide tout le contenu actuel
    alerte.appendChild(texte); // Ajoute le paragraphe dans le bloc d’alerte
  }

  // Injecte le texte du message (ou un message par défaut s'il est vide)
  texte.textContent = message || "Une erreur inconnue est survenue.";

  // Supprime toutes les classes de style précédentes pour éviter les conflits
  alerte.classList.remove("cachee", "success", "error", "info");

  // Rend l’alerte visible avec la classe CSS "show"
  alerte.classList.add("show");

  // Ajoute une classe spécifique selon le type de message
  switch (type) {
    case "success":
      alerte.classList.add("success"); // Message vert
      break;
    case "error":
      alerte.classList.add("error"); // Message rouge
      break;
    case "info":
    default:
      alerte.classList.add("info"); // Message gris ou neutre par défaut
      break;
  }

  // Attends 3 secondes avant de masquer automatiquement le message
  setTimeout(() => {
    // On retire toutes les classes de style
    alerte.classList.remove("show", "success", "error", "info");

    // On ajoute la classe "cachee" pour bien cacher l'alerte à la fin
    alerte.classList.add("cachee");
  }, 3000);
}

// Lors du chargement du DOM, on vérifie s’il y a un message injecté via PHP
document.addEventListener("DOMContentLoaded", () => {
  const alertDiv = document.getElementById("alerte-dynamique"); // Récupère le bloc inséré en HTML (via PHP)

  // Si ce bloc existe
  if (alertDiv) {
    const message = alertDiv.dataset.message; // Récupère l’attribut data-message (ex: data-message="Erreur !")
    const type = alertDiv.dataset.type || "info"; // Récupère le type ou "info" par défaut (ex: data-type="success")

    // Si un message est présent et que la fonction est bien définie
    if (message && typeof afficherPopup === "function") {
      afficherPopup(message, type); // Affiche le popup avec message + style (info/success/error)
    }

    alertDiv.remove(); // Supprime le bloc HTML une fois le message traité pour éviter une double alerte
  }
});



// Méthode / Propriété
// function nom() | Définir une fonction
// document.querySelector() | Sélectionner le premier élément correspondant à un sélecteur
// document.getElementById() | Sélectionner un élément HTML par son id
// element.querySelector() | Sélectionner un enfant d’un élément via un sélecteur CSS
// document.createElement() | Créer dynamiquement un élément HTML
// element.appendChild() | Ajouter un élément dans le DOM
// element.textContent | Définir ou lire le contenu textuel d’un élément
// element.classList.add() | Ajouter une ou plusieurs classes CSS
// element.classList.remove() | Supprimer une ou plusieurs classes CSS
// switch / case | Exécuter un bloc différent selon une valeur
// setTimeout() | Exécuter une fonction après un délai
// typeof | Vérifier le type d’une variable ou fonction
// element.dataset | Accéder aux attributs data-* personnalisés
// element.remove() | Supprimer un élément du DOM
// document.addEventListener() | Attacher un écouteur d’événement (ici DOMContentLoaded)
