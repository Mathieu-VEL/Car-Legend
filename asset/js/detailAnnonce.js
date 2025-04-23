// Exécute le code une fois que le DOM est entièrement chargé
document.addEventListener("DOMContentLoaded", function () {
  // Récupération des éléments du DOM nécessaires à la modale de zoom
  const modale = document.getElementById("modale-zoom"); // Conteneur de la modale d’image
  const imageZoom = document.getElementById("image-agrandie"); // Image affichée en grand dans la modale
  const btnFermer = document.querySelector(".detail-fermer-modale"); // Bouton de fermeture (croix)

  // Sélectionne toutes les images cliquables (image principale + miniatures)
  const images = document.querySelectorAll(
    ".detail-image-principale, .detail-miniature-img"
  );

  // Sécurité : on s’assure que tous les éléments existent avant de continuer
  if (!modale || !imageZoom || !btnFermer || images.length === 0) {
    return;
  }

  // Pour chaque image cliquable, on ajoute un événement au clic
  images.forEach((img) => {
    img.addEventListener("click", function () {
      imageZoom.src = img.src; // Copie la source de l’image cliquée dans la modale
      modale.classList.remove("cachee"); // Affiche la modale (la classe "cachee" la rend invisible)
    });
  });

  // Lorsqu'on clique sur la croix de fermeture
  btnFermer.addEventListener("click", function () {
    modale.classList.add("cachee"); // Cache la modale à nouveau
    imageZoom.src = ""; // Vide la source de l’image affichée pour éviter un clignotement au prochain affichage
  });

  // Lorsque l’utilisateur clique en dehors de l’image (sur le fond sombre)
  modale.addEventListener("click", function (e) {
    if (e.target === modale) {
      modale.classList.add("cachee"); // Cache la modale
      imageZoom.src = ""; // Vide l’image
    }
  });
});


// Méthode / Propriété
// document.addEventListener() | Attendre que le DOM soit complètement chargé
// document.getElementById() | Sélectionner un élément HTML par son identifiant
// document.querySelector() | Sélectionner le premier élément correspondant à un sélecteur CSS
// document.querySelectorAll() | Sélectionner tous les éléments correspondant à un sélecteur CSS
// NodeList.prototype.forEach() | Parcourir une liste de nœuds (résultat de querySelectorAll)
// element.addEventListener() | Attacher un gestionnaire d’événement (ici click)
// element.src | Modifier ou lire la source d’une image
// element.classList.add() | Ajouter une classe CSS à un élément
// element.classList.remove() | Supprimer une classe CSS d’un élément
// event.target | Obtenir l’élément exact cliqué lors d’un événement