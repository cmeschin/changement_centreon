<?php
if (session_id()=='')
{
	session_start();
};
include('log.php'); // chargement de la fonction de log

// récupération de la ref demande
$ID_Demande= $_SESSION['ID_dem'];
//$ID_Demande= 7;
$nom_periode = (isset($_POST["nom_periode"])) ? $_POST["nom_periode"] : NULL;

addlog("Suppression periode: " . $nom_periode . "");
include_once('connexion_sql_supervision.php');
try {
	$del_periode = $bdd_supervision->prepare('DELETE
		 FROM periode_temporelle
		 WHERE Nom_Periode= :nom_periode
			 AND ID_Demande = :ID_Demande');
	$del_periode -> execute(Array(
			'nom_periode' => htmlspecialchars($nom_periode),
			'ID_Demande' => htmlspecialchars($ID_Demande)
	)) or die(print_r($del_periode->errorInfo()));

	addlog("Suppression periode: " . $nom_periode . " OK.");	
} catch (Exception $e) {
	die('Erreur supprime_periode: ' . $e->getMessage());
};
