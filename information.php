<?php
/**
 * Message d'information affiché sur l apage d'accueil
 * Choisir la taille h1 à h6
 * Choisir le type de message:
 * 	class:	ok(vert), attention (jaune), critique (rouge)
 * indiquer le message à afficher
 */

$balise= "h3";
$balise2= "h3";
$class_message = "ok";
$class_message2 = "attention";
$message = "A compter du 1 Décembre 2019, les brouillons non modifiés supérieurs à deux mois seront automatiquement supprimés.";
$message2 = "Une mise à jour a été effectuée le 30 Octobre; pensez à vider le cache de votre navigateur si vous ne l'avez pas déjà fait.";
echo "<" . $balise . " class='" . $class_message . "'>" . $message . "</" . $balise . ">";
echo "<" . $balise2 . " class='" . $class_message2 . "'>" . $message2 . "</" . $balise2 . ">";
