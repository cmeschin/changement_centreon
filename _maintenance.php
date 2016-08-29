<?php 
/**
 * Message de maintenance affiché sur la page d'accueil
 * Choisir la taille h1 à h6
 * Choisir le type de message:
 * 	class:	titre, ok(vert), attention (jaune), critique (rouge)
 * indiquer le message à afficher
 */

$balise= "h3";
$class_message = "titre";
$message = "Site actuellement en maintenance, veuillez revenir dans quelques instants.";

echo "<" . $balise . " class='" . $class_message . "'>" . $message . "</" . $balise . ">
			<p>Merci de votre compréhension. Pour toute information complémentaire, veuillez contacter l'administrateur.</p>";
