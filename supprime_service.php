<?php
if (session_id()=='')
{
	session_start();
};
include('log.php'); // chargement de la fonction de log

// récupération de la ref demande
$ID_Demande= $_SESSION['ID_dem'];
//$ID_Demande= 7;
$nom_service = (isset($_POST["nom_service"])) ? $_POST["nom_service"] : NULL;
$service_hote = (isset($_POST["service_hote"])) ? $_POST["service_hote"] : NULL;

addlog("Suppression service: " . $nom_service . " de l'hôte " . $service_hote ."...");
include_once('connexion_sql_supervision.php');
try {
	$del_service = $bdd_supervision->prepare('DELETE
			 FROM service
			 WHERE Nom_Service= :nom_service
				 AND ID_Hote= :service_hote
				 AND ID_Demande = :ID_Demande');
	$del_service -> execute(Array(
			'nom_service' => htmlspecialchars($nom_service),
			'service_hote' => htmlspecialchars($service_hote),
			'ID_Demande' => htmlspecialchars($ID_Demande)
	)) or die(print_r($del_service->errorInfo()));
	
	addlog("Suppression service: " . $nom_service . " de l'hôte " . $service_hote ." OK.");
	
} catch (Exception $e) {
	die('Erreur supprime_service: ' . $e->getMessage());
};
