// Exécute le script une fois que le DOM est complètement chargé
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form-annonce"); // Le formulaire d’ajout/modif d’annonce
  const description = document.getElementById("description"); // Champ texte de description
  const compteur = document.getElementById("compteur-caracteres"); // Compteur de caractères affiché
  const barre = document.getElementById("barre-remplie"); // Barre visuelle de progression

  // Si les éléments nécessaires sont présents
  if (description && compteur && barre) {
    const max = parseInt(description.getAttribute("maxlength")); // Récupère la valeur max (3000 caractères)

    // Fonction de mise à jour dynamique du compteur et de la barre
    const majCompteur = () => {
      const long = description.value.length; // Nombre de caractères actuels
      compteur.textContent = long; // Met à jour l’affichage numérique
      barre.style.width = `${(long / max) * 100}%`; // Barre remplie (en %)

      // Si on approche de la limite, la couleur devient rouge
      barre.style.backgroundColor = long > max - 300 ? "#e74c3c" : "#f9c349";
    };

    majCompteur(); // Initialisation à l’ouverture de la page
    description.addEventListener("input", majCompteur); // Mise à jour à chaque frappe clavier
  }

  if (!form) return; // Sécurité : on arrête si le formulaire n’existe pas

  // Écoute de la soumission du formulaire
  form.addEventListener("submit", (e) => {
    // On récupère toutes les valeurs à valider
    const titre = form.querySelector("#titre").value.trim();
    const desc = form.querySelector("#description").value.trim();
    const prix = parseFloat(form.querySelector("#prix").value);
    const kilometrage = parseInt(form.querySelector("#kilometrage").value);
    const marque = form.querySelector("#marque").value.trim();
    const modele = form.querySelector("#modele").value.trim();
    const annee = parseInt(form.querySelector("#annee").value);
    const carburant = form.querySelector("#carburant").value;

    const anneeActuelle = new Date().getFullYear(); // Année système

    // Vérification des conditions d’erreurs sur chaque champ
    if (
      titre.length === 0 || titre.length > 100 ||
      desc.length === 0 || desc.length > 3000 ||
      isNaN(prix) || prix <= 0 ||
      isNaN(kilometrage) || kilometrage <= 0 ||
      marque.length === 0 ||
      modele.length === 0 ||
      isNaN(annee) || annee < 1885 || annee > anneeActuelle ||
      !carburant
    ) {
      e.preventDefault(); // Bloque l’envoi si une erreur est détectée
    }
  });
});


// Méthode / Propriété | Rôle
// document.addEventListener() | Attendre que le DOM soit complètement chargé
// document.getElementById() | Sélectionner un élément HTML par son identifiant
// element.getAttribute() | Lire la valeur d’un attribut HTML
// element.textContent | Lire ou modifier le texte affiché d’un élément
// element.style.width | Définir dynamiquement la largeur CSS d’un élément
// element.style.backgroundColor | Modifier dynamiquement la couleur de fond d’un élément
// element.addEventListener() | Attacher un gestionnaire d’événement (input, submit)
// element.value | Obtenir ou définir la valeur d’un champ de formulaire
// String.prototype.trim() | Supprimer les espaces autour d’une chaîne
// parseInt() | Convertir une chaîne en entier
// parseFloat() | Convertir une chaîne en nombre décimal
// isNaN() | Vérifier si une valeur n’est pas un nombre valide
// new Date().getFullYear() | Récupérer l’année actuelle
// event.preventDefault() | Empêcher l’envoi du formulaire si une erreur est détectée