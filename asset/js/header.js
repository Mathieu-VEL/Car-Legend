// Écouteur d’événement DOMContentLoaded → attend que le DOM soit prêt
document.addEventListener("DOMContentLoaded", () => {
  // Récupère l’élément du bouton burger via son ID
  const burger = document.getElementById("burger-menu");

  // Récupère l’en-tête principal via sa classe CSS
  const header = document.querySelector(".site-header");

  // Ajoute un événement "click" au bouton burger
  burger.addEventListener("click", () => {
    // Toggle (ajoute ou retire) la classe CSS "open" à l’élément header
    // Cela active l’affichage du menu mobile dans le SCSS
    header.classList.toggle("open");
  });
});


// Méthode / Propriété
// document.addEventListener() | Attendre que le DOM soit entièrement chargé
// document.getElementById() | Sélectionner un élément HTML par son identifiant
// document.querySelector() | Sélectionner un élément HTML par son sélecteur CSS
// element.addEventListener() | Attacher un gestionnaire d’événement
// element.classList.toggle() | Ajouter ou retirer une classe CSS dynamiquement