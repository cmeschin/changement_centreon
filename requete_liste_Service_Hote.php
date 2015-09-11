<?php
if (session_id()=='')
{
session_start();
};
// récupération de la ref demande
$ID_Demande= $_SESSION['ID_dem'];

//$req_Service_Hote = $bdd_supervision->prepare('SELECT DISTINCT(Nom_Hote), ID_Hote_Centreon, IP_Hote, ID_Localisation FROM hote_temp WHERE ID_Demande = :ID_Demande ORDER BY Nom_Hote');
$req_Service_Hote = $bdd_supervision->prepare('SELECT
		 DISTINCT(Nom_Hote),
		 ID_Hote_Centreon,
		 IP_Hote,
		 ID_Localisation,
		 ID_Hote
		 FROM hote
		 WHERE ID_Demande = :ID_Demande
		 ORDER BY Nom_Hote');
$req_Service_Hote -> execute(Array(
	'ID_Demande' => htmlspecialchars($ID_Demande)
	)) or die(print_r($req_Service_Hote->errorInfo()));
