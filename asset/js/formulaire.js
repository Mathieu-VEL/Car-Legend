// Attend que le DOM soit entièrement chargé
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("#form-inscription"); // Sélectionne le formulaire par ID
  if (!form) return; // Si le formulaire n'existe pas, on arrête

  // Écouteur d'événement lors de la soumission du formulaire
  form.addEventListener("submit", function (e) {
    const mdp = form.querySelector("#motdepasse").value; // Récupère la valeur du mot de passe
    const confirm = form.querySelector("#confirm_motdepasse").value; // Valeur du champ confirmation

    // Expression régulière : vérifie si le mot de passe contient :
    // - au moins une majuscule
    // - au moins un chiffre
    // - au moins un caractère spécial
    // - et fait minimum 8 caractères
    const regexMdp = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W]).{8,}$/;

    // Teste si le mot de passe est non conforme
    if (!regexMdp.test(mdp)) {
      afficherPopup(
        "Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.",
        "error"
      );
      e.preventDefault(); // Empêche l'envoi du formulaire
      return;
    }

    // Vérifie si les mots de passe ne correspondent pas
    if (mdp !== confirm) {
      afficherPopup("Les mots de passe ne correspondent pas.", "error");
      e.preventDefault(); // Bloque aussi l'envoi du formulaire
      return;
    }
  });
});

// Deuxième écouteur DOMContentLoaded pour la checklist visuelle
document.addEventListener("DOMContentLoaded", function () {
  const mdp = document.getElementById("motdepasse"); // Champ mot de passe

  // Sélection des éléments de la checklist
  const critereLongueur = document.getElementById("length");
  const critereMajuscule = document.getElementById("uppercase");
  const critereChiffre = document.getElementById("number");
  const critereSpecial = document.getElementById("special");

  // Vérifie que le champ et au moins un critère existent
  if (!mdp || !critereLongueur) return;

  // À chaque saisie dans le champ mot de passe
  mdp.addEventListener("input", () => {
    const valeur = mdp.value; // Valeur actuelle du champ

    // Teste si la longueur est ≥ 8 caractères
    valeur.length >= 8
      ? critereLongueur.classList.add("valid")
      : critereLongueur.classList.remove("valid");

    // Teste la présence d'une majuscule
    /[A-Z]/.test(valeur)
      ? critereMajuscule.classList.add("valid")
      : critereMajuscule.classList.remove("valid");

    // Teste la présence d’un chiffre
    /\d/.test(valeur)
      ? critereChiffre.classList.add("valid")
      : critereChiffre.classList.remove("valid");

    // Teste la présence d’un caractère spécial
    /[\W_]/.test(valeur)
      ? critereSpecial.classList.add("valid")
      : critereSpecial.classList.remove("valid");
  });
});
