<?php
/**
 * Message d'information affiché sur l apage d'accueil
 * Choisir la taille h1 à h6
 * Choisir le type de message:
 * 	class:	ok(vert), attention (jaune), critique (rouge)
 * indiquer le message à afficher
 */

$balise= "h3";
$class_message = "attention";
$message = "A compter du 3 Octobre 2016, les brouillons supérieurs à trois mois seront automatiquement supprimés.";
echo "<" . $balise . " class='" . $class_message . "'>" . $message . "</" . $balise . ">";
