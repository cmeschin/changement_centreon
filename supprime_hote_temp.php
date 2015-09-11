<?php
if (session_id()=='')
{
	session_start();
};
include('log.php'); // chargement de la fonction de log

// récupération de la ref demande
$ID_Demande= $_SESSION['ID_dem'];
//$ID_Demande= 7;
$nom_hote = (isset($_POST["nom_hote"])) ? $_POST["nom_hote"] : NULL;

addlog("Suppression hote: " . $nom_hote . "");
include_once('connexion_sql_supervision.php');
try {
	$del_hote = $bdd_supervision->prepare('DELETE
			 FROM hote_temp
			 WHERE Nom_Hote= :nom_hote
				 AND ID_Demande = :ID_Demande');
	$del_hote -> execute(Array(
			'nom_hote' => htmlspecialchars($nom_hote),
			'ID_Demande' => htmlspecialchars($ID_Demande)
	)) or die(print_r($del_hote->errorInfo()));
	
	addlog("Suppression hote: " . $nom_hote . " OK.");	
} catch (Exception $e) {
	die('Erreur supprime_hote_temp: ' . $e->getMessage());
};
