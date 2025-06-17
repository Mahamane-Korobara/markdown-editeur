<?php
session_start();
// Désactiver la mise en cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Afficher toutes les données reçues
echo "<h1>Test de réception des données</h1>";
echo "<h2>Données POST :</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Données SESSION :</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

die("Test terminé");
?>
