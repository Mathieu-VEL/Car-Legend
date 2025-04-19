# CAR LEGEND

**Plateforme de vente de voitures anciennes** – Projet de fin de formation  
_Titre RNCP 37273 – Développeur Web Full-Stack_  
Candidat : Mathieu Chaltane  
Session : Juin 2025  

---

## Description

**Car Legend** est un site web dédié à la vente de véhicules anciens.  
Il permet aux passionnés, collectionneurs et professionnels de publier et consulter des annonces, de gérer leurs favoris, d’échanger par messagerie interne, et d’enrichir les fiches avec des données techniques via l’API CarQuery.

---

## Fonctionnalités principales

- Authentification (inscription, connexion, déconnexion)
- Gestion des annonces (ajout, modification, suppression)
- Galerie d’images (1 à 3 par annonce)
- Recherche par filtres (marque, modèle, année…)
- Favoris dynamiques (AJAX)
- Messagerie privée entre utilisateurs
- Espace profil + paramètres (avatar, mot de passe)
- Tableau de bord admin (modération)
- Intégration API CarQuery
- Responsive design + alertes personnalisées

---

## Technologies utilisées

- HTML5 / SCSS / JavaScript (Vanilla)
- PHP procédural avec architecture MVC personnalisée
- MySQL + PDO
- API externe : [CarQuery API](https://www.carqueryapi.com/)
- Aucune librairie JS (tout codé manuellement)
- Compilation SCSS via `sass --watch`

---

## Arborescence du projet

PROJET-FINAL/
├── index.php
├── BD.sql
├── .htaccess
│
│
├── asset/
│   ├── css/
│   │   ├── dist/
│   │   │   ├── style.css
│   │   │   └── style.css.map
│   │   └── scss/
│   │       ├── style.scss
│   │       ├── base/
│   │       │   └── global.scss
│   │       └── pages/
│   │           ├── 404.scss
│   │           ├── accueil.scss
│   │           ├── admin.scss
│   │           ├── ajouterAnnonce.scss
│   │           ├── alertes.scss
│   │           ├── annonce.scss
│   │           ├── connexion.scss
│   │           ├── connexionAdmin.scss
│   │           ├── contacter.scss
│   │           ├── detailAnnonce.scss
│   │           ├── favoris.scss
│   │           ├── footer.scss
│   │           ├── header.scss
│   │           ├── inscription.scss
│   │           ├── mesAnnonce.scss
│   │           ├── message.scss
│   │           ├── modifierAnnonce.scss
│   │           ├── pagination.scss
│   │           ├── parametres.scss
│   │           ├── profil.scss
│   │           ├── quiSommesNous.scss
│   │           ├── rgpd.scss
│   │           └── suppressionAnnonce.scss
│   │
│   ├── js/
│   │   ├── ajouteAnnonce.js
│   │   ├── alerteAnnoncePublie.js
│   │   ├── alerteInscription.js
│   │   ├── alerteProfile.js
│   │   ├── alertes.js
│   │   ├── api.js
│   │   ├── favoris.js
│   │   ├── formulaire.js
│   │   ├── modificationSucces.js
│   │   ├── modifierAnnonce.js
│   │   ├── popupSuppressionCompte.js
│   │   ├── suppressionAnnonce.js
│   │   └── validationAnnonce.js
│   │
│   ├── images/
│   └── photos/
│
├── controleur/
│   ├── admin.php
│   ├── config.php
│   ├── connexion.php
│   ├── connexionAdmin.php
│   ├── controleAccueil.php
│   ├── controleAnnonce.php
│   ├── controleErreur.php
│   ├── controleFavoris.php
│   ├── controleMessage.php
│   ├── controleUtilisateur.php
│   ├── deconnexion.php
│   ├── inscription.php
│   ├── routage.php
│   └── traitementConnexionAdmin.php
│
├── modele/
│   ├── authentification.php
│   ├── bd.annonce.php
│   ├── bd.favoris.php
│   ├── bd.message.php
│   ├── bd.php
│   ├── bd.utilisateur.php
│   └── uploadImage.php
│
├── vue/
│   ├── admin/
│   │   ├── vueAdmin.php
│   │   └── vueConnexionAdmin.php
│   ├── annonce/
│   │   ├── vueAjouterAnnonce.php
│   │   ├── vueAnnonce.php
│   │   ├── vueDetailAnnonce.php
│   │   ├── vueFavoris.php
│   │   ├── vueMesAnnonces.php
│   │   └── vueModifierAnnonce.php
│   ├── commun/
│   │   ├── footer.php
│   │   ├── header.php
│   │   ├── vue404.php
│   │   ├── vueAccueil.php
│   │   ├── vueRGPD.php
│   │   └── vueQuiSommesNous.php
│   ├── message/
│   │   ├── vueContacter.php
│   │   ├── vueConversations.php
│   │   └── vueMessagerie.php
│   └── utilisateur/
│       ├── vueConnexion.php
│       ├── vueDeconnexion.php
│       ├── vueInscription.php
│       ├── vueParametres.php
│       └── vueProfile.php


---

## Installation

1. Cloner le repo :
```bash
git clone https://github.com/Mathieu-VEL/Car-Legend.git
