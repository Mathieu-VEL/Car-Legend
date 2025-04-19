// Exécute le code une fois que le DOM est complètement chargé
document.addEventListener("DOMContentLoaded", function () {
  // Récupération des éléments HTML nécessaires pour l’interaction API
  const selectAnnee = document.getElementById("select-annee"); // Liste déroulante des années
  const selectMarque = document.getElementById("select-marque"); // Liste déroulante des marques
  const selectModele = document.getElementById("select-modele"); // Liste déroulante des modèles
  const comparaison = document.getElementById("comparaison-api"); // Bloc HTML pour afficher la fiche technique
  const btnRecherche = document.getElementById("btn-recherche"); // Bouton pour déclencher la recherche

  // Si un des éléments est introuvable, on arrête le script immédiatement
  if (!selectAnnee || !selectMarque || !selectModele || !comparaison || !btnRecherche)
    return;

  // Remplissage dynamique de la liste des années de 1980 à 2005
  for (let annee = 1980; annee <= 2005; annee++) {
    const option = document.createElement("option"); // Crée une balise <option>
    option.value = annee; // Valeur transmise à la sélection
    option.textContent = annee; // Texte visible dans la liste
    selectAnnee.appendChild(option); // Ajoute cette option dans la liste <select>
  }

  // --- Étape 1 : Charger les marques de véhicules ---
  const urlMakes = encodeURIComponent("https://www.carqueryapi.com/api/0.3/?cmd=getMakes"); // Encode l’URL pour éviter les erreurs de requête

  // Utilisation d’un proxy CORS pour contourner les restrictions de l’API
  fetch(`https://corsproxy.io/?url=${urlMakes}`)
    .then((response) => response.json()) // Conversion de la réponse JSON
    .then((data) => {
      if (!data.Makes) return; // Si aucune marque reçue, on sort
      data.Makes.forEach((make) => {
        const option = document.createElement("option");
        option.value = make.make_id; // Valeur transmise (ID)
        option.textContent = make.make_display; // Nom affiché (ex : Ford)
        selectMarque.appendChild(option);
      });
    })
    .catch(() => {
      // Pas d'action en cas d'erreur (silencieux)
    });

  // --- Étape 2 : Charger les modèles en fonction de la marque et de l’année ---
  selectMarque.addEventListener("change", () => {
    const marque = selectMarque.value; // Marque sélectionnée
    const annee = selectAnnee.value; // Année sélectionnée

    if (!marque || !annee) return; // Si une des valeurs est absente, on arrête

    selectModele.innerHTML = '<option value="">Modèle</option>'; // Réinitialise la liste des modèles

    const rawUrl = `https://www.carqueryapi.com/api/0.3/?cmd=getModels&make=${marque}&year=${annee}`; // URL API brute
    const url = `https://corsproxy.io/?url=${encodeURIComponent(rawUrl)}`; // Version proxy CORS

    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        if (!data.Models || !data.Models.length) return; // Si aucun modèle, on sort

        data.Models.forEach((modele) => {
          const option = document.createElement("option");
          option.value = modele.model_name; // Nom du modèle
          option.textContent = modele.model_name;
          selectModele.appendChild(option);
        });

        verifierChamps(); // Vérifie si on peut activer le bouton
      })
      .catch(() => {
        // Erreur silencieuse
      });
  });

  // --- Étape 3 : Recherche des informations techniques (fiche technique) ---
  btnRecherche.addEventListener("click", () => {
    const marque = selectMarque.value;
    const modele = selectModele.value;
    const annee = selectAnnee.value;

    if (!marque || !modele || !annee) return;

    const rawUrl = `https://www.carqueryapi.com/api/0.3/?cmd=getTrims&make=${marque}&model=${modele}&year=${annee}`;
    const url = `https://corsproxy.io/?url=${encodeURIComponent(rawUrl)}`;

    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        if (!data.Trims || !data.Trims.length) return;

        const infos = data.Trims[0]; // On prend le premier résultat (le plus courant)

        // Affiche dynamiquement les infos dans le bloc HTML
        comparaison.innerHTML = `
          <div class="fiche-technique">
            <div class="ligne"><span><strong>Marque :</strong></span> ${infos.make_display}</div>
            <div class="ligne"><span><strong>Modèle :</strong></span> ${infos.model_name}</div>
            <div class="ligne"><span><strong>Année :</strong></span> ${infos.model_year}</div>
            <div class="ligne"><span><strong>Moteur :</strong></span> ${infos.model_engine_fuel || "Non précisé"}</div>
            <div class="ligne"><span><strong>Transmission :</strong></span> ${infos.model_transmission_type || "Non précisée"}</div>
            <div class="ligne"><span><strong>Carrosserie :</strong></span> ${infos.model_body || "Non précisée"}</div>
            <div class="ligne"><span><strong>Portes :</strong></span> ${infos.model_doors || "?"}</div>
          </div>
        `;
      })
      .catch(() => {
        // Erreur silencieuse
      });
  });

  // --- Vérifie si tous les champs sont remplis pour activer le bouton ---
  selectAnnee.addEventListener("change", verifierChamps);
  selectMarque.addEventListener("change", verifierChamps);
  selectModele.addEventListener("change", verifierChamps);

  // Fonction utilitaire pour activer/désactiver le bouton de recherche
  function verifierChamps() {
    const marque = selectMarque.value;
    const modele = selectModele.value;
    const annee = selectAnnee.value;
    if (marque && modele && annee) {
      btnRecherche.removeAttribute("disabled"); // Active le bouton
    } else {
      btnRecherche.setAttribute("disabled", true); // Désactive le bouton
    }
  }
});
