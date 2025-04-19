<?php
session_start();

// Charger la config AVANT d'utiliser RACINE
require_once(__DIR__ . "/controleur/config.php");

// Charger le routeur
require_once(RACINE . "/controleur/routage.php");

// Exécuter le routage et inclure la page demandée
include(redirigeVers());


