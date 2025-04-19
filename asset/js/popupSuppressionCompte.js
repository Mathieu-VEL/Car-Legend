// Attend que tout le HTML soit chargé avant d’exécuter le script
document.addEventListener("DOMContentLoaded", () => {
  // On récupère les éléments HTML nécessaires
  const btnOuvrir = document.getElementById("ouvrir-confirmation-suppression"); // bouton pour déclencher la popup
  const popup = document.getElementById("popup-confirmation-suppression"); // bloc HTML de la popup
  const btnAnnuler = document.getElementById("annuler-suppression"); // bouton "Annuler" dans la popup

  // Vérifie que tous les éléments existent bien
  if (!btnOuvrir || !popup || !btnAnnuler) return; // Si un seul est manquant, on arrête le script

  // Lorsqu’on clique sur le bouton “Supprimer mon compte”
  btnOuvrir.addEventListener("click", () => {
    popup.classList.remove("cachee"); // enlève la classe CSS qui masque la popup
    popup.classList.add("show"); // ajoute une classe visible (peut lancer une animation CSS)
  });

  // Lorsqu’on clique sur le bouton “Annuler” dans la popup
  btnAnnuler.addEventListener("click", () => {
    popup.classList.remove("show"); // enlève la classe d’affichage
    popup.classList.add("cachee"); // remet la classe qui cache la popup
  });
});
