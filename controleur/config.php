<?php

// Chemin absolu de la racine du projet
// On définit une constante appelée 'RACINE' qui contient le chemin complet jusqu'au dossier racine du projet.
// Cela permet d'inclure facilement des fichiers sans se soucier de la profondeur des sous-dossiers.
define('RACINE', realpath(__DIR__ . '/..')); // __DIR__ = dossier actuel (controleur), '..' = dossier parent
