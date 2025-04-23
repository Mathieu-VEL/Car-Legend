## CAR LEGEND
 
 Plateforme de vente de voitures anciennes – Projet de fin de formation  
 Titre RNCP 37273 – Développeur Web Full-Stack
 Candidat : Mathieu Chaltane  
 Session : Juin 2025  
 
 ---
 
 ## Description
 
 Car Legend est un site web dédié à la vente de véhicules anciens.  
 Il permet aux passionnés, collectionneurs et professionnels de publier et consulter des annonces, de gérer leurs favoris, d’échanger par messagerie interne, et d’enrichir les fiches avec des données techniques via l’API CarQuery.
 
 ---
 
 ## Fonctionnalités principales
 
 - Authentification (inscription, connexion, déconnexion)
 - Gestion des annonces (ajout, modification, suppression)
 - Galerie d’images (1 à 3 par annonce)
 - Recherche par filtres (marque, modèle, année…)
 - Favoris dynamiques
 - Messagerie privée entre utilisateurs
 - Espace profil + paramètres (avatar,nom prenom)
 - Tableau de bord admin (modération)
 - Intégration API CarQuery
 - Responsive design + alertes personnalisées
 
 ---
 
 ## Technologies utilisées
 
 - HTML5 / SCSS / JavaScript (Vanilla)
 - PHP procédural avec architecture MVC personnalisée
 - MySQL
 - API externe : CarQuery API https://www.carqueryapi.com/
 - Aucune librairie JS (tout codé manuellement)
 - Compilation SCSS via  sass --watch 
 
 ---
 
 ## Arborescence du projet
 
 PROJET-FINAL/
 ├── index.php
 ├── .htaccess
 ├──  README
 │
 │
 ├── asset/
 │   ├── css/
 │   │   ├── dist/
 │   │   └── scss/
 │   │       ├── base/
 │   │       └── pages/
 │   │ 
 │   ├── js/
 │   ├── images/
 │   ├── photos/
 │   └── font/
 │ 
 ├── controleur/
 ├── modele/
 ├── vue/
 │   ├── admin/
 │   ├── annonce/
 │   ├── commun/
 │   ├── message/
 │   └── utilisateur/
 
 
 
 ## Base de données
 
 Voici la structure minimale pour initialiser la base de données
 
 
 CREATE TABLE utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50),
    role ENUM('admin', 'utilisateur', 'visiteur') NOT NULL,
    date_inscription DATE NOT NULL,
    avatar VARCHAR(255)
 );
 
 ## Installation
 
 Cloner le repo :
 
 git clone https://github.com/Mathieu-VEL/Car-Legend.git
 
 
 
 ## Déploiement
 
 Le projet est également disponible en ligne à l'adresse suivante :
 
 https://stagiaires-kercode9.greta-bretagne-sud.org/mathieu-chaltane/projet-final/index.php?page=accueil
 
 