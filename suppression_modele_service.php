<?php
session_start();
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include('log.php'); // chargement de la fonction de log
addlog("Chargement suppression_modele_service.php");

include_once('connexion_sql_supervision.php');
$sID_Modele_Service = (isset($_POST["ID_Modele_Service"])) ? $_POST["ID_Modele_Service"] : NULL;
//$ID_Modele_valeur = explode("$",$sID_Modele_valeur); // découpe la chaine en tableau avec comme séparateur le $

try {
	// Verification sur l'existence d'un nom identique
	$del_Modele = $bdd_supervision->prepare('DELETE
			 FROM modele_service
			 WHERE ID_Modele_Service = :ID_Modele_Service');
	$del_Modele->execute(Array(
			'ID_Modele_Service' => htmlspecialchars($sID_Modele_Service)
	)) or die(print_r($del_Modele->errorInfo()));
} catch (Exception $e) {
	die('Erreur suppression_modele_service: ' . $e->getMessage());
};

echo "Le modèle a bien été supprimé.";
